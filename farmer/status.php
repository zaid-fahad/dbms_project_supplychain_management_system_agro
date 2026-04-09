<?php 
include "../db.php";
$farmer_id = 1; // Assuming farmer_id=1 for demo; replace with session variable in production

if (isset($_GET['batch_id'])) {
    $batch_id = $_GET['batch_id'];

    $sql = "DELETE FROM Batches WHERE batch_id='$batch_id' AND farmer_id=$farmer_id";

    $result = $conn->query($sql);

    if ($result == TRUE) {
        header("Location: ./status.php?deleted=1");
        exit();
    } else {
        echo "Error:" . $sql . "<br>" . $conn->error;
    }
}

$sql = "SELECT b.batch_id, p.product_name, b.quantity, b.unit, b.purchase_date, q.quality_tag FROM Batches b JOIN Products p ON b.product_id = p.product_id LEFT JOIN Quality_Checks q ON b.batch_id = q.batch_id WHERE b.farmer_id=$farmer_id";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Produce Status - Farmer</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Check Produce Status'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <?php if (isset($_GET['deleted'])) { echo '<div class="alert alert-success" role="alert">Batch deleted successfully!</div>'; } ?>

    <main>
      <div class="card">
        <div class="card-header">
          <span class="card-title">Produce Status</span>
        </div>
        <table>
          <tr>
            <th>Batch ID</th>
            <th>Produce Type</th>
            <th>Quantity</th>
            <th>Date</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
          <?php
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $status = $row['quality_tag'] ? 'Approved' : 'Pending';
              $status_class = $status == 'Approved' ? 'completed' : 'pending';
          ?>
          <tr>
            <td><?php echo $row['batch_id']; ?></td>
            <td><?php echo $row['product_name']; ?></td>
            <td><?php echo $row['quantity'] . ' ' . $row['unit']; ?></td>
            <td><?php echo $row['purchase_date']; ?></td>
            <td><span class="status <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
            <td>
              <button class="btn btn-info" onclick="viewDetails('<?php echo $row['batch_id']; ?>', '<?php echo $row['product_name']; ?>', '<?php echo $row['quantity'] . ' ' . $row['unit']; ?>', '<?php echo $row['purchase_date']; ?>', '<?php echo $status; ?>', 'N/A')">View</button>
              <a href="batch_update.php?batch_id=<?php echo $row['batch_id']; ?>" class="btn btn-warning">Edit</a>
              <?php if (!$row['quality_tag']) { ?>
              <a href="status.php?batch_id=<?php echo $row['batch_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this batch?')">Delete</a>
              <?php } ?>
            </td>
          </tr>
          <?php
            }
          } else {
            echo "<tr><td colspan='6'>No batches found.</td></tr>";
          }
          $conn->close();
          ?>
        </table>
      </div>
    </main>

    <!-- 
    <nav>
        <a href="dashboard.html"><i class="fa fa-home"></i> Dashboard</a>
        <a href="produce.html"><i class="fa fa-plus-circle"></i> Add Produce</a>
        <a href="status.html" class="active"><i class="fa fa-list-alt"></i> Status</a>
        <a href="history.html"><i class="fa fa-history"></i> History</a>
    </nav> -->

    <div class="modal" id="detailsModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Produce Details</h3>
          <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div id="modalBody"></div>
      </div>
    </div>

    <script>
      function viewDetails(id, type, quantity, date, status, price) {
        const modal = document.getElementById("detailsModal");
        const modalBody = document.getElementById("modalBody");
        modalBody.innerHTML = `
                <div class="detail-row">
                    <span class="detail-label">Batch ID:</span>
                    <span class="detail-value">${id}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Produce Type:</span>
                    <span class="detail-value">${type}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Quantity:</span>
                    <span class="detail-value">${quantity}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value">${date}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value"><span class="status ${
                      status === "Approved" ? "completed" : "pending"
                    }">${status}</span></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Price:</span>
                    <span class="detail-value">${price}</span>
                </div>
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <a href="batch_update.php?batch_id=${id}" class="btn btn-primary">Edit</a>
                    <button class="btn btn-danger" onclick="closeModal()">Close</button>
                </div>
            `;
        modal.classList.add("active");
      }

      function closeModal() {
        document.getElementById("detailsModal").classList.remove("active");
      }
    </script>
  
  </body>
</html>

