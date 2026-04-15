<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Demand Forecast - Sales Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Demand Forecast'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <?php
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'dbms_scms');
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    // Fetch demand forecast data
    $sql = "SELECT * FROM Demand_Forecast ORDER BY product_name";
    $result = $conn->query($sql);

    // Calculate summary stats
    $totalProjectedDemand = 0;
    $lowStockCount = 0;
    if ($result->num_rows > 0) {
        $result->data_seek(0); // Reset result pointer
        while($row = $result->fetch_assoc()) {
            $totalProjectedDemand += $row['monthly_forecast'];
            if ($row['status'] == 'Low Stock') {
                $lowStockCount++;
            }
        }
        $result->data_seek(0); // Reset result pointer again
    }
    ?>

    <main>
      <div class="card">
        <div class="card-header">
          <span class="card-title">Demand Forecast Analysis</span>
        </div>
        <div class="stats-grid">
          <div class="stat-card">
            <i class="fa fa-line-chart"></i>
            <div class="value">85%</div>
            <div class="label">Forecast Accuracy</div>
          </div>
          <div class="stat-card">
            <i class="fa fa-calendar"></i>
            <div class="value">30 Days</div>
            <div class="label">Prediction Period</div>
          </div>
          <div class="stat-card">
            <i class="fa fa-trending-up"></i>
            <div class="value">+12%</div>
            <div class="label">Expected Growth</div>
          </div>
          <div class="stat-card">
            <i class="fa fa-shopping-cart"></i>
            <div class="value"><?php echo number_format($totalProjectedDemand); ?> kg</div>
            <div class="label">Projected Demand</div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Product Demand Forecast</span>
        </div>
        <table>
          <tr>
            <th>Product</th>
            <th>Current Stock</th>
            <th>Weekly Demand</th>
            <th>Monthly Forecast</th>
            <th>Recommended Stock</th>
            <th>Status</th>
          </tr>
          <?php
          if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                  $statusClass = strtolower(str_replace(' ', '-', $row['status']));
                  echo "<tr>
                          <td>{$row['product_name']}</td>
                          <td>{$row['current_stock']} kg</td>
                          <td>{$row['weekly_demand']} kg</td>
                          <td>{$row['monthly_forecast']} kg</td>
                          <td>{$row['recommended_stock']} kg</td>
                          <td><span class='status {$statusClass}'>{$row['status']}</span></td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='6'>No demand forecast data found</td></tr>";
          }
          ?>
        </table>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Seasonal Trends</span>
        </div>
        <div class="chart-placeholder">
          <i class="fa fa-line-chart" style="font-size: 48px; color: #ccc;"></i>
          <p>Interactive demand forecast chart would be displayed here</p>
        </div>
      </div>
    </main>

    <?php $conn->close(); ?>

  </body>
</html>