<?php
include "../db.php";
$message = '';
$message_type = '';

$driver_id = isset($_GET['driver_id']) ? intval($_GET['driver_id']) : 0;
$driver = null;

if ($driver_id > 0) {
    $driver_result = $conn->query("SELECT * FROM Users WHERE user_id=$driver_id AND role='Driver'");
    if ($driver_result && $driver_result->num_rows > 0) {
        $driver = $driver_result->fetch_assoc();
    } else {
        $message = 'Driver not found.';
        $message_type = 'error';
    }
}

if (isset($_POST['submit']) && $driver) {
    $username = $conn->real_escape_string(trim($_POST['username']));
    $full_name = $conn->real_escape_string(trim($_POST['full_name']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));

    if ($username === '' || $full_name === '' || $phone === '') {
        $message = 'Please complete all fields.';
        $message_type = 'error';
    } else {
        $check = $conn->query("SELECT user_id FROM Users WHERE username='$username' AND user_id != $driver_id");
        if ($check && $check->num_rows > 0) {
            $message = 'Username already exists.';
            $message_type = 'error';
        } else {
            if ($conn->query("UPDATE Users SET username='$username', full_name='$full_name', phone='$phone' WHERE user_id=$driver_id AND role='Driver'") === TRUE) {
                $message = 'Driver updated successfully.';
                $message_type = 'success';
                header('Refresh:2; url=driver_list.php');
            } else {
                $message = 'Error updating driver: ' . $conn->error;
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
    <title>Edit Driver - Transport Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Edit Driver'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
          <span class="card-title"><i class="fa fa-edit"></i> Update Driver</span>
        </div>

        <?php if ($message): ?>
          <div class="alert alert-<?php echo $message_type; ?>" role="alert"><i class="fa fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i> <?php echo $message; ?></div>
        <?php endif; ?>

        <?php if ($driver): ?>
          <form action="edit_driver.php?driver_id=<?php echo $driver_id; ?>" method="POST">
            <div class="form-group">
              <label for="username">Username</label>
              <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($driver['username']); ?>" required />
            </div>
            <div class="form-group">
              <label for="full_name">Full Name</label>
              <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($driver['full_name']); ?>" required />
            </div>
            <div class="form-group">
              <label for="phone">Phone</label>
              <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($driver['phone']); ?>" required />
            </div>
            <button type="submit" name="submit" class="btn-submit"><i class="fa fa-check"></i> Save Changes</button>
          </form>
        <?php else: ?>
          <p>Driver not found.</p>
        <?php endif; ?>
      </div>
    </main>
  </body>
</html>

