<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shop Orders - Sales Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Shop Orders'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <?php
    $message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['order_id']) && $_POST['action'] === 'process_order') {
        $orderId = intval($_POST['order_id']);
        try {
            $conn = new mysqli('localhost', 'root', '', 'dbms_scms');
            if (!$conn->connect_error) {
                $stmt = $conn->prepare("UPDATE SuperShop_Orders SET status='Processing' WHERE order_id = ?");
                $stmt->bind_param('i', $orderId);
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    $message = "Order SHOP-$orderId has been moved to Processing.";
                } else {
                    $message = "Order SHOP-$orderId could not be updated.";
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

    $dbConnected = false;
    $shopOrders = [];

    try {
        $conn = new mysqli('localhost', 'root', '', 'dbms_scms');
        if (!$conn->connect_error) {
            $dbConnected = true;
            $sql = "SELECT o.order_id, c.customer_name, o.total_amount, o.status, o.order_date
                    FROM SuperShop_Orders o
                    JOIN Customers c ON o.customer_id = c.customer_id
                    WHERE c.customer_type = 'Super Shop'
                    ORDER BY o.order_date DESC";
            $result = $conn->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $shopOrders[] = $row;
                }
            }
            $conn->close();
        }
    } catch (Exception $e) {
        $dbConnected = false;
    }

    if (!$dbConnected) {
        $shopOrders = [
            ['order_id' => 201, 'customer_name' => 'Super Shop A', 'total_amount' => 15000, 'status' => 'Pending', 'order_date' => '2024-04-12'],
            ['order_id' => 202, 'customer_name' => 'Mega Mart B', 'total_amount' => 22000, 'status' => 'Processing', 'order_date' => '2024-04-11'],
            ['order_id' => 203, 'customer_name' => 'Retail Store C', 'total_amount' => 8500, 'status' => 'Shipped', 'order_date' => '2024-04-10']
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
      </form>
      <div class="card">
        <div class="card-header">
          <span class="card-title">Shop Orders</span>
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
          <?php
          if (!empty($shopOrders)) {
              foreach ($shopOrders as $row) {
                  $statusClass = strtolower(str_replace(' ', '-', $row['status']));
                  $processBtn = '';
                  if ($row['status'] === 'Pending') {
                      $processBtn = "<button type='button' class='btn btn-success' onclick='submitProcessOrder({$row['order_id']})'><i class='fa fa-check'></i> Process</button>";
                  }
                  echo "<tr>
                          <td>SHOP-{$row['order_id']}</td>
                          <td>{$row['customer_name']}</td>
                          <td>" . date('Y-m-d', strtotime($row['order_date'])) . "</td>
                          <td>" . number_format($row['total_amount'], 0) . " BDT</td>
                          <td><span class='status {$statusClass}'>{$row['status']}</span></td>
                          <td style='display: flex; gap: 5px;'>
                            <a href='view_shop_order.php?order_id={$row['order_id']}' class='btn btn-info'><i class='fa fa-eye'></i> View</a>
                            {$processBtn}
                          </td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='6'>No shop orders found</td></tr>";
          }
          ?>
        </table>
      </div>
    </main>

    <script>
      function submitProcessOrder(orderId) {
        if (confirm(`Process order SHOP-${orderId}?`)) {
          document.getElementById('processOrderId').value = orderId;
          document.getElementById('processForm').submit();
        }
      }
    </script>
  </body>
</html>