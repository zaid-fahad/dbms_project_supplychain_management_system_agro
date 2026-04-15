<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sales Manager Dashboard</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Sales Manager Dashboard'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <?php
    $message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['order_id'], $_POST['order_type']) && $_POST['action'] === 'process_order') {
        $orderId = intval($_POST['order_id']);
        $orderType = $_POST['order_type'];
        $table = $orderType === 'shop' ? 'SuperShop_Orders' : 'Orders';
        try {
            $conn = new mysqli('localhost', 'root', '', 'dbms_scms');
            if (!$conn->connect_error) {
                $stmt = $conn->prepare("UPDATE $table SET status='Processing' WHERE order_id = ?");
                $stmt->bind_param('i', $orderId);
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    $message = "Order " . ($orderType === 'shop' ? 'SHOP' : 'ORD') . "-$orderId has been moved to Processing.";
                } else {
                    $message = "Order " . ($orderType === 'shop' ? 'SHOP' : 'ORD') . "-$orderId could not be updated.";
                }
                $stmt->close();
                $conn->close();
            } else {
                $message = 'Database connection failed while processing order.';
            }
        } catch (Exception $e) {
            $message = 'Unable to update order due to database error.';
        }
    }

    // Try database connection, fallback to dummy data if connection fails
    $dbConnected = false;
    $totalOrders = 85;
    $completedOrders = 65;
    $pendingOrders = 20;
    $totalSales = 1200000;
    $pendingOrdersData = [];

    try {
        $conn = new mysqli('localhost', 'root', '', 'dbms_scms');
        if (!$conn->connect_error) {
            $dbConnected = true;

            $totalOrders = intval($conn->query("SELECT COUNT(*) as count FROM Orders")->fetch_assoc()['count']);
            $completedOrders = intval($conn->query("SELECT COUNT(*) as count FROM Orders WHERE status = 'Delivered'")->fetch_assoc()['count']);
            $pendingOrders = intval($conn->query("SELECT COUNT(*) as count FROM Orders WHERE status = 'Pending'")->fetch_assoc()['count']);
            $totalSales = floatval($conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM Orders WHERE status = 'Delivered'")->fetch_assoc()['total']);

            $pendingOrdersQuery = $conn->query("SELECT o.order_id, c.customer_name, o.total_amount, o.status, o.order_date FROM Orders o LEFT JOIN Customers c ON o.customer_id = c.customer_id WHERE o.status = 'Pending' ORDER BY o.order_date DESC LIMIT 5");

            if ($pendingOrdersQuery) {
                while ($row = $pendingOrdersQuery->fetch_assoc()) {
                    $pendingOrdersData[] = $row;
                }
            }

            $conn->close();
        }
    } catch (Exception $e) {
        $dbConnected = false;
        $pendingOrdersData = [
            ['order_id' => 1, 'customer_name' => 'Super Shop A', 'total_amount' => 10000, 'order_date' => '2024-01-15', 'status' => 'Pending'],
            ['order_id' => 2, 'customer_name' => 'Local Market B', 'total_amount' => 7500, 'order_date' => '2024-01-14', 'status' => 'Pending'],
            ['order_id' => 3, 'customer_name' => 'Restaurant C', 'total_amount' => 5000, 'order_date' => '2024-01-13', 'status' => 'Pending']
        ];
    }
    ?>

    <main>
      <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>

      <form id="processForm" method="post" style="display:none;">
        <input type="hidden" name="action" value="process_order" />
        <input type="hidden" id="processOrderId" name="order_id" value="" />
        <input type="hidden" id="processOrderType" name="order_type" value="" />
      </form>
      <div class="stats-grid">
        <div class="stat-card">
          <i class="fa fa-shopping-cart"></i>
          <div class="value"><?php echo $totalOrders; ?></div>
          <div class="label">Total Orders</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-check-circle"></i>
          <div class="value"><?php echo $completedOrders; ?></div>
          <div class="label">Completed</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-clock-o"></i>
          <div class="value"><?php echo $pendingOrders; ?></div>
          <div class="label">Pending</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-money"></i>
          <div class="value"><?php echo number_format($totalSales, 0); ?>K</div>
          <div class="label">Total Sales (BDT)</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Quick Actions</span>
        </div>
        <div class="quick-actions">
          <a href="order_processing.php" class="action-btn">
            <i class="fa fa-shopping-cart"></i>
            <span>Process Orders</span>
          </a>
          <a href="sales_reports.php" class="action-btn">
            <i class="fa fa-bar-chart"></i>
            <span>Sales Reports</span>
          </a>
          <a href="customer_management.php" class="action-btn">
            <i class="fa fa-users"></i>
            <span>Customers</span>
          </a>
          <a href="market_orders.php" class="action-btn">
            <i class="fa fa-shopping-basket"></i>
            <span>Market Orders</span>
          </a>
          <a href="shop_orders.php" class="action-btn">
            <i class="fa fa-store"></i>
            <span>Shop Orders</span>
          </a>
          <a href="demand_forecast.php" class="action-btn">
            <i class="fa fa-line-chart"></i>
            <span>Demand Forecast</span>
          </a>
          <a href="price_trends.php" class="action-btn">
            <i class="fa fa-bar-chart"></i>
            <span>Price Trends</span>
          </a>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Pending Orders</span>
        </div>
        <table>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Order Date</th>
            <th>Action</th>
          </tr>
          <?php
          if (!empty($pendingOrdersData)) {
              foreach($pendingOrdersData as $order) {
                  echo "<tr>
                          <td>ORD-{$order['order_id']}</td>
                          <td>{$order['customer_name']}</td>
                          <td>" . number_format($order['total_amount'], 0) . " BDT</td>
                          <td><span class='status " . strtolower(str_replace(' ', '-', $order['status'])) . "'>{$order['status']}</span></td>
                          <td>" . date('Y-m-d', strtotime($order['order_date'])) . "</td>
                          <td>
                            <button class='btn btn-info' onclick=\"viewOrder('ORD-{$order['order_id']}', '{$order['customer_name']}', '" . number_format($order['total_amount'], 0) . " BDT', '{$order['status']}')\">
                              View
                            </button>
                          </td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='6'>No pending orders</td></tr>";
          }
          ?>
        </table>
      </div>
    </main>

    <div class="modal" id="detailsModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Order Details</h3>
          <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div id="modalBody"></div>
      </div>
    </div>

    <script>
      function viewOrder(orderId, customer, amount, status) {
        const isPending = status === 'Pending';
        document.getElementById("modalBody").innerHTML = `
                <div class="detail-row">
                    <span class="detail-label">Order ID:</span>
                    <span class="detail-value">${orderId}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Customer:</span>
                    <span class="detail-value">${customer}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount:</span>
                    <span class="detail-value">${amount}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value"><span class="status ${
                      status === "Pending" ? "pending" : "completed"
                    }">${status}</span></span>
                </div>
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    ${isPending ? '<button class="btn btn-primary" onclick="processOrder(\'' + orderId + '\', \'' + status + '\')">Process</button>' : ''}
                    <button class="btn btn-danger" onclick="closeModal()">Close</button>
                </div>
            `;
        document.getElementById("detailsModal").classList.add("active");
      }

      function processOrder(orderId, status) {
        if (status === 'Pending') {
          if (confirm('Process order ' + orderId + '?')) {
            const id = orderId.replace('ORD-', '');
            document.getElementById('processOrderId').value = id;
            document.getElementById('processOrderType').value = 'market';
            document.getElementById('processForm').submit();
          }
        }
      }

      function closeModal() {
        document.getElementById("detailsModal").classList.remove("active");
      }
    </script>
  </body>
</html>

    <script>
      function viewOrder(orderId, customer, product, quantity, amount, status) {
        document.getElementById("modalBody").innerHTML = `
                <div class="detail-row">
                    <span class="detail-label">Order ID:</span>
                    <span class="detail-value">${orderId}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Customer:</span>
                    <span class="detail-value">${customer}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Product:</span>
                    <span class="detail-value">${product}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Quantity:</span>
                    <span class="detail-value">${quantity}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount:</span>
                    <span class="detail-value">${amount}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value"><span class="status ${
                      status === "Pending" ? "pending" : "completed"
                    }">${status}</span></span>
                </div>
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button class="btn btn-primary">Process</button>
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
