<?php
include "../db.php";
$message = '';
$message_type = '';

if (isset($_POST['submit'])) {
    $license_plate = $conn->real_escape_string(trim($_POST['license_plate']));
    $vehicle_type = $conn->real_escape_string(trim($_POST['vehicle_type']));

    if ($license_plate === '' || $vehicle_type === '') {
        $message = 'Please fill out both fields.';
        $message_type = 'error';
    } else {
        $check = $conn->query("SELECT vehicle_id FROM Vehicles WHERE license_plate='$license_plate'");
        if ($check && $check->num_rows > 0) {
            $message = 'License plate already exists.';
            $message_type = 'error';
        } else {
            if ($conn->query("INSERT INTO Vehicles (license_plate, vehicle_type) VALUES ('$license_plate', '$vehicle_type')") === TRUE) {
                $message = 'Vehicle added successfully.';
                $message_type = 'success';
                header('Refresh:2; url=vehicle_list.php');
            } else {
                $message = 'Error adding vehicle: ' . $conn->error;
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
    <title>Add Vehicle - Transport Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Add Vehicle'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
          <span class="card-title"><i class="fa fa-plus"></i> New Vehicle</span>
        </div>

        <?php if ($message): ?>
          <div class="alert alert-<?php echo $message_type; ?>" role="alert"><i class="fa fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i> <?php echo $message; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
          <div class="form-group">
            <label for="license_plate">License Plate</label>
            <input type="text" id="license_plate" name="license_plate" placeholder="Enter license plate" required />
          </div>
          <div class="form-group">
            <label for="vehicle_type">Vehicle Type</label>
            <select id="vehicle_type" name="vehicle_type" required>
              <option value="">Select Type</option>
              <option value="Truck">Truck</option>
              <option value="Van">Van</option>
              <option value="Pickup">Pickup</option>
              <option value="Trailer">Trailer</option>
            </select>
          </div>
          <button type="submit" name="submit" class="btn-submit"><i class="fa fa-check"></i> Save Vehicle</button>
        </form>
      </div>
    </main>
  </body>
</html>

