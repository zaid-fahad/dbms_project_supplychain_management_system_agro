<?php
include "../db.php";
$sql = "SELECT b.batch_id, b.batch_number, f.name AS farmer_name, p.product_name, b.quantity, b.unit, DATE_FORMAT(b.purchase_date, '%Y-%m-%d') AS purchase_date, q.quality_tag FROM Batches b JOIN Farmers f ON b.farmer_id = f.farmer_id JOIN Products p ON b.product_id = p.product_id LEFT JOIN Quality_Checks q ON b.batch_id = q.batch_id ORDER BY b.purchase_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Batch Management - Field Supervisor</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Batch Management'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card">
        <div class="card-header">
          <span class="card-title">All Batches</span>
        </div>
        <table>
          <tr>
            <th>Batch ID</th>
            <th>Farmer</th>
            <th>Produce</th>
            <th>Quantity</th>
            <th>Date</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
          <?php if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $status = $row['quality_tag'] ? 'Approved' : 'Pending';
              $status_class = $status == 'Approved' ? 'completed' : 'pending';
          ?>
          <tr>
            <td><?php echo $row['batch_number']; ?></td>
            <td><?php echo htmlspecialchars($row['farmer_name']); ?></td>
            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
            <td><?php echo $row['quantity'] . ' ' . htmlspecialchars($row['unit']); ?></td>
            <td><?php echo $row['purchase_date']; ?></td>
            <td><span class="status <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
            <td>
              <button class="btn btn-info" onclick="viewBatch('<?php echo $row['batch_number']; ?>', '<?php echo htmlspecialchars(addslashes($row['farmer_name'])); ?>', '<?php echo htmlspecialchars(addslashes($row['product_name'])); ?>', '<?php echo $row['quantity'] . ' ' . htmlspecialchars($row['unit']); ?>', '<?php echo $row['purchase_date']; ?>', '<?php echo $status; ?>')">View</button>
            </td>
          </tr>
          <?php }
          } else {
            echo '<tr><td colspan="7">No batches found.</td></tr>';
          } ?>
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
      function viewBatch(batch, farmer, produce, qty, date, status) {
        document.getElementById('modalBody').innerHTML = `
          <div class="detail-row"><span class="detail-label">Batch ID:</span><span class="detail-value">${batch}</span></div>
          <div class="detail-row"><span class="detail-label">Farmer:</span><span class="detail-value">${farmer}</span></div>
          <div class="detail-row"><span class="detail-label">Produce:</span><span class="detail-value">${produce}</span></div>
          <div class="detail-row"><span class="detail-label">Quantity:</span><span class="detail-value">${qty}</span></div>
          <div class="detail-row"><span class="detail-label">Date:</span><span class="detail-value">${date}</span></div>
          <div class="detail-row"><span class="detail-label">Status:</span><span class="detail-value"><span class="status ${status === 'Approved' ? 'completed' : 'pending'}">${status}</span></span></div>
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

