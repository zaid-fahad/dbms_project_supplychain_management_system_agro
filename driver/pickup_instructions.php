<?php
include "../db.php";

$driver_id = 1;

$sql = "
    SELECT d.delivery_id
    FROM Deliveries d
    WHERE d.driver_id = $driver_id
    LIMIT 1
";

$result = $conn->query($sql);
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pickup Instructions</title>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>

<?php include '../components/topbar.html'; ?>
<?php $page_title = 'Pickup Instructions'; include '../components/header.html'; ?>

<main>

    <div class="card">

        <div class="card-header">
            <span class="card-title">
                Pickup Details - DEL-<?php echo $data['delivery_id']; ?>
            </span>
        </div>

        <div style="padding: 20px;">

            <p><strong>Farmer Name:</strong> Rahim</p>
            <p><strong>Address:</strong> Shariatpur, Bangladesh</p>
            <p><strong>Phone:</strong> 0171xxxxxxx</p>
            <p><strong>Product:</strong> Rice</p>
            <p><strong>Quantity:</strong> 500 kg</p>
            <p><strong>Expected Arrival:</strong> 10:00 AM</p>

            <button class="btn btn-primary">
                <i class="fa fa-check"></i> Confirm Pickup
            </button>

        </div>

    </div>

</main>

</body>
</html>

