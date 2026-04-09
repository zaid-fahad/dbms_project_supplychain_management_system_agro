<?php
include "../db.php";

$vehicles = $conn->query("SELECT v.vehicle_id, v.license_plate, v.vehicle_type, COUNT(d.delivery_id) AS active_deliveries 
    FROM Vehicles v 
    LEFT JOIN Deliveries d ON v.vehicle_id=d.vehicle_id AND d.status != 'Completed'
    GROUP BY v.vehicle_id ORDER BY v.license_plate");

$drivers = $conn->query("SELECT u.user_id, u.full_name, u.phone, COUNT(d.delivery_id) AS active_deliveries 
    FROM Users u 
    LEFT JOIN Deliveries d ON u.user_id=d.driver_id AND d.status != 'Completed'
    WHERE u.role = 'Driver'
    GROUP BY u.user_id ORDER BY u.full_name");
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Fleet Management - Transport Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Fleet Management'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="fa fa-cogs"></i> Fleet Actions</span>
        </div>
        <div class="card-body" style="display: flex; gap: 1rem; flex-wrap: wrap;">
          <a href="vehicle_list.php" class="btn btn-primary"><i class="fa fa-car"></i> Manage Vehicles</a>
          <a href="driver_list.php" class="btn btn-primary"><i class="fa fa-user"></i> Manage Drivers</a>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="fa fa-car"></i> Vehicle Fleet</span>
        </div>
        <table>
          <tr>
            <th><i class="fa fa-hashtag"></i> Vehicle ID</th>
            <th><i class="fa fa-car"></i> Type</th>
            <th><i class="fa fa-id-card"></i> License Plate</th>
            <th><i class="fa fa-tasks"></i> Active Deliveries</th>
            <th>Status</th>
          </tr>
          <?php if ($vehicles && $vehicles->num_rows > 0) {
            while ($row = $vehicles->fetch_assoc()) {
              $status = $row['active_deliveries'] > 0 ? 'In Use' : 'Available';
              $status_class = $status == 'In Use' ? 'in-transit' : 'pending';
          ?>
          <tr>
            <td><?php echo $row['vehicle_id']; ?></td>
            <td><?php echo htmlspecialchars($row['vehicle_type']); ?></td>
            <td><strong><?php echo htmlspecialchars($row['license_plate']); ?></strong></td>
            <td><?php echo $row['active_deliveries']; ?></td>
            <td><span class="status <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
          </tr>
          <?php }
          } else {
            echo '<tr><td colspan="5" style="text-align:center; padding:20px;">No vehicles found.</td></tr>';
          } ?>
        </table>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="fa fa-users"></i> Drivers</span>
        </div>
        <table>
          <tr>
            <th><i class="fa fa-hashtag"></i> Driver ID</th>
            <th><i class="fa fa-user"></i> Name</th>
            <th><i class="fa fa-phone"></i> Phone</th>
            <th><i class="fa fa-tasks"></i> Active Deliveries</th>
            <th>Status</th>
          </tr>
          <?php if ($drivers && $drivers->num_rows > 0) {
            while ($row = $drivers->fetch_assoc()) {
              $status = $row['active_deliveries'] > 0 ? 'On Duty' : 'Available';
              $status_class = $status == 'On Duty' ? 'in-transit' : 'pending';
          ?>
          <tr>
            <td><?php echo $row['user_id']; ?></td>
            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
            <td><?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?></td>
            <td><?php echo $row['active_deliveries']; ?></td>
            <td><span class="status <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
          </tr>
          <?php }
          } else {
            echo '<tr><td colspan="5" style="text-align:center; padding:20px;">No drivers found.</td></tr>';
          } ?>
        </table>
      </div>
    </main>
  </body>
</html>

