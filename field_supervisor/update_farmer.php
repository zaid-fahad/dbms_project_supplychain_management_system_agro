<?php
include "../db.php";
$message = '';
$farmer_id = isset($_GET['farmer_id']) ? intval($_GET['farmer_id']) : 0;
$farmer = null;

if ($farmer_id) {
    $sql = "SELECT * FROM Farmers WHERE farmer_id=$farmer_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $farmer = $result->fetch_assoc();
    } else {
        $message = 'Farmer not found.';
    }
}

if (isset($_POST['submit']) && $farmer_id) {
    $name = $conn->real_escape_string($_POST['name']);
    $location = $conn->real_escape_string($_POST['location']);
    $contact_info = $conn->real_escape_string($_POST['contact_info']);

    $sql = "UPDATE Farmers SET name='$name', location='$location', contact_info='$contact_info' WHERE farmer_id=$farmer_id";

    if ($conn->query($sql) === TRUE) {
        $message = 'Farmer updated successfully.';
        header("refresh:2; url=./farmer_list.php");
    } else {
        $message = 'Error updating farmer: ' . $conn->error;
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Update Farmer - Field Supervisor</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  </head>
  <body>
    <?php include 'components/topbar.html'; ?>
    <header>
      <div class="header-left">
        <img src="../logo.png" alt="Logo" class="logo" />
        <span class="title">Update Farmer</span>
      </div>
      <a href="farmer_list.php" class="back-btn"><i class="fa fa-arrow-left"></i> Back</a>
    </header>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card" style="max-width: 600px; margin: 0 auto">
        <div class="card-header">
          <span class="card-title">Update Farmer</span>
        </div>
        <?php if ($message): ?>
          <div class="alert alert-success" role="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($farmer): ?>
        <form action="" method="POST">
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($farmer['name']); ?>" required />
          </div>
          <div class="form-group">
            <label for="location">Location</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($farmer['location']); ?>" required />
          </div>
          <div class="form-group">
            <label for="contact_info">Contact Info</label>
            <input type="text" id="contact_info" name="contact_info" value="<?php echo htmlspecialchars($farmer['contact_info']); ?>" required />
          </div>
          <button type="submit" name="submit" class="btn-submit">
            <i class="fa fa-check"></i> Update Farmer
          </button>
        </form>
        <?php else: ?>
        <p>Farmer not found.</p>
        <?php endif; ?>
      </div>
    </main>
  </body>
</html>