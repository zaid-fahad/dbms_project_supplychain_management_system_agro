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

$total_orders = 0;
$delivered_orders = 0;
$in_transit_orders = 0;
$total_spent = 0.00;
$recent_orders = [];

$result = $conn->query("SELECT COUNT(*) AS total FROM SuperShop_Orders");
if ($result) {
  $total_orders = (int) ($result->fetch_assoc()['total'] ?? 0);
}

$result = $conn->query("SELECT COUNT(*) AS total FROM SuperShop_Orders sso LEFT JOIN SuperShop_Order_Refs sor ON sso.super_shop_order_id = sor.super_shop_order_id LEFT JOIN Orders o ON sor.order_id = o.order_id WHERE COALESCE(o.status, sso.status) = 'Delivered'");
if ($result) {
  $delivered_orders = (int) ($result->fetch_assoc()['total'] ?? 0);
}

$result = $conn->query("SELECT COUNT(*) AS total FROM SuperShop_Orders sso LEFT JOIN SuperShop_Order_Refs sor ON sso.super_shop_order_id = sor.super_shop_order_id LEFT JOIN Orders o ON sor.order_id = o.order_id WHERE COALESCE(o.status, sso.status) IN ('Processing', 'Shipped')");
if ($result) {
  $in_transit_orders = (int) ($result->fetch_assoc()['total'] ?? 0);
}

$result = $conn->query("SELECT COALESCE(SUM(COALESCE(o.total_amount, sso.total_amount)), 0) AS total FROM SuperShop_Orders sso LEFT JOIN SuperShop_Order_Refs sor ON sso.super_shop_order_id = sor.super_shop_order_id LEFT JOIN Orders o ON sor.order_id = o.order_id");
if ($result) {
  $total_spent = (float) ($result->fetch_assoc()['total'] ?? 0);
}

$recent_sql = "SELECT COALESCE(ord.order_id, o.super_shop_order_id) AS order_id, o.customer_name, o.delivery_address, DATE_FORMAT(o.delivery_date, '%Y-%m-%d') AS delivery_date, DATE_FORMAT(o.order_date, '%Y-%m-%d') AS order_date, COALESCE(ord.status, o.status) AS status, COALESCE(ord.total_amount, o.total_amount) AS total_amount, GROUP_CONCAT(CONCAT(p.product_name, ' x ', oi.quantity, ' ', oi.unit) ORDER BY oi.order_item_id SEPARATOR ', ') AS items FROM SuperShop_Orders o LEFT JOIN SuperShop_Order_Items oi ON o.super_shop_order_id = oi.super_shop_order_id LEFT JOIN Products p ON oi.product_id = p.product_id LEFT JOIN SuperShop_Order_Refs ref ON o.super_shop_order_id = ref.super_shop_order_id LEFT JOIN Orders ord ON ref.order_id = ord.order_id GROUP BY o.super_shop_order_id, ord.order_id, o.customer_name, o.delivery_address, o.delivery_date, o.order_date, ord.status, o.status, ord.total_amount, o.total_amount ORDER BY o.order_date DESC LIMIT 5";
$recent_result = $conn->query($recent_sql);
if ($recent_result) {
  while ($row = $recent_result->fetch_assoc()) {
    $recent_orders[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Super Shop Dashboard</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Super Shop Dashboard'; include '../components/header.html'; ?>

    <main>
      <div class="stats-grid">
        <div class="stat-card">
          <i class="fa fa-shopping-bag"></i>
          <div class="value"><?php echo $total_orders; ?></div>
          <div class="label">Total Orders</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-check-circle"></i>
          <div class="value"><?php echo $delivered_orders; ?></div>
          <div class="label">Delivered</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-clock-o"></i>
          <div class="value"><?php echo $in_transit_orders; ?></div>
          <div class="label">In Transit</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-money"></i>
          <div class="value"><?php echo number_format($total_spent, 2); ?></div>
          <div class="label">Total Spent (BDT)</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Quick Actions</span>
        </div>
        <div class="quick-actions">
          <a href="place_order.php" class="action-btn">
            <i class="fa fa-cart-plus"></i>
            <span>Place Order</span>
          </a>
          <a href="order_status.php" class="action-btn">
            <i class="fa fa-list-alt"></i>
            <span>Order Status</span>
          </a>
          <a href="order_history.php" class="action-btn">
            <i class="fa fa-history"></i>
            <span>Order History</span>
          </a>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Recent Orders</span>
        </div>
        <table>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Items</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
          <?php if (!empty($recent_orders)): ?>
            <?php foreach ($recent_orders as $order): ?>
              <tr>
                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($order['items'] ?: 'N/A'); ?></td>
                <td><?php echo htmlspecialchars(number_format((float) $order['total_amount'], 2) . ' BDT'); ?></td>
                <td><span class="status <?php echo supershop_status_class($order['status']); ?>"><?php echo htmlspecialchars($order['status']); ?></span></td>
                <td>
                  <button
                    class="btn btn-info"
                    onclick="viewOrder(<?php echo json_encode((string) $order['order_id']); ?>, <?php echo json_encode($order['customer_name']); ?>, <?php echo json_encode($order['items'] ?: 'N/A'); ?>, <?php echo json_encode(number_format((float) $order['total_amount'], 2) . ' BDT'); ?>, <?php echo json_encode($order['order_date']); ?>, <?php echo json_encode($order['delivery_date'] ?: 'N/A'); ?>, <?php echo json_encode($order['status']); ?>, <?php echo json_encode($order['delivery_address']); ?>)"
                  >
                    View
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6">No orders available.</td>
            </tr>
          <?php endif; ?>
        </table>
      </div>
    </main>

    <div class="modal" id="detailsModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Order Details</h3>
          <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div id="modalBody"></div>
      </div>
    </div>

    <script>
      function viewOrder(orderId, customer, items, amount, orderDate, deliveryDate, status, address) {
        document.getElementById("modalBody").innerHTML = `
                <div class="detail-row">
                    <span class="detail-label">Order ID:</span>
                    <span class="detail-value">${orderId}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Customer:</span>
                    <span class="detail-value">${customer}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Items:</span>
                    <span class="detail-value">${items}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount:</span>
                    <span class="detail-value">${amount}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Order Date:</span>
                    <span class="detail-value">${orderDate}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Delivery Date:</span>
                    <span class="detail-value">${deliveryDate}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Delivery Address:</span>
                    <span class="detail-value">${address || 'N/A'}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value"><span class="status ${
                      status === "Delivered"
                        ? "completed"
                        : status === "Processing" || status === "Shipped"
                        ? "in-transit"
                        : "pending"
                    }">${status}</span></span>
                </div>
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <a href="place_order.php" class="btn btn-primary">Reorder</a>
                    <button class="btn btn-danger" onclick="closeModal()">Close</button>
                </div>
            `;
        document.getElementById("detailsModal").classList.add("active");
      }

      function closeModal() {
        document.getElementById("detailsModal").classList.remove("active");
      }
    </script>
  </body>
</html>
