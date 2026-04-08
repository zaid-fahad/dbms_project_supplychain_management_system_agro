<?php
include "../db.php";
$message = '';
if (isset($_GET['delete_farmer_id'])) {
    $delete_id = intval($_GET['delete_farmer_id']);
    $conn->query("DELETE FROM Batches WHERE farmer_id=$delete_id");
    if ($conn->query("DELETE FROM Farmers WHERE farmer_id=$delete_id") === TRUE) {
        $message = 'Farmer deleted successfully.';
    } else {
        $message = 'Error deleting farmer: ' . $conn->error;
    }
}

$sql = "SELECT f.farmer_id, f.name, f.location, f.contact_info, COUNT(b.batch_id) AS total_batches FROM Farmers f LEFT JOIN Batches b ON f.farmer_id=b.farmer_id GROUP BY f.farmer_id ORDER BY f.name";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Farmer List - Field Supervisor</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  </head>
  <body>
    <?php include 'components/topbar.html'; ?>
    <header>
      <div class="header-left">
        <img src="../logo.png" alt="Logo" class="logo" />
        <span class="title">Farmer List</span>
      </div>
      <a href="dashboard.php" class="back-btn"><i class="fa fa-arrow-left"></i> Back</a>
    </header>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card">
        <div class="card-header">
          <span class="card-title">Registered Farmers</span>
          <a href="add_farmer.php" class="btn btn-primary"><i class="fa fa-plus"></i> Add Farmer</a>
        </div>
        <?php if ($message): ?>
            <div class="alert alert-success" role="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        <table>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Location</th>
            <th>Contact</th>
            <th>Total Batches</th>
            <th>Action</th>
          </tr>
          <?php if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
          ?>
          <tr>
            <td><?php echo $row['farmer_id']; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['location']); ?></td>
            <td><?php echo htmlspecialchars($row['contact_info']); ?></td>
            <td><?php echo $row['total_batches']; ?></td>
            <td>
              <a class="btn btn-info" href="farmer_status.php?farmer_id=<?php echo $row['farmer_id']; ?>">View Batches</a>
              <a class="btn btn-warning" href="update_farmer.php?farmer_id=<?php echo $row['farmer_id']; ?>">Edit</a>
              <a class="btn btn-danger" href="farmer_list.php?delete_farmer_id=<?php echo $row['farmer_id']; ?>" onclick="return confirm('Delete this farmer and all associated batches?');">Delete</a>
            </td>
          </tr>
          <?php }
          } else {
            echo '<tr><td colspan="6">No farmers found.</td></tr>';
          } ?>
        </table>
      </div>
    </main>
  </body>
</html>
