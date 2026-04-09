<?php
include "../db.php";

$sql = "SELECT p.product_name, i.current_stock, i.last_updated FROM Inventory i JOIN Products p ON i.product_id=p.product_id ORDER BY p.product_name";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inventory Reports - Inventory Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Inventory Reports'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="fa fa-bar-chart"></i> Stock Summary by Product</span>
        </div>
        <table>
          <tr>
            <th><i class="fa fa-leaf"></i> Product</th>
            <th><i class="fa fa-cube"></i> Current Stock (kg)</th>
            <th><i class="fa fa-clock-o"></i> Last Updated</th>
            <th><i class="fa fa-info-circle"></i> Status</th>
            <th>Action</th>
          </tr>
          <?php if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $status = $row['current_stock'] < 500 ? 'Low Stock' : 'Good';
              $status_class = $status == 'Low Stock' ? 'warning' : 'success';
          ?>
          <tr>
            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
            <td><?php echo number_format($row['current_stock'], 2); ?></td>
            <td><?php echo $row['last_updated']; ?></td>
            <td><span class="status <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
            <td><a href="stock_update.php" class="btn btn-info btn-sm"><i class="fa fa-edit"></i> Update</a></td>
          </tr>
          <?php }
          } else {
            echo '<tr><td colspan="5" style="text-align:center; padding:20px;">No inventory records found.</td></tr>';
          } ?>
        </table>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="fa fa-calculator"></i> Inventory Statistics</span>
        </div>
        <?php
          $total_result = $conn->query("SELECT SUM(current_stock) AS total, COUNT(*) AS count FROM Inventory WHERE current_stock > 0");
          $stats = $total_result->fetch_assoc();
          $avg_result = $conn->query("SELECT AVG(current_stock) AS average FROM Inventory WHERE current_stock > 0");
          $avg = $avg_result->fetch_assoc();
          $low_result = $conn->query("SELECT COUNT(*) AS count FROM Inventory WHERE current_stock < 500");
          $low_count = $low_result->fetch_assoc();
        ?>
        <table>
          <tr>
            <th><i class="fa fa-info"></i> Metric</th>
            <th><i class="fa fa-hashtag"></i> Value</th>
          </tr>
          <tr>
            <td><strong>Total Stock</strong></td>
            <td><strong><?php echo number_format($stats['total'], 2); ?> kg</strong></td>
          </tr>
          <tr>
            <td>Active Products</td>
            <td><?php echo $stats['count']; ?></td>
          </tr>
          <tr>
            <td>Average Stock per Product</td>
            <td><?php echo number_format($avg['average'], 2); ?> kg</td>
          </tr>
          <tr>
            <td><span style="color: #f57c00;"><i class="fa fa-exclamation-triangle"></i> Low Stock Items</span></td>
            <td><span style="color: #f57c00; font-weight: bold;"><?php echo $low_count['count']; ?></span></td>
          </tr>
        </table>
      </div>
    </main>
  </body>
</html>

