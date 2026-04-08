<?php
include "../db.php";
$message = '';
$selected_farmer_id = isset($_GET['farmer_id']) ? intval($_GET['farmer_id']) : 0;

if (isset($_GET['delete_batch_id']) && $selected_farmer_id) {
    $batch_id = intval($_GET['delete_batch_id']);
    if ($conn->query("DELETE FROM Batches WHERE batch_id=$batch_id AND farmer_id=$selected_farmer_id") === TRUE) {
        $message = 'Batch deleted successfully.';
    } else {
        $message = 'Error deleting batch: ' . $conn->error;
    }
}

$farmer_sql = "SELECT farmer_id, name FROM Farmers ORDER BY name";
$farmer_result = $conn->query($farmer_sql);
$statuses = [];
if ($selected_farmer_id) {
    $query = "SELECT b.batch_id, b.batch_number, p.product_name, b.quantity, b.unit, DATE_FORMAT(b.purchase_date, '%Y-%m-%d') AS purchase_date, q.quality_tag FROM Batches b JOIN Products p ON b.product_id=p.product_id LEFT JOIN Quality_Checks q ON b.batch_id=q.batch_id WHERE b.farmer_id=$selected_farmer_id ORDER BY b.purchase_date DESC";
    $statuses = $conn->query($query);
    $farmer_name_query = $conn->query("SELECT name FROM Farmers WHERE farmer_id=$selected_farmer_id");
    $farmer_name = $farmer_name_query->num_rows ? $farmer_name_query->fetch_assoc()['name'] : 'Selected Farmer';
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Farmer Status - Field Supervisor</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  </head>
  <body>
    <?php include 'components/topbar.html'; ?>
    <header>
      <div class="header-left">
        <img src="../logo.png" alt="Logo" class="logo" />
        <span class="title">Farmer Produce Status</span>
      </div>
      <a href="dashboard.php" class="back-btn"><i class="fa fa-arrow-left"></i> Back</a>
    </header>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card" style="max-width: 900px; margin: 0 auto;">
        <div class="card-header">
          <span class="card-title">Select Farmer</span>
        </div>
        <form action="" method="GET" style="display:flex; gap: 12px; flex-wrap: wrap; align-items: center;">
          <div class="form-group" style="flex:1; min-width: 260px;">
            <label for="farmer_id">Farmer</label>
            <select id="farmer_id" name="farmer_id" required>
              <option value="">Choose a farmer</option>
              <?php while ($farmer = $farmer_result->fetch_assoc()) {
                $selected = $farmer['farmer_id'] === $selected_farmer_id ? ' selected' : '';
                echo '<option value="' . $farmer['farmer_id'] . '"' . $selected . '>' . htmlspecialchars($farmer['name']) . '</option>';
              } ?>
            </select>
          </div>
          <button type="submit" class="btn-submit" style="margin-top: 26px;">
            <i class="fa fa-search"></i> Show Status
          </button>
        </form>
      </div>

      <?php if ($message): ?>
        <div class="alert alert-success" role="alert"><?php echo $message; ?></div>
      <?php endif; ?>

      <?php if ($selected_farmer_id): ?>
      <div class="card">
        <div class="card-header">
          <span class="card-title">Produce Status for <?php echo htmlspecialchars($farmer_name); ?></span>
        </div>
        <table>
          <tr>
            <th>Batch ID</th>
            <th>Produce</th>
            <th>Quantity</th>
            <th>Date</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
          <?php if ($statuses && $statuses->num_rows > 0) {
            while ($row = $statuses->fetch_assoc()) {
              $status = $row['quality_tag'] ? 'Approved' : 'Pending';
              $status_class = $status == 'Approved' ? 'completed' : 'pending';
          ?>
          <tr>
            <td><?php echo $row['batch_number']; ?></td>
            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
            <td><?php echo $row['quantity'] . ' ' . htmlspecialchars($row['unit']); ?></td>
            <td><?php echo $row['purchase_date']; ?></td>
            <td><span class="status <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
            <td>
              <button class="btn btn-info" onclick="viewDetails('<?php echo $row['batch_number']; ?>', '<?php echo htmlspecialchars(addslashes($row['product_name'])); ?>', '<?php echo $row['quantity'] . ' ' . htmlspecialchars(addslashes($row['unit'])); ?>', '<?php echo $row['purchase_date']; ?>', '<?php echo $status; ?>')">View</button>
              <a class="btn btn-warning" href="../farmer/batch_update.php?batch_id=<?php echo $row['batch_id']; ?>">Edit</a>
              <?php if (!$row['quality_tag']): ?>
                <a class="btn btn-danger" href="farmer_status.php?farmer_id=<?php echo $selected_farmer_id; ?>&delete_batch_id=<?php echo $row['batch_id']; ?>" onclick="return confirm('Delete this batch?');">Delete</a>
              <?php endif; ?>
            </td>
          </tr>
          <?php }
          } else {
            echo '<tr><td colspan="6">No batches found for this farmer.</td></tr>';
          } ?>
        </table>
      </div>
      <?php endif; ?>
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
      function viewDetails(batch, product, qty, date, status) {
        document.getElementById('modalBody').innerHTML = `
          <div class="detail-row"><span class="detail-label">Batch ID:</span><span class="detail-value">${batch}</span></div>
          <div class="detail-row"><span class="detail-label">Produce:</span><span class="detail-value">${product}</span></div>
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
