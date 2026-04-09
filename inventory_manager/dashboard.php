<?php
include "../db.php";

$total_stock = 0;
$product_types = 0;
$low_stock_items = 0;
$batches_processed = 0;

$stock_result = $conn->query("SELECT SUM(current_stock) AS total FROM Inventory");
if ($stock_result && $stock_result->num_rows > 0) {
    $row = $stock_result->fetch_assoc();
    $total_stock = $row['total'] ? number_format($row['total'], 0) : 0;
}

$product_result = $conn->query("SELECT COUNT(*) AS total FROM Products");
if ($product_result && $product_result->num_rows > 0) {
    $product_types = $product_result->fetch_assoc()['total'];
}

$low_result = $conn->query("SELECT COUNT(*) AS total FROM Inventory WHERE current_stock < 500");
if ($low_result && $low_result->num_rows > 0) {
    $low_stock_items = $low_result->fetch_assoc()['total'];
}

$batch_result = $conn->query("SELECT COUNT(*) AS total FROM Batches");
if ($batch_result && $batch_result->num_rows > 0) {
    $batches_processed = $batch_result->fetch_assoc()['total'];
}

$recent_sql = "SELECT p.product_name, i.current_stock, DATE_FORMAT(i.last_updated, '%Y-%m-%d') AS last_updated FROM Inventory i JOIN Products p ON i.product_id=p.product_id ORDER BY i.last_updated DESC LIMIT 5";
$recent_result = $conn->query($recent_sql);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inventory Manager Dashboard</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Inventory Manager Dashboard'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="stats-grid">
        <div class="stat-card">
          <i class="fa fa-cubes"></i>
          <div class="value"><?php echo $total_stock; ?></div>
          <div class="label">Total Stock (kg)</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-cube"></i>
          <div class="value"><?php echo $product_types; ?></div>
          <div class="label">Product Types</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-exclamation-triangle"></i>
          <div class="value"><?php echo $low_stock_items; ?></div>
          <div class="label">Low Stock Items</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-check-circle"></i>
          <div class="value"><?php echo $batches_processed; ?></div>
          <div class="label">Batches Processed</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Quick Actions</span>
        </div>
        <div class="quick-actions">
          <a href="stock_update.php" class="action-btn">
            <i class="fa fa-plus-square"></i>
            <span>Update Stock</span>
          </a>
          <a href="inventory_reports.php" class="action-btn">
            <i class="fa fa-bar-chart"></i>
            <span>Reports</span>
          </a>
          <a href="low_stock_alerts.php" class="action-btn">
            <i class="fa fa-exclamation-triangle"></i>
            <span>Low Stock</span>
          </a>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="fa fa-arrow-right"></i> Recent Stock Updates</span>
        </div>
        <table>
          <tr>
            <th><i class="fa fa-leaf"></i> Product</th>
            <th><i class="fa fa-cube"></i> Current Stock (kg)</th>
            <th><i class="fa fa-clock-o"></i> Last Updated</th>
            <th><i class="fa fa-info-circle"></i> Status</th>
          </tr>
          <?php if ($recent_result && $recent_result->num_rows > 0) {
            while ($row = $recent_result->fetch_assoc()) {
              $status = $row['current_stock'] < 500 ? 'Low' : 'Good';
              $status_class = $status == 'Low' ? 'warning' : 'success';
          ?>
          <tr>
            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
            <td><?php echo number_format($row['current_stock'], 2); ?></td>
            <td><?php echo $row['last_updated']; ?></td>
            <td><span class="status <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
          </tr>
          <?php }
          } else {
            echo '<tr><td colspan="4" style="text-align:center; padding:20px;">No stock records found.</td></tr>';
          } ?>
        </table>
      </div>
    </main>
  </body>
</html>
          </a>
        </div>
      </div>

    </main>

    <div class="modal" id="detailsModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Stock Details</h3>
          <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div id="modalBody"></div>
      </div>
    </div>

    <script>
      function viewStock(product, warehouse, stock, minStock, status) {
        document.getElementById("modalBody").innerHTML = `
                <div class="detail-row">
                    <span class="detail-label">Product:</span>
                    <span class="detail-value">${product}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Warehouse:</span>
                    <span class="detail-value">${warehouse}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Current Stock:</span>
                    <span class="detail-value">${stock} kg</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Min. Required:</span>
                    <span class="detail-value">${minStock} kg</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value"><span class="status ${
                      status === "In Stock" ? "completed" : "pending"
                    }">${status}</span></span>
                </div>
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button class="btn btn-primary">Update</button>
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

