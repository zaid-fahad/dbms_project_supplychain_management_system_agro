<?php
include "../db.php";
$message = '';
$message_type = '';

$vehicle_id = isset($_GET['vehicle_id']) ? intval($_GET['vehicle_id']) : 0;
$vehicle = null;

if ($vehicle_id > 0) {
    $vehicle_result = $conn->query("SELECT * FROM Vehicles WHERE vehicle_id=$vehicle_id");
    if ($vehicle_result && $vehicle_result->num_rows > 0) {
        $vehicle = $vehicle_result->fetch_assoc();
    } else {
        $message = 'Vehicle not found.';
        $message_type = 'error';
    }
}

if (isset($_POST['submit']) && $vehicle) {
    $license_plate = $conn->real_escape_string(trim($_POST['license_plate']));
    $vehicle_type = $conn->real_escape_string(trim($_POST['vehicle_type']));

    if ($license_plate === '' || $vehicle_type === '') {
        $message = 'Please fill out both fields.';
        $message_type = 'error';
    } else {
        $check = $conn->query("SELECT vehicle_id FROM Vehicles WHERE license_plate='$license_plate' AND vehicle_id != $vehicle_id");
        if ($check && $check->num_rows > 0) {
            $message = 'Another vehicle already uses that license plate.';
            $message_type = 'error';
        } else {
            if ($conn->query("UPDATE Vehicles SET license_plate='$license_plate', vehicle_type='$vehicle_type' WHERE vehicle_id=$vehicle_id") === TRUE) {
                $message = 'Vehicle updated successfully.';
                $message_type = 'success';
                header('Refresh:2; url=vehicle_list.php');
            } else {
                $message = 'Error updating vehicle: ' . $conn->error;
                $message_type = 'error';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Vehicle - Transport Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Edit Vehicle'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
          <span class="card-title"><i class="fa fa-edit"></i> Update Vehicle</span>
        </div>

        <?php if ($message): ?>
          <div class="alert alert-<?php echo $message_type; ?>" role="alert"><i class="fa fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i> <?php echo $message; ?></div>
        <?php endif; ?>

        <?php if ($vehicle): ?>
          <form action="edit_vehicle.php?vehicle_id=<?php echo $vehicle_id; ?>" method="POST">
            <div class="form-group">
              <label for="license_plate">License Plate</label>
              <input type="text" id="license_plate" name="license_plate" value="<?php echo htmlspecialchars($vehicle['license_plate']); ?>" required />
            </div>
            <div class="form-group">
              <label for="vehicle_type">Vehicle Type</label>
              <select id="vehicle_type" name="vehicle_type" required>
                <option value="">Select Type</option>
                <option value="Truck" <?php echo $vehicle['vehicle_type'] === 'Truck' ? 'selected' : ''; ?>>Truck</option>
                <option value="Van" <?php echo $vehicle['vehicle_type'] === 'Van' ? 'selected' : ''; ?>>Van</option>
                <option value="Pickup" <?php echo $vehicle['vehicle_type'] === 'Pickup' ? 'selected' : ''; ?>>Pickup</option>
                <option value="Trailer" <?php echo $vehicle['vehicle_type'] === 'Trailer' ? 'selected' : ''; ?>>Trailer</option>
              </select>
            </div>
            <button type="submit" name="submit" class="btn-submit"><i class="fa fa-check"></i> Save Changes</button>
          </form>
        <?php else: ?>
          <p>Vehicle not found.</p>
        <?php endif; ?>
      </div>
    </main>
  </body>
</html>

