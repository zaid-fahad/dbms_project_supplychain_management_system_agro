<?php
include "../db.php";

function supershop_status_class($status) {
  if ($status === 'Delivered') {
    return 'completed';
  }

  if ($status === 'Processing' || $status === 'Shipped') {
    return 'in-transit';
  }

  return 'pending';
}

$orders = [];
$sql = "SELECT COALESCE(ord.order_id, o.super_shop_order_id) AS order_id, o.customer_name, o.delivery_address, DATE_FORMAT(o.delivery_date, '%Y-%m-%d') AS delivery_date, DATE_FORMAT(o.order_date, '%Y-%m-%d') AS order_date, COALESCE(ord.status, o.status) AS status, COALESCE(ord.total_amount, o.total_amount) AS total_amount, GROUP_CONCAT(CONCAT(p.product_name, ' x ', oi.quantity, ' ', oi.unit) ORDER BY oi.order_item_id SEPARATOR ', ') AS items FROM SuperShop_Orders o LEFT JOIN SuperShop_Order_Items oi ON o.super_shop_order_id = oi.super_shop_order_id LEFT JOIN Products p ON oi.product_id = p.product_id LEFT JOIN SuperShop_Order_Refs ref ON o.super_shop_order_id = ref.super_shop_order_id LEFT JOIN Orders ord ON ref.order_id = ord.order_id WHERE COALESCE(ord.status, o.status) = 'Delivered' GROUP BY o.super_shop_order_id, ord.order_id, o.customer_name, o.delivery_address, o.delivery_date, o.order_date, ord.status, o.status, ord.total_amount, o.total_amount ORDER BY o.order_date DESC";
$result = $conn->query($sql);
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order History - Super Shop</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Order History'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card">
        <div class="card-header">
          <span class="card-title">Order History</span>
        </div>
        <table>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Items</th>
            <th>Amount</th>
            <th>Order Date</th>
            <th>Delivery Date</th>
            <th>Status</th>
          </tr>
          <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $order): ?>
              <tr>
                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($order['items'] ?: 'N/A'); ?></td>
                <td><?php echo htmlspecialchars(number_format((float) $order['total_amount'], 2) . ' BDT'); ?></td>
                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                <td><?php echo htmlspecialchars($order['delivery_date'] ?: 'N/A'); ?></td>
                <td><span class="status <?php echo supershop_status_class($order['status']); ?>"><?php echo htmlspecialchars($order['status']); ?></span></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7">No delivered orders available.</td>
            </tr>
          <?php endif; ?>
        </table>
      </div>
    </main>
  </body>
</html>