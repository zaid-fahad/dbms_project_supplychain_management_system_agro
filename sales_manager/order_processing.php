<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Process Orders - Sales Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Order Processing'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <?php
    $dbConnected = false;
    $pendingOrders = [];
    $processedOrders = [];
    $message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['order_id'], $_POST['order_type']) && $_POST['action'] === 'process_order') {
        $orderId = intval($_POST['order_id']);
        $orderType = $_POST['order_type'];
        $table = $orderType === 'shop' ? 'SuperShop_Orders' : 'Orders';
        try {
            $conn = new mysqli('localhost', 'root', '', 'dbms_scms');
            if (!$conn->connect_error) {
                $stmt = $conn->prepare("UPDATE $table SET status='Processing' WHERE order_id = ?");
                $stmt->bind_param('i', $orderId);
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    $message = "Order " . ($orderType === 'shop' ? 'SHOP' : 'ORD') . "-$orderId has been moved to Processing.";
                } else {
                    $message = "Order " . ($orderType === 'shop' ? 'SHOP' : 'ORD') . "-$orderId could not be updated.";
                }
                $stmt->close();
                $conn->close();
            } else {
                $message = 'Database connection failed while processing order.';
            }
        } catch (Exception $e) {
            $message = 'Unable to update order due to database error.';
        }
    }

    try {
        $conn = new mysqli('localhost', 'root', '', 'dbms_scms');
        if (!$conn->connect_error) {
            $dbConnected = true;

            $sql = "(SELECT 'market' as order_type, o.order_id, c.customer_name, o.total_amount, o.status, o.order_date
                    FROM Orders o
                    LEFT JOIN Customers c ON o.customer_id = c.customer_id
                    WHERE o.status = 'Pending')
                    UNION
                    (SELECT 'shop' as order_type, o.order_id, c.customer_name, o.total_amount, o.status, o.order_date
                    FROM SuperShop_Orders o
                    LEFT JOIN Customers c ON o.customer_id = c.customer_id
                    WHERE o.status = 'Pending')
                    ORDER BY order_date DESC";
            $pendingResult = $conn->query($sql);

            $sql = "(SELECT 'market' as order_type, o.order_id, c.customer_name, o.total_amount, o.status, o.order_date
                    FROM Orders o
                    LEFT JOIN Customers c ON o.customer_id = c.customer_id
                    WHERE o.status IN ('Verified', 'Processing', 'Shipped', 'Delivered'))
                    UNION
                    (SELECT 'shop' as order_type, o.order_id, c.customer_name, o.total_amount, o.status, o.order_date
                    FROM SuperShop_Orders o
                    LEFT JOIN Customers c ON o.customer_id = c.customer_id
                    WHERE o.status IN ('Verified', 'Processing', 'Shipped', 'Delivered'))
                    ORDER BY order_date DESC LIMIT 5";
            $processedResult = $conn->query($sql);

            if ($pendingResult) {
                while ($row = $pendingResult->fetch_assoc()) {
                    $pendingOrders[] = $row;
                }
            }

            if ($processedResult) {
                while ($row = $processedResult->fetch_assoc()) {
                    $processedOrders[] = $row;
                }
            }

            $conn->close();
        }
    } catch (Exception $e) {
        $dbConnected = false;
    }

    if (!$dbConnected) {
        $pendingOrders = [
            ['order_type' => 'market', 'order_id' => 1, 'customer_name' => 'Local Market B', 'total_amount' => 7500, 'order_date' => '2024-01-14', 'status' => 'Pending'],
            ['order_type' => 'shop', 'order_id' => 2, 'customer_name' => 'Super Shop A', 'total_amount' => 10000, 'order_date' => '2024-01-15', 'status' => 'Pending'],
            ['order_type' => 'market', 'order_id' => 3, 'customer_name' => 'Restaurant C', 'total_amount' => 5000, 'order_date' => '2024-01-13', 'status' => 'Pending']
        ];

        $processedOrders = [
            ['order_type' => 'shop', 'order_id' => 4, 'customer_name' => 'Wholesale D', 'total_amount' => 25000, 'order_date' => '2024-01-12', 'status' => 'Processing'],
            ['order_type' => 'market', 'order_id' => 5, 'customer_name' => 'Super Shop A', 'total_amount' => 15000, 'order_date' => '2024-01-11', 'status' => 'Delivered']
        ];
    }
    ?>

    <main>
      <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>

      <form id="processForm" method="post" style="display:none;">
        <input type="hidden" name="action" value="process_order" />
        <input type="hidden" id="processOrderId" name="order_id" value="" />
        <input type="hidden" id="processOrderType" name="order_type" value="" />
      </form>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Pending Orders</span>
        </div>
        <table>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Order Date</th>
            <th>Action</th>
          </tr>
          <?php
          if (!empty($pendingOrders)) {
              foreach($pendingOrders as $order) {
                  $statusClass = strtolower(str_replace(' ', '-', $order['status']));
                  $prefix = $order['order_type'] === 'shop' ? 'SHOP' : 'ORD';
                  echo "<tr>
                          <td>{$prefix}-{$order['order_id']}</td>
                          <td>{$order['customer_name']}</td>
                          <td>" . number_format($order['total_amount'], 0) . " BDT</td>
                          <td><span class='status {$statusClass}'>{$order['status']}</span></td>
                          <td>" . date('Y-m-d', strtotime($order['order_date'])) . "</td>
                          <td>
                            <button type='button' class='btn btn-success' onclick='submitProcessOrder({$order['order_id']}, \"{$order['order_type']}\")'>
                              <i class='fa fa-check'></i> Process
                            </button>
                          </td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='6'>No pending orders found</td></tr>";
          }
          ?>
        </table>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Recent Processed Orders</span>
        </div>
        <table>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Processed Date</th>
          </tr>
          <?php
          if (!empty($processedOrders)) {
              foreach($processedOrders as $order) {
                  $statusClass = strtolower(str_replace(' ', '-', $order['status']));
                  $prefix = $order['order_type'] === 'shop' ? 'SHOP' : 'ORD';
                  echo "<tr>
                          <td>{$prefix}-{$order['order_id']}</td>
                          <td>{$order['customer_name']}</td>
                          <td>" . number_format($order['total_amount'], 0) . " BDT</td>
                          <td><span class='status {$statusClass}'>{$order['status']}</span></td>
                          <td>" . date('Y-m-d', strtotime($order['order_date'])) . "</td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='5'>No processed orders found</td></tr>";
          }
          ?>
        </table>
      </div>
    </main>

    <script>
      function submitProcessOrder(orderId, orderType) {
        if (confirm(`Process order ${orderType === 'shop' ? 'SHOP' : 'ORD'}-${orderId}?`)) {
          document.getElementById('processOrderId').value = orderId;
          document.getElementById('processOrderType').value = orderType;
          document.getElementById('processForm').submit();
        }