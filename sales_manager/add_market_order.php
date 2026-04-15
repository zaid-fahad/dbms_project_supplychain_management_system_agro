<?php
include "../db.php";
$message = '';

if (isset($_POST['submit'])) {
    $customer_id = intval($_POST['customer_id']);
    $total_amount = floatval($_POST['total_amount']);
    $status = 'Pending';
    $order_date = date('Y-m-d');

    $sql = "INSERT INTO Orders (customer_id, total_amount, status, order_date) VALUES ($customer_id, $total_amount, '$status', '$order_date')";

    if ($conn->query($sql) === TRUE) {
        $message = 'Market order added successfully.';
        header("refresh:2; url=./market_orders.php");
    } else {
        $message = 'Error adding order: ' . $conn->error;
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Market Order - Sales Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Add Market Order'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card" style="max-width: 600px; margin: 0 auto">
        <div class="card-header">
          <span class="card-title">Add New Market Order</span>
        </div>
        <?php if ($message): ?>
          <div class="alert alert-success" role="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="" method="POST">
          <div class="form-group">
            <label for="customer_id">Local Market</label>
            <select id="customer_id" name="customer_id" required>
              <option value="">Select a local market customer</option>
              <?php
              include "../db.php";
              $sql = "SELECT customer_id, customer_name FROM Customers WHERE customer_type = 'Local Market' ORDER BY customer_name";
              $result = $conn->query($sql);
              if ($result) {
                  while ($row = $result->fetch_assoc()) {
                      echo '<option value="' . $row['customer_id'] . '">' . htmlspecialchars($row['customer_name']) . '</option>';
                  }
              }
              $conn->close();
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="total_amount">Total Amount (BDT)</label>
            <input type="number" id="total_amount" name="total_amount" placeholder="Enter total amount" min="0" step="0.01" required />
          </div>
          <button type="submit" name="submit" class="btn-submit">
            <i class="fa fa-check"></i> Add Market Order
          </button>
        </form>
      </div>
    </main>
  </body>
</html>
