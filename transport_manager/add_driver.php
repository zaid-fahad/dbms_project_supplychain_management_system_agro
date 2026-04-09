<?php
include "../db.php";
$message = '';
$message_type = '';

if (isset($_POST['submit'])) {
    $username = $conn->real_escape_string(trim($_POST['username']));
    $full_name = $conn->real_escape_string(trim($_POST['full_name']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));

    if ($username === '' || $full_name === '' || $phone === '') {
        $message = 'Please complete all fields.';
        $message_type = 'error';
    } else {
        $check = $conn->query("SELECT user_id FROM Users WHERE username='$username'");
        if ($check && $check->num_rows > 0) {
            $message = 'Username already exists.';
            $message_type = 'error';
        } else {
            if ($conn->query("INSERT INTO Users (username, password_hash, full_name, role, phone) VALUES ('$username', 'hash', '$full_name', 'Driver', '$phone')") === TRUE) {
                $message = 'Driver added successfully.';
                $message_type = 'success';
                header('Refresh:2; url=driver_list.php');
            } else {
                $message = 'Error adding driver: ' . $conn->error;
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
    <title>Add Driver - Transport Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Add Driver'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
          <span class="card-title"><i class="fa fa-plus"></i> New Driver</span>
        </div>

        <?php if ($message): ?>
          <div class="alert alert-<?php echo $message_type; ?>" role="alert"><i class="fa fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i> <?php echo $message; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter username" required />
          </div>
          <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" placeholder="Enter full name" required />
          </div>
          <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" placeholder="Enter phone number" required />
          </div>
          <button type="submit" name="submit" class="btn-submit"><i class="fa fa-check"></i> Save Driver</button>
        </form>
      </div>
    </main>
  </body>
</html>

