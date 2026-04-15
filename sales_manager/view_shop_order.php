<?php
include "../db.php";
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$order = null;
$message = '';

if ($order_id) {
    $sql = "SELECT o.order_id, c.customer_name, o.total_amount, o.status, o.order_date, c.customer_type 
            FROM SuperShop_Orders o 
            JOIN Customers c ON o.customer_id = c.customer_id 
            WHERE o.order_id = $order_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
    } else {
        $message = 'Order not found.';
    }
}

if (isset($_GET['delete_order_id']) && $order_id) {
    $delete_id = intval($_GET['delete_order_id']);
    if ($conn->query("DELETE FROM SuperShop_Orders WHERE order_id=$delete_id") === TRUE) {
        $message = 'Order deleted successfully.';
        header("refresh:2; url=./shop_orders.php");
    } else {
        $message = 'Error deleting order: ' . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>View Shop Order - Sales Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'View Shop Order'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <?php if ($message): ?>
        <div class="alert alert-success" role="alert"><?php echo $message; ?></div>
      <?php endif; ?>

      <?php if ($order): ?>
      <div class="card" style="max-width: 700px; margin: 0 auto">
        <div class="card-header">
          <span class="card-title">Shop Order Details - SHOP-<?php echo $order['order_id']; ?></span>
        </div>
        <div class="detail-section">
          <div class="detail-row">
            <span class="detail-label">Order ID:</span>
            <span class="detail-value">SHOP-<?php echo $order['order_id']; ?></span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Customer:</span>
            <span class="detail-value"><?php echo htmlspecialchars($order['customer_name']); ?></span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Customer Type:</span>
            <span class="detail-value"><?php echo htmlspecialchars($order['customer_type']); ?></span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Total Amount:</span>
            <span class="detail-value"><?php echo number_format($order['total_amount'], 0); ?> BDT</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Order Date:</span>
            <span class="detail-value"><?php echo date('Y-m-d', strtotime($order['order_date'])); ?></span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Status:</span>
            <span class="detail-value">
              <span class="status <?php echo strtolower(str_replace(' ', '-', $order['status'])); ?>">
                <?php echo $order['status']; ?>
              </span>
            </span>
          </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px; flex-wrap: wrap;">
          <a href="update_shop_order.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-warning">
            <i class="fa fa-edit"></i> Edit
          </a>
          <a href="view_shop_order.php?order_id=<?php echo $order['order_id']; ?>&delete_order_id=<?php echo $order['order_id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this order?');">
            <i class="fa fa-trash"></i> Delete
          </a>
          <a href="shop_orders.php" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Back
          </a>
        </div>
      </div>
      <?php else: ?>
      <div class="card" style="max-width: 700px; margin: 0 auto">
        <p><?php echo $message ?: 'No order selected.'; ?></p>
        <a href="shop_orders.php" class="btn btn-secondary">
          <i class="fa fa-arrow-left"></i> Back to Orders
        </a>
      </div>
      <?php endif; ?>
    </main>

    <style>
      .detail-section {
        padding: 20px;
      }
      .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
      }
      .detail-row:last-child {
        border-bottom: none;
      }
      .detail-label {
        font-weight: 600;
        color: #666;
        min-width: 150px;
      }
      .detail-value {
        color: #333;
        text-align: right;
        flex: 1;
      }
    </style>
  </body>
</html>
