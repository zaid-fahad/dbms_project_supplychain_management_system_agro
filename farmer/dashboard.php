<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Farmer Dashboard</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php
    include "../db.php";
    $farmer_id = 1; // Assuming farmer_id=1 for demo; replace with session variable in production

    // Total Batches
    $sql_total = "SELECT COUNT(*) as total FROM Batches WHERE farmer_id=$farmer_id";
    $result_total = $conn->query($sql_total);
    $total_batches = $result_total->fetch_assoc()['total'];

    // Approved
    $sql_approved = "SELECT COUNT(*) as approved FROM Batches b JOIN Quality_Checks q ON b.batch_id = q.batch_id WHERE b.farmer_id=$farmer_id AND q.quality_tag = 'Approved'";
    $result_approved = $conn->query($sql_approved);
    $approved = $result_approved->fetch_assoc()['approved'];

    // Pending
    $sql_pending = "SELECT COUNT(*) as pending FROM Batches b LEFT JOIN Quality_Checks q ON b.batch_id = q.batch_id WHERE b.farmer_id=$farmer_id AND q.quality_tag IS NULL";
    $result_pending = $conn->query($sql_pending);
    $pending = $result_pending->fetch_assoc()['pending'];

    // Total Earnings - No price data in schema, set to 0
    $total_earnings = 0;
    ?>

    <?php include 'components/topbar.html'; ?>

    <header>
      <div class="header-left">
        <img src="../logo.png" alt="Logo" class="logo" />
        <span class="title">Farmer Dashboard</span>
      </div>
      <a href="../index.php" class="back-btn"
        ><i class="fa fa-arrow-left"></i> Back</a
      >
    </header>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="stats-grid">
        <div class="stat-card">
          <i class="fa fa-leaf"></i>
          <div class="value"><?php echo $total_batches; ?></div>
          <div class="label">Total Batches</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-check-circle"></i>
          <div class="value"><?php echo $approved; ?></div>
          <div class="label">Approved</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-clock-o"></i>
          <div class="value"><?php echo $pending; ?></div>
          <div class="label">Pending</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-money"></i>
          <div class="value"><?php echo $total_earnings; ?></div>
          <div class="label">Total Earnings (BDT)</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Quick Actions</span>
        </div>
        <div class="quick-actions">
          <a href="produce.php" class="action-btn">
            <i class="fa fa-plus-circle"></i>
            <span>Add Produce</span>
          </a>
          <a href="status.php" class="action-btn">
            <i class="fa fa-list-alt"></i>
            <span>Check Status</span>
          </a>
          <a href="history.php" class="action-btn">
            <i class="fa fa-history"></i>
            <span>Sales History</span>
          </a>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Recent Produce</span>
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
          $sql = "SELECT b.batch_id, p.product_name, b.quantity, b.unit, b.purchase_date, q.quality_tag FROM Batches b JOIN Products p ON b.product_id = p.product_id LEFT JOIN Quality_Checks q ON b.batch_id = q.batch_id WHERE b.farmer_id=$farmer_id ORDER BY b.purchase_date DESC LIMIT 10";
          $result = $conn->query($sql);
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
              <button class="btn btn-info" onclick="viewDetails('<?php echo $row['batch_id']; ?>', '<?php echo $row['product_name']; ?>', '<?php echo $row['quantity'] . ' ' . $row['unit']; ?>', '<?php echo $row['purchase_date']; ?>', '<?php echo $status; ?>', 'N/A')">
                View
              </button>
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
