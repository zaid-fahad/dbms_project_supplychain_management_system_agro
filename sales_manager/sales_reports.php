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

  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Sales Reports'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <?php
    // Try database connection, fallback to dummy data if connection fails
    $dbConnected = false;
    $totalOrders = 85;
    $completedOrders = 65;
    $totalSales = 1200000;
    $activeCustomers = 68;
    $topProducts = [];
    $customerPerformance = [];

    try {
        $conn = new mysqli('localhost', 'root', '', 'dbms_scms');
        if (!$conn->connect_error) {
            $dbConnected = true;

            // Get total sales statistics
            $totalOrders = $conn->query("SELECT COUNT(*) as count FROM Orders")->fetch_assoc()['count'];
            $completedOrders = $conn->query("SELECT COUNT(*) as count FROM Orders WHERE status = 'Completed'")->fetch_assoc()['count'];
            $totalSales = $conn->query("SELECT SUM(total_amount) as total FROM Orders WHERE status = 'Completed'")->fetch_assoc()['total'];
            $activeCustomers = $conn->query("SELECT COUNT(DISTINCT customer_id) as count FROM Orders")->fetch_assoc()['count'];

            // Get top selling products
            $topProductsQuery = $conn->query("
                SELECT p.name, SUM(o.quantity) as total_quantity, SUM(o.total_amount) as total_revenue
                FROM Orders o
                JOIN Products p ON o.product_id = p.id
                WHERE o.status = 'Completed'
                GROUP BY p.id, p.name
                ORDER BY total_revenue DESC
                LIMIT 5
            ");

            // Get customer performance
            $customerPerfQuery = $conn->query("
                SELECT c.name, COUNT(o.id) as order_count, SUM(o.total_amount) as total_spent
                FROM Orders o
                JOIN Customers c ON o.customer_id = c.id
                WHERE o.status = 'Completed'
                GROUP BY c.id, c.name
                ORDER BY total_spent DESC
                LIMIT 5
            ");

            if ($topProductsQuery) {
                while($row = $topProductsQuery->fetch_assoc()) {
                    $topProducts[] = $row;
                }
            }

            if ($customerPerfQuery) {
                while($row = $customerPerfQuery->fetch_assoc()) {
                    $customerPerformance[] = $row;
                }
            }

            $conn->close();
        }
    } catch (Exception $e) {
        // Database connection failed, use dummy data
        $dbConnected = false;
        $topProducts = [
            ['name' => 'Rice', 'total_quantity' => 2500, 'total_revenue' => 125000],
            ['name' => 'Wheat', 'total_quantity' => 1800, 'total_revenue' => 90000],
            ['name' => 'Potatoes', 'total_quantity' => 1200, 'total_revenue' => 60000],
            ['name' => 'Tomatoes', 'total_quantity' => 800, 'total_revenue' => 40000],
            ['name' => 'Onions', 'total_quantity' => 600, 'total_revenue' => 30000]
        ];

        $customerPerformance = [
            ['name' => 'Super Shop A', 'order_count' => 25, 'total_spent' => 450000],
            ['name' => 'Local Market B', 'order_count' => 18, 'total_spent' => 320000],
            ['name' => 'Restaurant C', 'order_count' => 32, 'total_spent' => 280000],
            ['name' => 'Wholesale D', 'order_count' => 8, 'total_spent' => 180000],
            ['name' => 'Hotel E', 'order_count' => 15, 'total_spent' => 150000]
        ];
    }
    ?>
        SELECT c.name, COUNT(o.id) as order_count, SUM(o.total_amount) as total_value, MAX(o.order_date) as last_order
        FROM Customers c
        LEFT JOIN Orders o ON c.id = o.customer_id
        GROUP BY c.id, c.name
        ORDER BY total_value DESC
        LIMIT 5
    ?>

    <main>
      <div class="stats-grid">
        <div class="stat-card">
          <i class="fa fa-money"></i>
          <div class="value"><?php echo number_format($totalSales, 0); ?> BDT</div>
          <div class="label">Total Sales</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-shopping-cart"></i>
          <div class="value"><?php echo $totalOrders; ?></div>
          <div class="label">Orders Processed</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-users"></i>
          <div class="value"><?php echo $activeCustomers; ?></div>
          <div class="label">Active Customers</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-trending-up"></i>
          <div class="value"><?php echo $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0; ?>%</div>
          <div class="label">Completion Rate</div>
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
            <th>Contribution</th>
          </tr>
          <?php
          if (!empty($topProducts)) {
              foreach($topProducts as $product) {
                  $contribution = $totalSales > 0 ? round(($product['total_revenue'] / $totalSales) * 100, 1) : 0;
                  echo "<tr>
                          <td>{$product['name']}</td>
                          <td>{$product['total_quantity']} kg</td>
                          <td>" . number_format($product['total_revenue'], 0) . " BDT</td>
                          <td>{$contribution}%</td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='4'>No sales data available</td></tr>";
          }
          ?>
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
          <?php
          if (!empty($customerPerformance)) {
              foreach($customerPerformance as $customer) {
                  echo "<tr>
                          <td>{$customer['name']}</td>
                          <td>{$customer['order_count']}</td>
                          <td>" . number_format($customer['total_spent'], 0) . " BDT</td>
                          <td>Recent</td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='4'>No customer data available</td></tr>";
          }
          ?>
        </table>
      </div>
    </main>

    <?php $conn->close(); ?>
  </body>
</html>
