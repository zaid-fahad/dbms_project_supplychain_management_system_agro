<?php
include "../db.php";
$message = '';

if (isset($_POST['submit'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $location = $conn->real_escape_string($_POST['location']);
    $contact_info = $conn->real_escape_string($_POST['contact_info']);

    $sql = "INSERT INTO Farmers (name, location, contact_info) VALUES ('$name', '$location', '$contact_info')";

    if ($conn->query($sql) === TRUE) {
        $message = 'Farmer added successfully.';
        header("refresh:2; url=./farmer_list.php");
    } else {
        $message = 'Error adding farmer: ' . $conn->error;
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Farmer - Field Supervisor</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Add Farmer'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card" style="max-width: 600px; margin: 0 auto">
        <div class="card-header">
          <span class="card-title">Add New Farmer</span>
        </div>
        <?php if ($message): ?>
          <div class="alert alert-success" role="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="" method="POST">
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" placeholder="Enter farmer name" required />
          </div>
          <div class="form-group">
            <label for="location">Location</label>
            <input type="text" id="location" name="location" placeholder="Enter location" required />
          </div>
          <div class="form-group">
            <label for="contact_info">Contact Info</label>
            <input type="text" id="contact_info" name="contact_info" placeholder="Enter contact info" required />
          </div>
          <button type="submit" name="submit" class="btn-submit">
            <i class="fa fa-check"></i> Add Farmer
          </button>
        </form>
      </div>
    </main>
  </body>
</html>
