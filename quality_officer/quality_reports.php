<?php
include "../db.php";

$totalResult = $conn->query("SELECT COUNT(*) AS total FROM Quality_Checks");
$total = $totalResult ? intval($totalResult->fetch_assoc()['total']) : 0;
$approvedResult = $conn->query("SELECT COUNT(*) AS approved FROM Quality_Checks WHERE quality_tag = 'approved'");
$approved = $approvedResult ? intval($approvedResult->fetch_assoc()['approved']) : 0;
$rejectedResult = $conn->query("SELECT COUNT(*) AS rejected FROM Quality_Checks WHERE quality_tag = 'rejected'");
$rejected = $rejectedResult ? intval($rejectedResult->fetch_assoc()['rejected']) : 0;
$approveRate = $total ? round(($approved / $total) * 100, 1) : 0;

$reportSql = "SELECT DATE(check_date) AS report_date, COUNT(*) AS total_batches, SUM(quality_tag = 'approved') AS approved, SUM(quality_tag = 'rejected') AS rejected, ROUND(100 * SUM(quality_tag = 'approved') / COUNT(*), 1) AS approve_rate FROM Quality_Checks GROUP BY DATE(check_date) ORDER BY report_date DESC LIMIT 7";
$reportResult = $conn->query($reportSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quality Reports - Quality Officer</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Quality Reports'; include '../components/header.html'; ?>

    <main>
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fa fa-check-square-o"></i>
                <div class="value"><?php echo $total; ?></div>
                <div class="label">Total Checks</div>
            </div>
            <div class="stat-card">
                <i class="fa fa-check-circle"></i>
                <div class="value"><?php echo $approved; ?></div>
                <div class="label">Approved</div>
            </div>
            <div class="stat-card">
                <i class="fa fa-times-circle"></i>
                <div class="value"><?php echo $rejected; ?></div>
                <div class="label">Rejected</div>
            </div>
            <div class="stat-card">
                <i class="fa fa-percent"></i>
                <div class="value"><?php echo $approveRate; ?>%</div>
                <div class="label">Approve Rate</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title">Quality Report Summary</span>
            </div>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Total Batches</th>
                    <th>Approved</th>
                    <th>Rejected</th>
                    <th>Approve Rate</th>
                </tr>
                <?php if ($reportResult && $reportResult->num_rows > 0): ?>
                    <?php while ($row = $reportResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['report_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['total_batches']); ?></td>
                            <td><?php echo htmlspecialchars($row['approved']); ?></td>
                            <td><?php echo htmlspecialchars($row['rejected']); ?></td>
                            <td><?php echo htmlspecialchars($row['approve_rate']); ?>%</td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No quality report data available.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </main>
</body>
</html>