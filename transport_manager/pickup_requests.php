<?php
include "../db.php";

$sql = "SELECT o.order_id, c.customer_name, c.address, o.order_date, o.status, o.total_amount 
    FROM Orders o 
    LEFT JOIN Customers c ON o.customer_id=c.customer_id
    WHERE o.status IN ('Pending', 'Verified', 'Processing')
    ORDER BY o.order_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pickup Requests - Transport Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Pickup Requests'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card">
        <div class="card-header">
          <span class="card-title"><i class="fa fa-truck"></i> Pending Pickup Requests</span>
        </div>
        <table>
          <tr>
            <th><i class="fa fa-hashtag"></i> Order ID</th>
            <th><i class="fa fa-store"></i> Customer</th>
            <th><i class="fa fa-map-marker"></i> Address</th>
            <th><i class="fa fa-info-circle"></i> Status</th>
            <th><i class="fa fa-money"></i> Amount</th>
            <th><i class="fa fa-calendar"></i> Date</th>
            <th>Action</th>
          </tr>
          <?php if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $status = $row['status'];
              $status_class = ($status == 'Processing') ? 'in-transit' : 'pending';
          ?>
          <tr>
            <td><strong><?php echo $row['order_id']; ?></strong></td>
            <td><?php echo htmlspecialchars($row['customer_name'] ?? 'Unknown'); ?></td>
            <td><?php echo htmlspecialchars($row['address'] ?? 'N/A'); ?></td>
            <td><span class="status <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
            <td><?php echo $row['total_amount'] ? '৳ ' . number_format($row['total_amount'], 2) : 'N/A'; ?></td>
            <td><?php echo date('Y-m-d', strtotime($row['order_date'])); ?></td>
            <td>
              <a href="delivery_orders.php" class="btn btn-primary btn-sm"><i class="fa fa-check"></i> Assign Pickup</a>
            </td>
          </tr>
          <?php }
          } else {
            echo '<tr><td colspan="7" style="text-align:center; padding:20px; color: #4caf50;"><i class="fa fa-check-circle"></i> No pending requests!</td></tr>';
          } ?>
        </table>
      </div>
    </main>
  </body>
</html>

