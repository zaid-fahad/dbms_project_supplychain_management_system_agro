<?php
include "../db.php";

$message = '';
$isSuccess = false;

// Handle approve/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $check_id = isset($_POST['check_id']) ? intval($_POST['check_id']) : 0;
    $action = $_POST['action'];

    if ($check_id && in_array($action, ['approved', 'rejected'])) {
        $sql = "UPDATE Quality_Checks SET quality_tag = '$action' WHERE check_id = $check_id";
        if ($conn->query($sql) === TRUE) {
            $message = "Batch " . ($action === 'approved' ? 'approved' : 'rejected') . " successfully.";
            $isSuccess = true;
        } else {
            $message = 'Error updating batch status: ' . $conn->error;
        }
    }
}

$sql = "SELECT q.check_id, b.batch_number, p.product_name, f.name AS farmer_name, b.quantity, b.unit, q.quality_tag, q.grade, DATE_FORMAT(q.check_date, '%Y-%m-%d') AS check_date, q.moisture_content, q.purity, q.comments FROM Quality_Checks q JOIN Batches b ON q.batch_id = b.batch_id LEFT JOIN Farmers f ON b.farmer_id = f.farmer_id LEFT JOIN Products p ON b.product_id = p.product_id WHERE q.quality_tag = 'pending' ORDER BY q.check_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Approval - Quality Officer</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Batch Approval'; include '../components/header.html'; ?>

    <main>
        <div class="card">
            <div class="card-header">
                <span class="card-title">Batch Approval</span>
            </div>
            <?php if ($message): ?>
              <div class="alert <?php echo $isSuccess ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
              </div>
            <?php endif; ?>
            <table>
                <tr>
                    <th>Batch ID</th>
                    <th>Produce</th>
                    <th>Quantity</th>
                    <th>Submitted By</th>
                    <th>Grade</th>
                    <th>Action</th>
                </tr>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['batch_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['quantity'] . ' ' . $row['unit']); ?></td>
                            <td><?php echo htmlspecialchars($row['farmer_name'] ?? 'Unknown'); ?></td>
                            <td><?php echo htmlspecialchars($row['grade']); ?></td>
                            <td>
                                <form method="post" action="batch_approval.php" style="display: inline;">
                                    <input type="hidden" name="check_id" value="<?php echo $row['check_id']; ?>">
                                    <button type="submit" name="action" value="approved" class="btn btn-primary" style="margin-right: 5px;">Approve</button>
                                    <button type="submit" name="action" value="rejected" class="btn btn-danger">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No pending batches for approval.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </main>

    <div class="modal" id="detailsModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Batch Details</h3>
          <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div id="modalBody"></div>
      </div>
    </div>

    <script>
      function viewDetails(batch, produce, quantity, submittedBy, date, result, grade, moisture, purity, comments) {
        document.getElementById('modalBody').innerHTML = `
          <div class="detail-row"><span class="detail-label">Batch ID:</span><span class="detail-value">${batch}</span></div>
          <div class="detail-row"><span class="detail-label">Produce:</span><span class="detail-value">${produce}</span></div>
          <div class="detail-row"><span class="detail-label">Quantity:</span><span class="detail-value">${quantity}</span></div>
          <div class="detail-row"><span class="detail-label">Submitted By:</span><span class="detail-value">${submittedBy}</span></div>
          <div class="detail-row"><span class="detail-label">Check Date:</span><span class="detail-value">${date}</span></div>
          <div class="detail-row"><span class="detail-label">Result:</span><span class="detail-value">${result}</span></div>
          <div class="detail-row"><span class="detail-label">Grade:</span><span class="detail-value">${grade}</span></div>
          <div class="detail-row"><span class="detail-label">Moisture:</span><span class="detail-value">${moisture ? moisture + ' %' : 'N/A'}</span></div>
          <div class="detail-row"><span class="detail-label">Purity:</span><span class="detail-value">${purity ? purity + ' %' : 'N/A'}</span></div>
          <div class="detail-row"><span class="detail-label">Comments:</span><span class="detail-value">${comments || 'None'}</span></div>
          <div style="margin-top: 20px; display: flex; gap: 10px;"><button class="btn btn-danger" onclick="closeModal()">Close</button></div>
        `;
        document.getElementById('detailsModal').classList.add('active');
      }

      function closeModal() {
        document.getElementById('detailsModal').classList.remove('active');
      }
    </script>
</body>
</html>