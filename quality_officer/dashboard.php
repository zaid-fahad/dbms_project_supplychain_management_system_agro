<?php
include "../db.php";

// Get stats from database
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM Quality_Checks");
$total = $totalResult ? intval($totalResult->fetch_assoc()['total']) : 0;

$approvedResult = $conn->query("SELECT COUNT(*) AS approved FROM Quality_Checks WHERE quality_tag = 'approved'");
$approved = $approvedResult ? intval($approvedResult->fetch_assoc()['approved']) : 0;

$rejectedResult = $conn->query("SELECT COUNT(*) AS rejected FROM Quality_Checks WHERE quality_tag = 'rejected'");
$rejected = $rejectedResult ? intval($rejectedResult->fetch_assoc()['rejected']) : 0;

$pendingResult = $conn->query("SELECT COUNT(*) AS pending FROM Quality_Checks WHERE quality_tag = 'pending'");
$pending = $pendingResult ? intval($pendingResult->fetch_assoc()['pending']) : 0;

$uncheckedResult = $conn->query("SELECT COUNT(*) AS unchecked FROM Batches b LEFT JOIN Quality_Checks q ON b.batch_id = q.batch_id WHERE q.check_id IS NULL");
$unchecked = $uncheckedResult ? intval($uncheckedResult->fetch_assoc()['unchecked']) : 0;

// Get pending batches (batches without quality checks)
$pendingBatchesSql = "SELECT b.batch_id, b.batch_number, p.product_name, f.name AS farmer_name, b.quantity, b.unit, DATE_FORMAT(b.purchase_date, '%Y-%m-%d') AS purchase_date FROM Batches b LEFT JOIN Quality_Checks q ON b.batch_id = q.batch_id JOIN Farmers f ON b.farmer_id = f.farmer_id JOIN Products p ON b.product_id = p.product_id WHERE q.check_id IS NULL ORDER BY b.purchase_date DESC";
$pendingBatchesResult = $conn->query($pendingBatchesSql);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quality Officer Dashboard</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Quality Officer Dashboard'; include '../components/header.html'; ?>

    <!-- 
    <nav>
        <a href="quality_check.php"><i class="fa fa-check-square-o"></i> Quality Check</a>
        <a href="batch_approval.php"><i class="fa fa-check-circle"></i> Batch Approval</a>
        <a href="quality_reports.php"><i class="fa fa-bar-chart"></i> Quality Reports</a>
    </nav> -->

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
          <i class="fa fa-clock-o"></i>
          <div class="value"><?php echo $pending; ?></div>
          <div class="label">Pending</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-exclamation-triangle"></i>
          <div class="value"><?php echo $unchecked; ?></div>
          <div class="label">Unchecked</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Quick Actions</span>
        </div>
        <div class="quick-actions">
          <a href="quality_check.php" class="action-btn">
            <i class="fa fa-check-square-o"></i>
            <span>Quality Check</span>
          </a>
          <a href="batch_approval.php" class="action-btn">
            <i class="fa fa-check-circle"></i>
            <span>Batch Approval</span>
          </a>
          <a href="quality_reports.php" class="action-btn">
            <i class="fa fa-bar-chart"></i>
            <span>Quality Reports</span>
          </a>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Pending Quality Checks</span>
        </div>
        <table>
          <tr>
            <th>Batch ID</th>
            <th>Produce</th>
            <th>Quantity</th>
            <th>Submitted</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
          <?php if ($pendingBatchesResult && $pendingBatchesResult->num_rows > 0): ?>
            <?php while ($row = $pendingBatchesResult->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['batch_number']); ?></td>
                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                <td><?php echo htmlspecialchars($row['quantity'] . ' ' . $row['unit']); ?></td>
                <td><?php echo htmlspecialchars($row['farmer_name'] ?? 'Unknown'); ?></td>
                <td><?php echo htmlspecialchars($row['purchase_date']); ?></td>
                <td>
                  <a href="quality_check.php" class="btn btn-primary">Check</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6">No pending batches available.</td>
            </tr>
          <?php endif; ?>
        </table>
      </div>
    </main>

    <div class="modal" id="detailsModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Quality Check Details</h3>
          <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div id="modalBody"></div>
      </div>
    </div>

    <script>
      function viewDetails(
        id,
        produce,
        quantity,
        submitted,
        date,
        score,
        grade
      ) {
        document.getElementById("modalBody").innerHTML = `
                <div class="detail-row">
                    <span class="detail-label">Batch ID:</span>
                    <span class="detail-value">${id}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Produce:</span>
                    <span class="detail-value">${produce}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Quantity:</span>
                    <span class="detail-value">${quantity}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Submitted By:</span>
                    <span class="detail-value">${submitted}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value">${date}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Quality Score:</span>
                    <span class="detail-value">${score}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Grade:</span>
                    <span class="detail-value">${grade}</span>
                </div>
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button class="btn btn-primary">View Full Report</button>
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
