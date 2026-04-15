<?php
// 1. Central Database Connection
include '../db.php'; 

// Check connection
if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed. Check db.php settings.");
}

/**
 * 2. DOWNLOAD HANDLER (CSV Export)
 */
if (isset($_GET['download']) && $_GET['download'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="Agri_Sales_Report_' . date('Y-m-d') . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Product Name', 'Total Quantity', 'Total Revenue (BDT)']);
    
    $csvQuery = "SELECT p.product_name, SUM(combined.quantity) as qty, SUM(combined.revenue) as rev
                 FROM (
                    SELECT product_id, quantity, line_total as revenue FROM SuperShop_Order_Items
                    UNION ALL
                    SELECT product_id, quantity, (quantity * unit_price) as revenue FROM Order_Items
                 ) as combined
                 JOIN Products p ON combined.product_id = p.product_id
                 GROUP BY p.product_name ORDER BY rev DESC";
    $res = $conn->query($csvQuery);
    if ($res) {
        while($row = $res->fetch_assoc()) { fputcsv($output, $row); }
    }
    fclose($output);
    exit;
}

/**
 * 3. AJAX HANDLER: Fetch Product Details
 */
if (isset($_GET['action']) && $_GET['action'] === 'get_product_details') {
    ob_clean();
    header('Content-Type: application/json');
    $pName = $conn->real_escape_string($_GET['product_name']);
    $response = ['success' => false, 'orders' => []];

    try {
        $sql = "SELECT 'Shop' as source, sso.customer_name as customer, ssi.quantity, ssi.line_total as total, sso.order_date
                FROM SuperShop_Order_Items ssi
                JOIN Products p ON ssi.product_id = p.product_id
                JOIN SuperShop_Orders sso ON ssi.super_shop_order_id = sso.super_shop_order_id
                WHERE p.product_name = ?
                UNION ALL
                SELECT 'Market' as source, c.customer_name as customer, oi.quantity, (oi.quantity * oi.unit_price) as total, o.order_date
                FROM Order_Items oi
                JOIN Products p ON oi.product_id = p.product_id
                JOIN Orders o ON oi.order_id = o.order_id
                JOIN Customers c ON o.customer_id = c.customer_id
                WHERE p.product_name = ?
                ORDER BY order_date DESC LIMIT 10";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $pName, $pName);
        $stmt->execute();
        $response['orders'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $response['success'] = true;
    } catch (Exception $e) { $response['error'] = $e->getMessage(); }
    
    echo json_encode($response);
    exit;
}

// 4. Statistics & Data Fetching
$stats = ['sales' => 0, 'orders' => 0, 'customers' => 0, 'rate' => 0];
$topProducts = [];
$customerPerformance = [];

try {
    // Global Stats
    $summary = $conn->query("SELECT 
        (SELECT COUNT(*) FROM Orders) + (SELECT COUNT(*) FROM SuperShop_Orders) as t_o,
        (SELECT COUNT(*) FROM Orders WHERE status != 'Pending') + (SELECT COUNT(*) FROM SuperShop_Orders WHERE status != 'Pending') as c_o,
        (SELECT COALESCE(SUM(total_amount), 0) FROM Orders WHERE status != 'Pending') + (SELECT COALESCE(SUM(total_amount), 0) FROM SuperShop_Orders WHERE status != 'Pending') as t_s,
        (SELECT COUNT(DISTINCT customer_id) FROM Orders) + (SELECT COUNT(DISTINCT customer_name) FROM SuperShop_Orders) as t_c")->fetch_assoc();

    $stats['orders'] = $summary['t_o'];
    $stats['sales'] = $summary['t_s'];
    $stats['customers'] = $summary['t_c'];
    $stats['rate'] = ($summary['t_o'] > 0) ? round(($summary['c_o'] / $summary['t_o']) * 100, 1) : 0;

    // Top Products
    $resP = $conn->query("SELECT name, SUM(quantity) as qty, SUM(rev) as total_rev FROM (
                SELECT p.product_name as name, ssi.quantity, ssi.line_total as rev FROM SuperShop_Order_Items ssi JOIN Products p ON ssi.product_id = p.product_id
                UNION ALL
                SELECT p.product_name as name, oi.quantity, (oi.quantity * oi.unit_price) as rev FROM Order_Items oi JOIN Products p ON oi.product_id = p.product_id
            ) as items GROUP BY name ORDER BY total_rev DESC LIMIT 5");
    while($row = $resP->fetch_assoc()) { $topProducts[] = $row; }

    // Customer Performance
    $resC = $conn->query("SELECT name, SUM(c_count) as orders, SUM(total) as spent, MAX(last_act) as last_act FROM (
                SELECT c.customer_name as name, COUNT(o.order_id) as c_count, SUM(o.total_amount) as total, MAX(o.order_date) as last_act FROM Orders o JOIN Customers c ON o.customer_id = c.customer_id GROUP BY name
                UNION ALL
                SELECT customer_name as name, COUNT(super_shop_order_id) as c_count, SUM(total_amount) as total, MAX(order_date) as last_act FROM SuperShop_Orders GROUP BY name
            ) as buyers GROUP BY name ORDER BY spent DESC LIMIT 5");
    while($row = $resC->fetch_assoc()) { $customerPerformance[] = $row; }
} catch (Exception $e) { }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sales Reports - Sales Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <style>
        /* CSS Matched to your template */
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0, 0, 0, 0.4); }
        .modal-content { background-color: #fff; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 90%; max-width: 700px; border-radius: 8px; }
        .close { float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .item-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .item-table th, .item-table td { padding: 10px; border: 1px solid #eee; text-align: left; }
        .item-table th { background-color: #f9f9f9; color: #2e7d32; }
    </style>
</head>
<body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Sales Reports'; include '../components/header.html'; ?>
    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card" style="border:none; background:transparent; box-shadow:none;">
        <div class="card-header" style="border-radius:8px 8px 0 0;"><span class="card-title">Sales Insights</span></div>
        <div class="stats-grid">
          <div class="stat-card">
            <i class="fa fa-money"></i>
            <div class="value"><?php echo number_format($stats['sales'], 0); ?></div>
            <div class="label">Revenue (BDT)</div>
          </div>
          <div class="stat-card">
            <i class="fa fa-shopping-cart"></i>
            <div class="value"><?php echo $stats['orders']; ?></div>
            <div class="label">Total Orders</div>
          </div>
          <div class="stat-card">
            <i class="fa fa-users"></i>
            <div class="value"><?php echo $stats['customers']; ?></div>
            <div class="label">Active Buyers</div>
          </div>
          <div class="stat-card">
            <i class="fa fa-line-chart"></i>
            <div class="value"><?php echo $stats['rate']; ?>%</div>
            <div class="label">Success Rate</div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Top Selling Products</span>
          <a href="?download=csv" class="btn btn-primary">
            <i class="fa fa-download"></i> Export CSV
          </a>
        </div>
        <table>
          <tr>
            <th>Product Name</th>
            <th>Units Sold</th>
            <th>Revenue</th>
            <th>Market Share</th>
            <th>Details</th>
          </tr>
          <?php foreach($topProducts as $p): 
              $share = $stats['sales'] > 0 ? round(($p['total_rev'] / $stats['sales']) * 100, 1) : 0; ?>
              <tr>
                <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
                <td><?php echo number_format($p['qty'], 1); ?> kg</td>
                <td style="color:#2e7d32; font-weight:bold;"><?php echo number_format($p['total_rev'], 2); ?></td>
                <td><?php echo $share; ?>%</td>
                <td>
                    <button class="btn btn-warning btn-sm" onclick="openReportModal('<?php echo addslashes($p['name']); ?>')">
                        <i class="fa fa-search"></i>
                    </button>
                </td>
              </tr>
          <?php endforeach; ?>
        </table>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Customer Performance</span>
        </div>
        <table>
          <tr>
            <th>Customer</th>
            <th>Orders</th>
            <th>Total Value</th>
            <th>Status</th>
            <th>Last Active</th>
          </tr>
          <?php foreach($customerPerformance as $c): 
              $isInactive = (strtotime($c['last_act']) < strtotime('-90 days')); ?>
              <tr>
                <td><?php echo htmlspecialchars($c['name']); ?></td>
                <td><?php echo $c['orders']; ?></td>
                <td style="color:#2e7d32; font-weight:bold;"><?php echo number_format($c['spent'], 2); ?> BDT</td>
                <td><span class="status <?php echo $isInactive ? 'warning' : 'good'; ?>"><?php echo $isInactive ? 'Inactive' : 'Active'; ?></span></td>
                <td><?php echo date('Y-m-d', strtotime($c['last_act'])); ?></td>
              </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </main>

    <div id="reportModal" class="modal">
      <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle" style="color:#2e7d32;">Product Breakdown</h2>
        <hr>
        <table class="item-table">
            <thead>
                <tr><th>Source</th><th>Customer</th><th>Qty</th><th>Total</th><th>Date</th></tr>
            </thead>
            <tbody id="modalBody"></tbody>
        </table>
      </div>
    </div>

    <script>
      function openReportModal(productName) {
        fetch(`?action=get_product_details&product_name=${encodeURIComponent(productName)}`)
          .then(res => res.json())
          .then(data => {
            if(data.success) {
              document.getElementById('modalTitle').innerText = `Sales History: ${productName}`;
              let html = '';
              data.orders.forEach(o => {
                html += `<tr>
                  <td><span class="status ${o.source === 'Shop' ? 'good' : 'warning'}" style="font-size:10px;">${o.source}</span></td>
                  <td>${o.customer}</td>
                  <td>${o.quantity} kg</td>
                  <td>${parseFloat(o.total).toFixed(2)}</td>
                  <td>${o.order_date.split(' ')[0]}</td>
                </tr>`;
              });
              document.getElementById('modalBody').innerHTML = html;
              document.getElementById('reportModal').style.display = 'block';
            }
          });
      }

      function closeModal() { document.getElementById('reportModal').style.display = 'none'; }
      window.onclick = function(e) { if(e.target == document.getElementById('reportModal')) closeModal(); }
    </script>
</body>
</html>
<?php $conn->close(); ?>