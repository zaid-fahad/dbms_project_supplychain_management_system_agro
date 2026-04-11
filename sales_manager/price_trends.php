<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Price Trends - Sales Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Price Trends'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <?php
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'dbms_scms');
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    // Fetch price trends
    $sql = "SELECT * FROM Price_Trends ORDER BY product_name";
    $result = $conn->query($sql);
    ?>

    <main>
      <div class="card">
        <div class="card-header">
          <span class="card-title">Current Market Prices</span>
        </div>
        <table>
          <tr>
            <th>Product</th>
            <th>Current Price</th>
            <th>Last Week</th>
            <th>Last Month</th>
            <th>Trend</th>
          </tr>
          <?php
          if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                  $trendColor = '';
                  $trendIcon = '';
                  switch($row['trend']) {
                      case 'Rising':
                          $trendColor = 'green';
                          $trendIcon = 'fa-arrow-up';
                          break;
                      case 'Falling':
                          $trendColor = 'red';
                          $trendIcon = 'fa-arrow-down';
                          break;
                      case 'Stable':
                          $trendColor = 'orange';
                          $trendIcon = 'fa-minus';
                          break;
                  }
                  echo "<tr>
                          <td>{$row['product_name']}</td>
                          <td>{$row['current_price']} BDT/kg</td>
                          <td>{$row['last_week_price']} BDT/kg</td>
                          <td>{$row['last_month_price']} BDT/kg</td>
                          <td>
                            <span style='color: {$trendColor}'>
                              <i class='fa {$trendIcon}'></i> {$row['trend']}
                            </span>
                          </td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='5'>No price trend data found</td></tr>";
          }
          ?>
        </table>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Price History (Last 3 Months)</span>
        </div>
        <div class="chart-placeholder">
          <i class="fa fa-bar-chart" style="font-size: 48px; color: #ccc;"></i>
          <p>Interactive price trend chart would be displayed here</p>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Market Insights</span>
        </div>
        <div class="insights">
          <div class="insight-item">
            <i class="fa fa-lightbulb-o"></i>
            <div>
              <strong>Rice prices are trending upward</strong> due to increased demand during Ramadan season.
            </div>
          </div>
          <div class="insight-item">
            <i class="fa fa-lightbulb-o"></i>
            <div>
              <strong>Potato prices are falling</strong> due to good harvest yields this season.
            </div>
          </div>
          <div class="insight-item">
            <i class="fa fa-lightbulb-o"></i>
            <div>
              <strong>Consider bulk purchasing</strong> of tomatoes as prices are expected to rise further.
            </div>
          </div>
        </div>
      </div>
    </main>

    <style>
      .chart-placeholder {
        text-align: center;
        padding: 50px;
        background: #f9f9f9;
        border-radius: 8px;
        margin: 20px 0;
      }
      .insights {
        display: flex;
        flex-direction: column;
        gap: 15px;
      }
      .insight-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 15px;
        background: #f0f8ff;
        border-radius: 8px;
        border-left: 4px solid #007bff;
      }
      .insight-item i {
        color: #007bff;
        margin-top: 2px;
      }
    </style>
  </body>
</html>