<?php
include "../db.php";

$driver_id = 1;

$total = 0;
$completed = 0;
$active = 0;
$pending = 0;

$res = $conn->query("SELECT COUNT(*) AS total FROM Deliveries WHERE driver_id = $driver_id");
if ($res && $row = $res->fetch_assoc()) {
    $total = $row['total'];
}

$res = $conn->query("SELECT COUNT(*) AS total FROM Deliveries WHERE driver_id = $driver_id AND status = 'Completed'");
if ($res && $row = $res->fetch_assoc()) {
    $completed = $row['total'];
}

$res = $conn->query("SELECT COUNT(*) AS total FROM Deliveries WHERE driver_id = $driver_id AND status = 'In Transit'");
if ($res && $row = $res->fetch_assoc()) {
    $active = $row['total'];
}

$res = $conn->query("SELECT COUNT(*) AS total FROM Deliveries WHERE driver_id = $driver_id AND status = 'Assigned'");
if ($res && $row = $res->fetch_assoc()) {
    $pending = $row['total'];
}

$sql = "
    SELECT delivery_id, status
    FROM Deliveries
    WHERE driver_id = $driver_id
    LIMIT 5
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard</title>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>

<?php include '../components/topbar.html'; ?>
<?php $page_title = 'Driver Dashboard'; include '../components/header.html'; ?>

<?php include 'components/nav.html'; ?>

<main>

    <div class="stats-grid">

        <div class="stat-card">
            <i class="fa fa-check-circle"></i>
            <div class="value"><?php echo $completed ?? 0; ?></div>
            <div class="label">Completed</div>
        </div>

        <div class="stat-card">
            <i class="fa fa-truck"></i>
            <div class="value"><?php echo $active ?? 0; ?></div>
            <div class="label">Active</div>
        </div>

        <div class="stat-card">
            <i class="fa fa-clock-o"></i>
            <div class="value"><?php echo $pending ?? 0; ?></div>
            <div class="label">Pending</div>
        </div>

        <div class="stat-card">
            <i class="fa fa-money"></i>
            <div class="value"><?php echo $total ?? 0; ?></div>
            <div class="label">Total Deliveries</div>
        </div>

    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Quick Actions</span>
        </div>

        <div class="quick-actions">
            <a href="delivery_tracking.php" class="action-btn">
                <i class="fa fa-map-marker"></i>
                <span>My Deliveries</span>
            </a>

            <a href="pickup_instructions.php" class="action-btn">
                <i class="fa fa-truck"></i>
                <span>Pickup Info</span>
            </a>

            <a href="delivery_status.php" class="action-btn">
                <i class="fa fa-refresh"></i>
                <span>Update Status</span>
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">My Deliveries</span>
        </div>

        <table>
            <tr>
                <th>Delivery ID</th>
                <th>Pickup From</th>
                <th>Destination</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                    $status = $row['status'];
                    $status_class = strtolower(str_replace(' ', '-', $status));

                    $pickup = "Warehouse";
                    $destination = "Customer";
            ?>

            <tr>
                <td>DEL-<?php echo htmlspecialchars($row['delivery_id']); ?></td>

                <td><?php echo htmlspecialchars($pickup); ?></td>

                <td><?php echo htmlspecialchars($destination); ?></td>

                <td>
                    <span class="status <?php echo $status_class; ?>">
                        <?php echo htmlspecialchars($status); ?>
                    </span>
                </td>

                <td>
                    <button
                        class="btn btn-info"
                        onclick="viewDriverDelivery(
                            'DEL-<?php echo $row['delivery_id']; ?>',
                            '<?php echo $pickup; ?>',
                            '<?php echo $destination; ?>',
                            '<?php echo $status; ?>'
                        )"
                    >
                        View
                    </button>
                </td>
            </tr>

            <?php
                }
            } else {
                echo "<tr><td colspan='5'>No deliveries found</td></tr>";
            }

            $conn->close();
            ?>

        </table>
    </div>

</main>

<div class="modal" id="detailsModal">
    <div class="modal-content">

        <div class="modal-header">
            <h3>Delivery Details</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>

        <div id="modalBody"></div>

    </div>
</div>

<script>
function viewDriverDelivery(deliveryId, pickupFrom, destination, status) {

    const statusClass = status.toLowerCase().replace(" ", "-");

    document.getElementById("modalBody").innerHTML = `
        <div class="detail-row">
            <span class="detail-label">Delivery ID:</span>
            <span>${deliveryId}</span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Pickup From:</span>
            <span>${pickupFrom}</span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Destination:</span>
            <span>${destination}</span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Status:</span>
            <span class="status ${statusClass}">${status}</span>
        </div>

        <div style="margin-top: 20px;">
            <a href="delivery_status.php" class="btn btn-primary">Update Status</a>
            <button class="btn btn-danger" onclick="closeModal()">Close</button>
        </div>
    `;

    document.getElementById("detailsModal").classList.add("active");
}

function closeModal() {
    document.getElementById("detailsModal").classList.remove("active");
}
</script>

</body>
</html>

