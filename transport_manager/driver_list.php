<?php
include "../db.php";
$message = '';

if (isset($_GET['delete_driver_id'])) {
    $driver_id = intval($_GET['delete_driver_id']);
    if ($conn->query("DELETE FROM Users WHERE user_id=$driver_id AND role='Driver'") === TRUE) {
        $message = 'Driver deleted successfully.';
    } else {
        $message = 'Unable to delete driver: ' . $conn->error;
    }
}

$sql = "SELECT u.user_id, u.username, u.full_name, u.phone, COUNT(d.delivery_id) AS active_deliveries 
    FROM Users u 
    LEFT JOIN Deliveries d ON u.user_id = d.driver_id AND d.status <> 'Completed' 
    WHERE u.role = 'Driver' 
    GROUP BY u.user_id ORDER BY u.full_name";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Driver Management - Transport Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Driver Management'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <?php if ($message): ?>
        <div class="alert alert-success" role="alert"><i class="fa fa-check-circle"></i> <?php echo $message; ?></div>
      <?php endif; ?>

      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="fa fa-user"></i> Drivers</span>
          <a href="add_driver.php" class="btn btn-primary"><i class="fa fa-plus"></i> Add Driver</a>
        </div>
        <table>
          <tr>
            <th><i class="fa fa-hashtag"></i> Driver ID</th>
            <th><i class="fa fa-user"></i> Name</th>
            <th><i class="fa fa-user-circle"></i> Username</th>
            <th><i class="fa fa-phone"></i> Phone</th>
            <th><i class="fa fa-tasks"></i> Active Deliveries</th>
            <th>Actions</th>
          </tr>
          <?php if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
          ?>
          <tr>
            <td><?php echo $row['user_id']; ?></td>
            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['phone']); ?></td>
            <td><?php echo $row['active_deliveries']; ?></td>
            <td>
              <a href="edit_driver.php?driver_id=<?php echo $row['user_id']; ?>" class="btn btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>
              <a href="driver_list.php?delete_driver_id=<?php echo $row['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this driver?');"><i class="fa fa-trash"></i> Delete</a>
            </td>
          </tr>
          <?php }
          } else {
            echo '<tr><td colspan="6" style="text-align:center; padding:20px;">No drivers found.</td></tr>';
          } ?>
        </table>
      </div>
    </main>
  </body>
</html>

