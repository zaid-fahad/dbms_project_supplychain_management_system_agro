<?php
include "../db.php";
$message = '';
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$order = null;

if ($order_id) {
    $sql = "SELECT * FROM SuperShop_Orders WHERE order_id=$order_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
    } else {
        $message = 'Order not found.';
    }
}

if (isset($_POST['submit']) && $order_id) {
    $customer_id = intval($_POST['customer_id']);
    $total_amount = floatval($_POST['total_amount']);
    $status = $conn->real_escape_string($_POST['status']);

    $sql = "UPDATE SuperShop_Orders SET customer_id=$customer_id, total_amount=$total_amount, status='$status' WHERE order_id=$order_id";

    if ($conn->query($sql) === TRUE) {
        $message = 'Shop order updated successfully.';
        header("refresh:2; url=./shop_orders.php");
    } else {
        $message = 'Error updating order: ' . $conn->error;
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Update Shop Order - Sales Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Update Shop Order'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card" style="max-width: 600px; margin: 0 auto">
        <div class="card-header">
          <span class="card-title">Update Shop Order</span>
        </div>
        <?php if ($message): ?>
          <div class="alert alert-success" role="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($order): ?>
        <form action="" method="POST">
          <div class="form-group">
            <label for="order_id">Order ID</label>
            <input type="text" id="order_id" value="SHOP-<?php echo $order['order_id']; ?>" disabled />
          </div>
          <div class="form-group">
            <label for="customer_id">Super Shop</label>
            <select id="customer_id" name="customer_id" required>
              <option value="">Select a super shop customer</option>
              <?php
              include "../db.php";
              $sql = "SELECT customer_id, customer_name FROM Customers WHERE customer_type = 'Super Shop' ORDER BY customer_name";
              $result = $conn->query($sql);
              if ($result) {
                  while ($row = $result->fetch_assoc()) {
                      $selected = $row['customer_id'] == $order['customer_id'] ? ' selected' : '';
                      echo '<option value="' . $row['customer_id'] . '"' . $selected . '>' . htmlspecialchars($row['customer_name']) . '</option>';
                  }
              }
              $conn->close();
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="total_amount">Total Amount (BDT)</label>
            <input type="number" id="total_amount" name="total_amount" value="<?php echo $order['total_amount']; ?>" min="0" step="0.01" required />
          </div>
          <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" required>
              <option value="Pending" <?php echo $order['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
              <option value="Processing" <?php echo $order['status'] == 'Processing' ? 'selected' : ''; ?>>Processing</option>
              <option value="Verified" <?php echo $order['status'] == 'Verified' ? 'selected' : ''; ?>>Verified</option>
              <option value="Shipped" <?php echo $order['status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
              <option value="Delivered" <?php echo $order['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
            </select>
          </div>
          <button type="submit" name="submit" class="btn-submit">
            <i class="fa fa-check"></i> Update Shop Order
          </button>
        </form>
        <?php else: ?>
        <p>Order not found.</p>
        <?php endif; ?>
      </div>
    </main>
  </body>
</html>
