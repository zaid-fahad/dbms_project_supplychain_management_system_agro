<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sales Reports - Sales Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Sales Reports'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="stats-grid">
        <div class="stat-card">
          <i class="fa fa-money"></i>
          <div class="value">2.5M</div>
          <div class="label">Total Sales (BDT)</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-shopping-cart"></i>
          <div class="value">450</div>
          <div class="label">Orders Processed</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-users"></i>
          <div class="value">85</div>
          <div class="label">Active Customers</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-trending-up"></i>
          <div class="value">+15%</div>
          <div class="label">Growth Rate</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Monthly Sales Report</span>
        </div>
        <div class="chart-placeholder">
          <i class="fa fa-bar-chart" style="font-size: 48px; color: #ccc;"></i>
          <p>Sales trend chart would be displayed here</p>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Top Selling Products</span>
        </div>
        <table>
          <tr>
            <th>Product</th>
            <th>Units Sold</th>
            <th>Revenue</th>
            <th>Growth</th>
          </tr>
          <tr>
            <td>Rice</td>
            <td>2,500 kg</td>
            <td>125,000 BDT</td>
            <td><span style="color: green">+8%</span></td>
          </tr>
          <tr>
            <td>Wheat</td>
            <td>1,800 kg</td>
            <td>81,000 BDT</td>
            <td><span style="color: green">+12%</span></td>
          </tr>
          <tr>
            <td>Potatoes</td>
            <td>1,200 kg</td>
            <td>30,000 BDT</td>
            <td><span style="color: red">-3%</span></td>
          </tr>
          <tr>
            <td>Tomatoes</td>
            <td>950 kg</td>
            <td>38,000 BDT</td>
            <td><span style="color: green">+18%</span></td>
          </tr>
        </table>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Customer Performance</span>
        </div>
        <table>
          <tr>
            <th>Customer</th>
            <th>Total Orders</th>
            <th>Total Value</th>
            <th>Last Order</th>
          </tr>
          <tr>
            <td>Super Shop A</td>
            <td>25</td>
            <td>450,000 BDT</td>
            <td>2024-01-15</td>
          </tr>
          <tr>
            <td>Local Market B</td>
            <td>18</td>
            <td>320,000 BDT</td>
            <td>2024-01-14</td>
          </tr>
          <tr>
            <td>Restaurant C</td>
            <td>32</td>
            <td>280,000 BDT</td>
            <td>2024-01-13</td>
          </tr>
        </table>
      </div>
    </main>
  </body>
</html>
