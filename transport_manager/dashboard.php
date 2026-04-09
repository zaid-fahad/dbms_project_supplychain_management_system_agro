<?php
include "../db.php";

$total_pickups = 0;
$total_deliveries = 0;
$total_vehicles = 0;
$total_drivers = 0;

$pickup_result = $conn->query("SELECT COUNT(*) AS total FROM Orders WHERE status IN ('Pending', 'Processing')");
if ($pickup_result) {
    $total_pickups = $pickup_result->fetch_assoc()['total'];
}

$delivery_result = $conn->query("SELECT COUNT(*) AS total FROM Deliveries WHERE status IN ('Assigned', 'In Transit')");
if ($delivery_result) {
    $total_deliveries = $delivery_result->fetch_assoc()['total'];
}

$vehicle_result = $conn->query("SELECT COUNT(*) AS total FROM Vehicles");
if ($vehicle_result) {
    $total_vehicles = $vehicle_result->fetch_assoc()['total'];
}

$driver_result = $conn->query("SELECT COUNT(*) AS total FROM Users WHERE role = 'Driver'");
if ($driver_result) {
    $total_drivers = $driver_result->fetch_assoc()['total'];
}

$recent_sql = "SELECT d.delivery_id, o.order_id, d.status, d.pickup_time, d.delivery_time FROM Deliveries d LEFT JOIN Orders o ON d.order_id=o.order_id ORDER BY d.pickup_time DESC LIMIT 5";
$recent_result = $conn->query($recent_sql);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Transport Manager Dashboard</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Transport Manager Dashboard'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="stats-grid">
        <div class="stat-card">
          <i class="fa fa-truck"></i>
          <div class="value"><?php echo $total_pickups; ?></div>
          <div class="label">Pending Pickups</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-send"></i>
          <div class="value"><?php echo $total_deliveries; ?></div>
          <div class="label">In Progress Deliveries</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-car"></i>
          <div class="value"><?php echo $total_vehicles; ?></div>
          <div class="label">Total Vehicles</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-user"></i>
          <div class="value"><?php echo $total_drivers; ?></div>
          <div class="label">Active Drivers</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="fa fa-arrow-right"></i> Quick Actions</span>
        </div>
        <div class="quick-actions">
          <a href="pickup_requests.php" class="action-btn">
            <i class="fa fa-truck"></i>
            <span>Pickup Requests</span>
          </a>
          <a href="delivery_orders.php" class="action-btn">
            <i class="fa fa-send"></i>
            <span>Delivery Orders</span>
          </a>
          <a href="fleet_management.php" class="action-btn">
            <i class="fa fa-car"></i>
            <span>Fleet Management</span>
          </a>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="fa fa-history"></i> Recent Deliveries</span>
        </div>
        <table>
          <tr>
            <th><i class="fa fa-hashtag"></i> Delivery ID</th>
            <th><i class="fa fa-shopping-cart"></i> Order ID</th>
            <th><i class="fa fa-info-circle"></i> Status</th>
            <th><i class="fa fa-clock-o"></i> Pickup Time</th>
            <th><i class="fa fa-flag-checkered"></i> Delivery Time</th>
          </tr>
          <?php if ($recent_result && $recent_result->num_rows > 0) {
            while ($row = $recent_result->fetch_assoc()) {
              $status = $row['status'];
              $status_class = ($status == 'Completed') ? 'completed' : (($status == 'In Transit') ? 'in-transit' : 'pending');
          ?>
          <tr>
            <td><?php echo $row['delivery_id']; ?></td>
            <td><?php echo $row['order_id'] ?? 'N/A'; ?></td>
            <td><span class="status <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
            <td><?php echo $row['pickup_time'] ? date('Y-m-d H:i', strtotime($row['pickup_time'])) : 'Pending'; ?></td>
            <td><?php echo $row['delivery_time'] ? date('Y-m-d H:i', strtotime($row['delivery_time'])) : 'Not yet'; ?></td>
          </tr>
          <?php }
          } else {
            echo '<tr><td colspan="5" style="text-align:center; padding:20px;">No deliveries found.</td></tr>';
          } ?>
        </table>
      </div>
    </main>
  </body>
</html>
      </div>
    </main>
  </body>
</html>

