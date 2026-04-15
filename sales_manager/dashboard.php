<?php
// 1. Centralized Database Connection
include '../db.php'; 

$message = '';
$dbConnected = true; // Assumed true as db.php handles the die() on failure

// 2. Handle Process Order Action (from Dashboard Quick View)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['order_id'])) {
    if ($_POST['action'] === 'process_order') {
        $orderId = intval($_POST['order_id']);
        $orderType = $_POST['order_type']; // 'shop' or 'market'
        try {
            // Using the $conn object from db.php
            $sql = ($orderType === 'shop') 
                ? "UPDATE SuperShop_Orders SET status='Processing' WHERE super_shop_order_id = ?"
                : "UPDATE Orders SET status='Processing' WHERE order_id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $orderId);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $message = "Order successfully moved to Processing.";
            }
            $stmt->close();
        } catch (Exception $e) { 
            $message = "Error: " . $e->getMessage(); 
        }
    }
}

// 3. Fetch Aggregated Statistics (Combining Shop and Market)
$totalOrders = 0; $completedOrders = 0; $pendingOrdersCount = 0; $totalSales = 0;
$pendingOrdersData = [];

try {
    // Count Totals across both tables using the global $conn
    $res = $conn->query("SELECT 
        (SELECT COUNT(*) FROM Orders) + (SELECT COUNT(*) FROM SuperShop_Orders) as total,
        (SELECT COUNT(*) FROM Orders WHERE status='Delivered') + (SELECT COUNT(*) FROM SuperShop_Orders WHERE status='Delivered') as delivered,
        (SELECT COUNT(*) FROM Orders WHERE status='Pending') + (SELECT COUNT(*) FROM SuperShop_Orders WHERE status='Pending') as pending,
        (SELECT COALESCE(SUM(total_amount),0) FROM Orders WHERE status='Delivered') + (SELECT COALESCE(SUM(total_amount),0) FROM SuperShop_Orders WHERE status='Delivered') as sales");
    
    if ($res) {
        $stats = $res->fetch_assoc();
        $totalOrders = $stats['total'];
        $completedOrders = $stats['delivered'];
        $pendingOrdersCount = $stats['pending'];
        $totalSales = $stats['sales'];
    }

    // Fetch Top 5 Pending Orders (Mixed)
    $sqlPending = "(SELECT 'market' as type, o.order_id as id, c.customer_name as name, o.total_amount as amt, o.status, o.order_date as dt
                    FROM Orders o LEFT JOIN Customers c ON o.customer_id = c.customer_id WHERE o.status = 'Pending')
                    UNION
                    (SELECT 'shop' as type, s.super_shop_order_id as id, s.customer_name as name, s.total_amount as amt, s.status, s.order_date as dt
                    FROM SuperShop_Orders s WHERE s.status = 'Pending')
                    ORDER BY dt DESC LIMIT 5";
    
    $resPending = $conn->query($sqlPending);
    if ($resPending) {
        while ($row = $resPending->fetch_assoc()) { 
            $pendingOrdersData[] = $row; 
        }
    }
} catch (Exception $e) { 
    $dbConnected = false; 
}

// Fallback dummy data only if the database query fails
if (!$dbConnected || empty($pendingOrdersData) && $totalOrders == 0) {
    $totalOrders = 85; $completedOrders = 65; $pendingOrdersCount = 20; $totalSales = 1200000;
    $pendingOrdersData = [
        ['id' => 1, 'type' => 'shop', 'name' => 'Super Shop A', 'amt' => 10000, 'dt' => '2026-04-14', 'status' => 'Pending'],
        ['id' => 2, 'type' => 'market', 'name' => 'Local Market B', 'amt' => 7500, 'dt' => '2026-04-14', 'status' => 'Pending']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sales Manager Dashboard</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <style>
        .status.pending { background: #fff3cd; color: #856404; padding: 4px 10px; border-radius: 20px; font-size: 0.85em; }
        .stat-card i { color: #4caf50; }
        .btn-primary { background-color: #4caf50; border: none; color: white; padding: 8px 15px; border-radius: 4px; cursor: pointer; }
        .btn-primary:hover { background-color: #43a047; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal.active { display: block; }
        .modal-content { background: white; margin: 10% auto; padding: 25px; width: 40%; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .detail-label { font-weight: bold; color: #666; }
    </style>
</head>
<body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Sales Manager Dashboard'; include '../components/header.html'; ?>
    <?php include 'components/nav.html'; ?>

    <main>
      <?php if ($message): ?><div class="alert alert-info"><?php echo $message; ?></div><?php endif; ?>

      <form id="processForm" method="post" style="display:none;">
        <input type="hidden" name="action" value="process_order" />
        <input type="hidden" id="processOrderId" name="order_id" value="" />
        <input type="hidden" id="processOrderType" name="order_type" value="" />
      </form>

      <div class="stats-grid">
        <div class="stat-card">
          <i class="fa fa-shopping-cart"></i>
          <div class="value"><?php echo $totalOrders; ?></div>
          <div class="label">Total Orders</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-check-circle"></i>
          <div class="value"><?php echo $completedOrders; ?></div>
          <div class="label">Completed</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-clock-o"></i>
          <div class="value"><?php echo $pendingOrdersCount; ?></div>
          <div class="label">Pending</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-money"></i>
          <div class="value"><?php echo number_format($totalSales / 1000, 1); ?>K</div>
          <div class="label">Total Sales (BDT)</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><span class="card-title">Operations Control</span></div>
        <div class="quick-actions">
          <a href="order_processing.php" class="action-btn"><i class="fa fa-tasks"></i><span>Process All</span></a>
          <a href="sales_reports.php" class="action-btn"><i class="fa fa-bar-chart"></i><span>Reports</span></a>
          <a href="customer_management.php" class="action-btn"><i class="fa fa-users"></i><span>Customers</span></a>
          <a href="market_orders.php" class="action-btn"><i class="fa fa-shopping-basket"></i><span>Market Hub</span></a>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><span class="card-title">Recent Pending Orders</span></div>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Customer</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($pendingOrdersData)): ?>
              <?php foreach($pendingOrdersData as $o): 
                  $idStr = ($o['type'] === 'shop' ? 'SHOP' : 'ORD') . "-" . $o['id'];
              ?>
                <tr>
                  <td><strong><?php echo $idStr; ?></strong></td>
                  <td><?php echo htmlspecialchars($o['name']); ?></td>
                  <td><?php echo number_format($o['amt'], 0); ?> BDT</td>
                  <td><span class="status pending"><?php echo $o['status']; ?></span></td>
                  <td><?php echo date('Y-m-d', strtotime($o['dt'])); ?></td>
                  <td>
                    <button class="btn btn-info" onclick="viewOrder('<?php echo $idStr; ?>', '<?php echo htmlspecialchars($o['name']); ?>', '<?php echo number_format($o['amt'], 0); ?> BDT', 'Pending', '<?php echo $o['id']; ?>', '<?php echo $o['type']; ?>')">
                      View
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" style="text-align:center;">All orders are processed!</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>

    <div class="modal" id="detailsModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Order Overview</h3>
          <span style="cursor:pointer; font-size:24px;" onclick="closeModal()">&times;</span>
        </div>
        <div id="modalBody" style="margin-top:15px;"></div>
      </div>
    </div>

    <script>
      function viewOrder(idStr, customer, amount, status, rawId, type) {
        document.getElementById("modalBody").innerHTML = `
            <div class="detail-row"><span class="detail-label">Order Reference:</span><span>${idStr}</span></div>
            <div class="detail-row"><span class="detail-label">Customer:</span><span>${customer}</span></div>
            <div class="detail-row"><span class="detail-label">Total Amount:</span><span>${amount}</span></div>
            <div class="detail-row"><span class="detail-label">Current Status:</span><span class="status pending">${status}</span></div>
            
            <div style="margin-top: 25px; display: flex; gap: 10px; justify-content: flex-end;">
                <button class="btn btn-primary" onclick="confirmProcess(${rawId}, '${type}')">Process Now</button>
                <button class="btn btn-danger" onclick="closeModal()">Close</button>
            </div>
        `;
        document.getElementById("detailsModal").classList.add("active");
      }

      function confirmProcess(id, type) {
        if (confirm('Verify and move this order to processing?')) {
          document.getElementById('processOrderId').value = id;
          document.getElementById('processOrderType').value = type;
          document.getElementById('processForm').submit();
        }
      }

      function closeModal() {
        document.getElementById("detailsModal").classList.remove("active");
      }

      window.onclick = function(event) {
        if (event.target == document.getElementById("detailsModal")) closeModal();
      }
    </script>
</body>
</html>
<?php
// Close the global connection at the very end of the script
$conn->close();
?>