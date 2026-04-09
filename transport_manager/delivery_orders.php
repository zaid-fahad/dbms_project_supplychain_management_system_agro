<?php
include "../db.php";
$message = '';

if (isset($_GET['update_status']) && isset($_GET['delivery_id'])) {
    $delivery_id = intval($_GET['delivery_id']);
    $new_status = $conn->real_escape_string($_GET['update_status']);
    
    if (in_array($new_status, ['Assigned', 'In Transit', 'Completed'])) {
        if ($new_status == 'Completed') {
            $sql = "UPDATE Deliveries SET status='$new_status', delivery_time=NOW() WHERE delivery_id=$delivery_id";
        } else {
            $sql = "UPDATE Deliveries SET status='$new_status' WHERE delivery_id=$delivery_id";
        }

        if ($conn->query($sql) === TRUE) {
            $message = "Delivery status updated to $new_status";
        } else {
            $message = 'Unable to update status: ' . $conn->error;
        }
    } else {
        $message = 'Invalid delivery status.';
    }
}


$sql = "SELECT d.delivery_id, o.order_id, c.customer_name, u.full_name AS driver_name, v.license_plate, d.status, 
        d.pickup_time, d.delivery_time FROM Deliveries d 
        LEFT JOIN Orders o ON d.order_id=o.order_id 
        LEFT JOIN Customers c ON o.customer_id=c.customer_id
        LEFT JOIN Users u ON d.driver_id=u.user_id
        LEFT JOIN Vehicles v ON d.vehicle_id=v.vehicle_id
        ORDER BY d.pickup_time DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Delivery Orders - Transport Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Delivery Orders'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <?php if ($message): ?>
        <div class="alert alert-success" role="alert"><i class="fa fa-check-circle"></i> <?php echo $message; ?></div>
      <?php endif; ?>

      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="fa fa-send"></i> Active Delivery Orders</span>
        </div>
        <table>
          <tr>
            <th><i class="fa fa-hashtag"></i> Delivery ID</th>
            <th><i class="fa fa-shopping-cart"></i> Order ID</th>
            <th><i class="fa fa-user"></i> Customer</th>
            <th><i class="fa fa-user-circle"></i> Driver</th>
            <th><i class="fa fa-car"></i> Vehicle</th>
            <th><i class="fa fa-info-circle"></i> Status</th>
            <th>Actions</th>
          </tr>
          <?php if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $status = $row['status'];
              $status_class = ($status == 'Completed') ? 'completed' : (($status == 'In Transit') ? 'in-transit' : 'pending');
          ?>
          <tr>
            <td><?php echo $row['delivery_id']; ?></td>
            <td><?php echo $row['order_id'] ?? 'N/A'; ?></td>
            <td><?php echo htmlspecialchars($row['customer_name'] ?? 'Unknown'); ?></td>
            <td><?php echo htmlspecialchars($row['driver_name'] ?? 'Unassigned'); ?></td>
            <td><?php echo htmlspecialchars($row['license_plate'] ?? 'N/A'); ?></td>
            <td><span class="status <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
            <td>
              <?php if ($status != 'Completed'): ?>
                <a href="?update_status=<?php echo ($status == 'Assigned') ? 'In Transit' : 'Completed'; ?>&delivery_id=<?php echo $row['delivery_id']; ?>" class="btn btn-info btn-sm">
                  <i class="fa fa-arrow-right"></i> <?php echo ($status == 'Assigned') ? 'Start Transit' : 'Complete'; ?>
                </a>
              <?php else: ?>
                <span style="color: #4caf50;"><i class="fa fa-check"></i> Done</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php }
          } else {
            echo '<tr><td colspan="7" style="text-align:center; padding:20px;">No deliveries found.</td></tr>';
          } ?>
        </table>
      </div>
    </main>
  </body>
</html>

