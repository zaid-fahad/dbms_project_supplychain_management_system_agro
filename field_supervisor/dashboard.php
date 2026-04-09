<?php
include "../db.php";

$total_batches = 0;
$total_farmers = 0;
$total_purchases = 0;
$total_earnings = 0;

$result = $conn->query("SELECT COUNT(*) AS total FROM Batches");
if ($result) {
    $total_batches = $result->fetch_assoc()['total'];
}

$result = $conn->query("SELECT COUNT(*) AS total FROM Farmers");
if ($result) {
    $total_farmers = $result->fetch_assoc()['total'];
}

$total_purchases = $total_batches;

$recent_sql = "SELECT b.batch_number, f.name AS farmer_name, p.product_name, b.quantity, b.unit, DATE_FORMAT(b.purchase_date, '%Y-%m-%d') AS purchase_date, q.quality_tag FROM Batches b JOIN Farmers f ON b.farmer_id=f.farmer_id JOIN Products p ON b.product_id=p.product_id LEFT JOIN Quality_Checks q ON b.batch_id=q.batch_id ORDER BY b.purchase_date DESC LIMIT 5";
$recent_result = $conn->query($recent_sql);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Field Supervisor Dashboard</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Field Supervisor Dashboard'; include '../components/header.html'; ?>

    <main>
      <div class="stats-grid">
        <div class="stat-card">
          <i class="fa fa-cart-plus"></i>
          <div class="value"><?php echo $total_purchases; ?></div>
          <div class="label">Total Purchases</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-users"></i>
          <div class="value"><?php echo $total_farmers; ?></div>
          <div class="label">Farmers</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-cube"></i>
          <div class="value"><?php echo $total_batches; ?></div>
          <div class="label">Batches Created</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-money"></i>
          <div class="value"><?php echo $total_earnings; ?></div>
          <div class="label">Total Spent (BDT)</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Quick Actions</span>
        </div>
        <div class="quick-actions">
          <a href="record_purchase.php" class="action-btn">
            <i class="fa fa-shopping-cart"></i>
            <span>Record Purchase</span>
          </a>
          <a href="batch_management.php" class="action-btn">
            <i class="fa fa-cube"></i>
            <span>Manage Batches</span>
          </a>
          <a href="farmer_list.php" class="action-btn">
            <i class="fa fa-users"></i>
            <span>Farmer List</span>
          </a>
          <a href="farmer_status.php" class="action-btn">
            <i class="fa fa-list-alt"></i>
            <span>Farmer Status</span>
          </a>
          <a href="farmer_history.php" class="action-btn">
            <i class="fa fa-history"></i>
            <span>Sales History</span>
          </a>

          <a href="add_farmer.php" class="action-btn">
            <i class="fa fa-user-plus"></i>
            <span>Add Farmer</span>
          </a>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Recent Purchases</span>
        </div>
        <table>
          <tr>
            <th>Batch ID</th>
            <th>Farmer</th>
            <th>Produce</th>
            <th>Quantity</th>
            <th>Date</th>
            <th>Status</th>
          </tr>
          <?php if ($recent_result && $recent_result->num_rows > 0) {
            while ($row = $recent_result->fetch_assoc()) {
              $status = $row['quality_tag'] ? 'Approved' : 'Pending';
              $status_class = $status === 'Approved' ? 'completed' : 'pending';
          ?>
          <tr>
            <td><?php echo htmlspecialchars($row['batch_number']); ?></td>
            <td><?php echo htmlspecialchars($row['farmer_name']); ?></td>
            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
            <td><?php echo htmlspecialchars($row['quantity'] . ' ' . $row['unit']); ?></td>
            <td><?php echo htmlspecialchars($row['purchase_date']); ?></td>
            <td><span class="status <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
          </tr>
          <?php }
          } else {
            echo '<tr><td colspan="6">No recent purchases available.</td></tr>';
          }
          $conn->close(); ?>
        </table>
      </div>
    </main>
  </body>
</html>
