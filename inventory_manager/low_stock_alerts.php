<?php
include "../db.php";

$low_stock_threshold = 500;
$sql = "SELECT p.product_id, p.product_name, i.current_stock, i.last_updated, ($low_stock_threshold - i.current_stock) AS shortage FROM Inventory i JOIN Products p ON i.product_id=p.product_id WHERE i.current_stock < $low_stock_threshold ORDER BY i.current_stock ASC";
$result = $conn->query($sql);

$total_low_stock = $result ? $result->num_rows : 0;
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Low Stock Alerts - Inventory Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
    <style>
      .alert-banner {
        background: linear-gradient(135deg, #f57c00 0%, #e65100 100%);
        color: white;
        padding: 20px;
        border-radius: 4px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
      }
      
      .alert-banner i {
        font-size: 32px;
      }
      
      .alert-banner-text h3 {
        margin: 0 0 5px 0;
      }
      
      .alert-banner-text p {
        margin: 0;
        font-size: 14px;
        opacity: 0.9;
      }
      
      .all-good {
        background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%);
        text-align: center;
        padding: 40px 20px;
      }
      
      .all-good i {
        font-size: 48px;
        display: block;
        margin-bottom: 10px;
      }
      
      .all-good h4 {
        margin: 10px 0 5px 0;
      }
      
      .all-good p {
        margin: 0;
        opacity: 0.9;
      }
      
      table tr:hover {
        background: #f9f9f9;
      }
      
      .shortage-critical {
        color: #c62828;
        font-weight: bold;
      }
      
      .shortage-mild {
        color: #f57c00;
        font-weight: bold;
      }
    </style>
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Low Stock Alerts'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <?php if ($total_low_stock > 0): ?>
      <div class="alert-banner">
        <i class="fa fa-exclamation-triangle"></i>
        <div class="alert-banner-text">
          <h3>Low Stock Alert</h3>
          <p><?php echo $total_low_stock; ?> product(s) below minimum threshold of <?php echo $low_stock_threshold; ?> kg</p>
        </div>
      </div>
      <?php else: ?>
      <div class="card alert-banner all-good">
        <i class="fa fa-check-circle"></i>
        <h4>All Items Well-Stocked!</h4>
        <p>No products are currently below the minimum threshold.</p>
      </div>
      <?php endif; ?>

      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="fa fa-exclamation-circle"></i> Items Below Minimum Threshold (<?php echo $low_stock_threshold; ?> kg)</span>
        </div>
        <table>
          <tr>
            <th><i class="fa fa-leaf"></i> Product</th>
            <th><i class="fa fa-cube"></i> Current Stock (kg)</th>
            <th><i class="fa fa-target"></i> Minimum (kg)</th>
            <th><i class="fa fa-minus-circle"></i> Shortage (kg)</th>
            <th><i class="fa fa-clock-o"></i> Last Updated</th>
            <th>Action</th>
          </tr>
          <?php if ($total_low_stock > 0) {
            while ($row = $result->fetch_assoc()) {
              $shortage_class = $row['shortage'] > 300 ? 'shortage-critical' : 'shortage-mild';
          ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($row['product_name']); ?></strong></td>
            <td><strong style="color: #f57c00;"><?php echo number_format($row['current_stock'], 2); ?></strong></td>
            <td><?php echo $low_stock_threshold; ?></td>
            <td><strong class="<?php echo $shortage_class; ?>">-<?php echo number_format($row['shortage'], 2); ?></strong></td>
            <td><small><?php echo $row['last_updated']; ?></small></td>
            <td><a href="stock_update.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Order More</a></td>
          </tr>
          <?php }
          } ?>
        </table>
      </div>

      <?php if ($total_low_stock > 0): ?>
      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="fa fa-lightbulb-o"></i> Quick Actions</span>
        </div>
        <div style="padding: 20px;">
          <p><strong>Recommended Actions:</strong></p>
          <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Review critical shortage items (highlighted in red)</li>
            <li>Prioritize orders for items with shortage > 300 kg</li>
            <li>Contact suppliers for urgent replenishment</li>
            <li>Update stock levels as new items arrive</li>
          </ul>
          <a href="stock_update.php" class="btn btn-primary" style="display: inline-block; margin-top: 10px;">
            <i class="fa fa-plus-square"></i> Add Stock Now
          </a>
        </div>
      </div>
      <?php endif; ?>
    </main>
  </body>
</html>

