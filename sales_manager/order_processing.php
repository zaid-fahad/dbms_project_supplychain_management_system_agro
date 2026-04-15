<?php
// 1. Include the central database connection
include '../db.php'; 

/**
 * 2. AJAX HANDLER: Fetch Order Details for Modal
 */
if (isset($_GET['action']) && $_GET['action'] === 'get_order_details') {
    ob_clean();
    header('Content-Type: application/json');
    $orderId = intval($_GET['order_id']);
    $orderType = $_GET['order_type'];
    $response = ['success' => false, 'items' => [], 'details' => []];

    try {
        if ($orderType === 'shop') {
            // Fetch Super Shop Items
            $sql = "SELECT p.product_name, i.quantity, i.unit, i.unit_price, i.line_total 
                    FROM SuperShop_Order_Items i 
                    JOIN Products p ON i.product_id = p.product_id 
                    WHERE i.super_shop_order_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $orderId);
            $stmt->execute();
            $response['items'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            
            // Fetch Shop Metadata (Address/Notes)
            $sqlDet = "SELECT delivery_address, notes FROM SuperShop_Orders WHERE super_shop_order_id = ?";
            $stmtDet = $conn->prepare($sqlDet);
            $stmtDet->bind_param('i', $orderId);
            $stmtDet->execute();
            $response['details'] = $stmtDet->get_result()->fetch_assoc();
            $stmtDet->close();
        } else {
            // Fetch Market Order Items
            $sql = "SELECT p.product_name, i.quantity, i.unit, i.unit_price, (i.quantity * i.unit_price) as line_total 
                    FROM Order_Items i 
                    JOIN Products p ON i.product_id = p.product_id 
                    WHERE i.order_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $orderId);
            $stmt->execute();
            $response['items'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }
        $response['success'] = true;
    } catch (Exception $e) { 
        $response['error'] = $e->getMessage(); 
    }
    echo json_encode($response);
    exit;
}

/**
 * 3. ACTION HANDLER: Move Order to 'Processing'
 */
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['order_id'], $_POST['order_type'])) {
    if ($_POST['action'] === 'process_order') {
        $orderId = intval($_POST['order_id']);
        $orderType = $_POST['order_type'];
        try {
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
            $message = "Error updating order."; 
        }
    }
}

/**
 * 4. DATA FETCHING: Pending and History
 */
$pendingOrders = [];
$historyOrders = [];

try {
    $query = "SELECT 'market' as type, o.order_id as id, c.customer_name as name, o.total_amount as amt, o.status, o.order_date as dt
              FROM Orders o LEFT JOIN Customers c ON o.customer_id = c.customer_id
              UNION
              SELECT 'shop' as type, s.super_shop_order_id as id, s.customer_name as name, s.total_amount as amt, s.status, s.order_date as dt
              FROM SuperShop_Orders s
              ORDER BY dt DESC";
    
    $result = $conn->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if ($row['status'] === 'Pending') { 
                $pendingOrders[] = $row; 
            } else { 
                $historyOrders[] = $row; 
            }
        }
    }
} catch (Exception $e) { 
    $message = "Database error fetching orders."; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Management - Sales Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <style>
        /* Restored Modal Styling from Customer Management style */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%;
            max-width: 600px;
            border-radius: 8px;
        }
        .close {
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .item-table th, .item-table td {
            padding: 10px;
            border: 1px solid #eee;
            text-align: left;
        }
        /* Green Status Classes from original simple style */
        .status.pending { background-color: #ffeb3b; }
        .status.processing { background-color: #03a9f4; color: white; }
        .status.delivered { background-color: #4caf50; color: white; }
    </style>
</head>
<body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Order Management'; include '../components/header.html'; ?>
    <?php include 'components/nav.html'; ?>

    <main>
      <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>

      <form id="processForm" method="post" style="display:none;">
        <input type="hidden" name="action" value="process_order" />
        <input type="hidden" id="processOrderId" name="order_id" value="" />
        <input type="hidden" id="processOrderType" name="order_type" value="" />
      </form>

      <div class="card" style="margin-bottom: 30px;">
        <div class="card-header">
          <span class="card-title">Pending Orders</span>
        </div>
        <table>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Order Date</th>
            <th>Total</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
          <?php if (!empty($pendingOrders)): ?>
              <?php foreach ($pendingOrders as $row): 
                  $prefix = ($row['type'] === 'shop' ? 'SHOP' : 'ORD');
                  $statusClass = strtolower(str_replace(' ', '-', $row['status']));
              ?>
                <tr>
                  <td><?php echo "$prefix-{$row['id']}"; ?></td>
                  <td><?php echo htmlspecialchars($row['name']); ?></td>
                  <td><?php echo date('Y-m-d', strtotime($row['dt'])); ?></td>
                  <td><?php echo number_format($row['amt'], 0); ?> BDT</td>
                  <td><span class="status <?php echo $statusClass; ?>"><?php echo $row['status']; ?></span></td>
                  <td style="display: flex; gap: 5px;">
                    <button type="button" class="btn btn-info" onclick="openView(<?php echo $row['id']; ?>, '<?php echo $row['type']; ?>')">
                        <i class="fa fa-eye"></i> View
                    </button>
                    <button type="button" class="btn btn-success" onclick="submitProcessOrder(<?php echo $row['id']; ?>, '<?php echo $row['type']; ?>')">
                        <i class="fa fa-check"></i> Process
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6">No pending orders found</td></tr>
            <?php endif; ?>
        </table>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Order History</span>
        </div>
        <table>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Order Date</th>
            <th>Total</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
          <?php if (!empty($historyOrders)): ?>
              <?php foreach ($historyOrders as $row): 
                  $prefix = ($row['type'] === 'shop' ? 'SHOP' : 'ORD');
                  $statusClass = strtolower(str_replace(' ', '-', $row['status']));
              ?>
                <tr>
                  <td><?php echo "$prefix-{$row['id']}"; ?></td>
                  <td><?php echo htmlspecialchars($row['name']); ?></td>
                  <td><?php echo date('Y-m-d', strtotime($row['dt'])); ?></td>
                  <td><?php echo number_format($row['amt'], 0); ?> BDT</td>
                  <td><span class="status <?php echo $statusClass; ?>"><?php echo $row['status']; ?></span></td>
                  <td>
                    <button type="button" class="btn btn-info" onclick="openView(<?php echo $row['id']; ?>, '<?php echo $row['type']; ?>')">
                        <i class="fa fa-eye"></i> Details
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
        </table>
      </div>
    </main>

    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3 id="modalTitle">Order Items</h3>
            <hr>
            <div style="margin-bottom: 15px;">
                <p><strong>Address:</strong> <span id="modalAddr"></span></p>
                <p><strong>Notes:</strong> <span id="modalNotes"></span></p>
            </div>
            <table class="item-table">
                <thead>
                    <tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr>
                </thead>
                <tbody id="modalBody"></tbody>
            </table>
        </div>
    </div>

    <script>
      function openView(id, type) {
        fetch(`?action=get_order_details&order_id=${id}&order_type=${type}`)
          .then(res => res.json())
          .then(data => {
            if(data.success) {
              document.getElementById('modalTitle').innerText = `Details for ${type.toUpperCase()}-${id}`;
              document.getElementById('modalAddr').innerText = data.details?.delivery_address || 'N/A';
              document.getElementById('modalNotes').innerText = data.details?.notes || 'None';
              
              let rows = '';
              data.items.forEach(i => {
                rows += `<tr>
                    <td>${i.product_name}</td>
                    <td>${i.quantity} ${i.unit}</td>
                    <td>${i.unit_price}</td>
                    <td>${parseFloat(i.line_total).toFixed(2)} BDT</td>
                </tr>`;
              });
              document.getElementById('modalBody').innerHTML = rows;
              document.getElementById('viewModal').style.display = 'block';
            }
          });
      }

      function closeModal() { document.getElementById('viewModal').style.display = 'none'; }
      window.onclick = function(e) { if(e.target == document.getElementById('viewModal')) closeModal(); }

      function submitProcessOrder(id, type) {
        if (confirm(`Process order ${type.toUpperCase()}-${id}?`)) {
          document.getElementById('processOrderId').value = id;
          document.getElementById('processOrderType').value = type;
          document.getElementById('processForm').submit();
        }
      }
    </script>
</body>
</html>
<?php $conn->close(); ?>