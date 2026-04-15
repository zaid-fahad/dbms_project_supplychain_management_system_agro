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
      <div class="card">
        <div class="card-header">
          <span class="card-title">Shop Orders</span>
          <button class="btn btn-primary" onclick="newShopOrder()">
            <i class="fa fa-plus"></i> New Order
          </button>
        </div>
        <table>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Order Date</th>
            <th>Total</th>
            <th>Status</th>
          </tr>
          <?php
          if (!empty($shopOrders)) {
              foreach ($shopOrders as $row) {
                  $statusClass = strtolower(str_replace(' ', '-', $row['status']));
                  echo "<tr>
                          <td>SHOP-{$row['order_id']}</td>
                          <td>{$row['customer_name']}</td>
                          <td>" . date('Y-m-d', strtotime($row['order_date'])) . "</td>
                          <td>" . number_format($row['total_amount'], 0) . " BDT</td>
                          <td><span class='status {$statusClass}'>{$row['status']}</span></td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='5'>No shop orders found</td></tr>";
          }
          ?>
        </table>
      </div>
    </main>

    <script>
      function newShopOrder() {
        alert('Shop orders are managed through the main order workflow.');
      }
    </script>
  </body>
</html>