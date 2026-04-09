<?php
include "../db.php";
$message = '';

if (isset($_GET['delete_vehicle_id'])) {
    $vehicle_id = intval($_GET['delete_vehicle_id']);
    if ($conn->query("DELETE FROM Vehicles WHERE vehicle_id=$vehicle_id") === TRUE) {
        $message = 'Vehicle deleted successfully.';
    } else {
        $message = 'Unable to delete vehicle: ' . $conn->error;
    }
}

$sql = "SELECT v.vehicle_id, v.license_plate, v.vehicle_type, COUNT(d.delivery_id) AS active_deliveries 
    FROM Vehicles v 
    LEFT JOIN Deliveries d ON v.vehicle_id = d.vehicle_id AND d.status <> 'Completed' 
    GROUP BY v.vehicle_id ORDER BY v.vehicle_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vehicle Management - Transport Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Vehicle Management'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <?php if ($message): ?>
        <div class="alert alert-success" role="alert"><i class="fa fa-check-circle"></i> <?php echo $message; ?></div>
      <?php endif; ?>

      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="fa fa-car"></i> Vehicles</span>
          <a href="add_vehicle.php" class="btn btn-primary"><i class="fa fa-plus"></i> Add Vehicle</a>
        </div>
        <table>
          <tr>
            <th><i class="fa fa-hashtag"></i> Vehicle ID</th>
            <th><i class="fa fa-car"></i> License Plate</th>
            <th><i class="fa fa-truck"></i> Type</th>
            <th><i class="fa fa-tasks"></i> Active Deliveries</th>
            <th>Actions</th>
          </tr>
          <?php if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
          ?>
          <tr>
            <td><?php echo $row['vehicle_id']; ?></td>
            <td><?php echo htmlspecialchars($row['license_plate']); ?></td>
            <td><?php echo htmlspecialchars($row['vehicle_type']); ?></td>
            <td><?php echo $row['active_deliveries']; ?></td>
            <td>
              <a href="edit_vehicle.php?vehicle_id=<?php echo $row['vehicle_id']; ?>" class="btn btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>
              <a href="vehicle_list.php?delete_vehicle_id=<?php echo $row['vehicle_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this vehicle?');"><i class="fa fa-trash"></i> Delete</a>
            </td>
          </tr>
          <?php }
          } else {
            echo '<tr><td colspan="5" style="text-align:center; padding:20px;">No vehicles found.</td></tr>';
          } ?>
        </table>
      </div>
    </main>
  </body>
</html>

