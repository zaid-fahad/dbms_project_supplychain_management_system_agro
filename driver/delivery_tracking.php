<?php
include "../db.php";

$driver_id = 1;

$sql = "
    SELECT d.delivery_id, d.status
    FROM Deliveries d
    WHERE d.driver_id = $driver_id
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Deliveries</title>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>

<?php include '../components/topbar.html'; ?>
<?php $page_title = 'My Deliveries'; include '../components/header.html'; ?>

<?php include 'components/nav.html'; ?>

<main>

    <div class="card">
        <div class="card-header">
            <span class="card-title">My Delivery Tasks</span>
        </div>

        <table>
            <tr>
                <th>Delivery ID</th>
                <th>Pickup From</th>
                <th>Destination</th>
                <th>Product</th>
                <th>Status</th>
            </tr>

            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                    $status = $row['status'];
                    $status_class = strtolower(str_replace(' ', '-', $status));
            ?>

            <tr>
                <td>DEL-<?php echo $row['delivery_id']; ?></td>
                <td>Farmer Rahim</td>
                <td>Dhaka Warehouse</td>
                <td>Rice (500kg)</td>

                <td>
                    <span class="status <?php echo $status_class; ?>">
                        <?php echo $status; ?>
                    </span>
                </td>
            </tr>

            <?php
                }
            } else {
                echo "<tr><td colspan='5'>No deliveries found</td></tr>";
            }
            ?>

        </table>
    </div>

</main>

</body>
</html>

