<?php
include "../db.php";

$driver_id = 1;

if (isset($_POST['update'])) {
    $delivery_id = $_POST['delivery_id'];
    $status = $_POST['status'];

    $conn->query("
        UPDATE Deliveries
        SET status = '$status'
        WHERE delivery_id = $delivery_id
        AND driver_id = $driver_id
    ");
}

$result = $conn->query("
    SELECT delivery_id
    FROM Deliveries
    WHERE driver_id = $driver_id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Delivery Status</title>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>

<?php include '../components/topbar.html'; ?>
<?php $page_title = 'Update Delivery Status'; include '../components/header.html'; ?>

<?php include 'components/nav.html'; ?>

<main>

    <div class="card" style="max-width: 600px; margin: 0 auto;">

        <div class="card-header">
            <span class="card-title">Update Delivery Status</span>
        </div>

        <form method="POST">

            <div class="form-group">
                <label>Delivery ID</label>
                <select name="delivery_id" required>
                    <option value="">Select Delivery</option>

                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <option value="<?php echo $row['delivery_id']; ?>">
                            DEL-<?php echo $row['delivery_id']; ?>
                        </option>
                    <?php } ?>

                </select>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="">Select Status</option>
                    <option value="Assigned">Assigned</option>
                    <option value="In Transit">In Transit</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>

            <div class="form-group">
                <label>Current Location</label>
                <input type="text" placeholder="Enter current location">
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea rows="3" placeholder="Any additional notes"></textarea>
            </div>

            <button name="update" class="btn-submit">
                <i class="fa fa-check"></i> Update Status
            </button>

        </form>

    </div>

</main>

</body>
</html>

