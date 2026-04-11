<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Market Orders - Sales Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Market Orders'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <?php
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'dbms_scms');
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    // Fetch market orders
    $sql = "SELECT * FROM Market_Orders ORDER BY order_date DESC";
    $result = $conn->query($sql);
    ?>

    <main>
      <div class="card">
        <div class="card-header">
          <span class="card-title">Market Orders</span>
          <button class="btn btn-primary">
            <i class="fa fa-plus"></i> New Order
          </button>
        </div>
        <table>
          <tr>
            <th>Order ID</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price/kg</th>
            <th>Total</th>
            <th>Status</th>
          </tr>
          <?php
          if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                  $statusClass = strtolower(str_replace(' ', '-', $row['status']));
                  echo "<tr>
                          <td>{$row['order_id']}</td>
                          <td>{$row['product_name']}</td>
                          <td>{$row['quantity']} kg</td>
                          <td>{$row['price_per_kg']} BDT</td>
                          <td>{$row['total_amount']} BDT</td>
                          <td><span class='status {$statusClass}'>{$row['status']}</span></td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='6'>No market orders found</td></tr>";
          }
          ?>
        </table>
      </div>
    </main>

    <?php $conn->close(); ?>

    <script>
      function viewOrder(id, product, quantity, price, status) {
        alert(`Order ${id}: ${product} - ${quantity} at ${price} (${status})`);
      }
    </script>
  </body>
</html>