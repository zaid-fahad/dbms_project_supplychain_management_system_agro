<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Customer Management - Sales Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Customer Management'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <?php
    $dbConnected = false;
    $customers = [];
    $totalCustomers = 0;
    $activeCustomers = 0;
    $newCustomers = 0;
    $message = '';

    try {
        $conn = new mysqli('localhost', 'root', '', 'dbms_scms');
        if (!$conn->connect_error) {
            $dbConnected = true;

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
                $customerName = $conn->real_escape_string(trim($_POST['customer_name'] ?? ''));
                $customerType = $conn->real_escape_string(trim($_POST['customer_type'] ?? ''));
                $address = $conn->real_escape_string(trim($_POST['address'] ?? ''));

                if ($_POST['action'] === 'add_customer') {
                    $stmt = $conn->prepare("INSERT INTO Customers (customer_name, customer_type, address) VALUES (?, ?, ?)");
                    $stmt->bind_param('sss', $customerName, $customerType, $address);
                    if ($stmt->execute()) {
                        $stmt->close();
                        header('Location: customer_management.php');
                        exit;
                    }
                    $message = 'Unable to add customer. Please try again.';
                    $stmt->close();
                }

                if ($_POST['action'] === 'update_customer' && isset($_POST['customer_id'])) {
                    $customerId = intval($_POST['customer_id']);
                    $stmt = $conn->prepare("UPDATE Customers SET customer_name = ?, customer_type = ?, address = ? WHERE customer_id = ?");
                    $stmt->bind_param('sssi', $customerName, $customerType, $address, $customerId);
                    if ($stmt->execute()) {
                        $stmt->close();
                        header('Location: customer_management.php');
                        exit;
                    }
                    $message = 'Unable to update customer. Please try again.';
                    $stmt->close();
                }
            }

            $sql = "SELECT c.customer_id, c.customer_name, c.customer_type, c.address,
                           COUNT(o.order_id) AS total_orders,
                           COALESCE(SUM(o.total_amount), 0) AS total_value,
                           MAX(o.order_date) AS last_order_date
                    FROM Customers c
                    LEFT JOIN Orders o ON c.customer_id = o.customer_id
                    GROUP BY c.customer_id
                    ORDER BY c.customer_name";
            $result = $conn->query($sql);

            $totalCustomers = intval($conn->query("SELECT COUNT(*) AS count FROM Customers")->fetch_assoc()['count']);
            $activeCustomers = intval($conn->query("SELECT COUNT(DISTINCT c.customer_id) AS count FROM Customers c JOIN Orders o ON c.customer_id = o.customer_id WHERE o.order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch_assoc()['count']);
            $newCustomers = intval($conn->query("SELECT COUNT(*) AS count FROM (SELECT customer_id FROM Orders GROUP BY customer_id HAVING MIN(order_date) >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS recent_customers")->fetch_assoc()['count']);

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $customers[] = $row;
                }
            }

            $conn->close();
        }
    } catch (Exception $e) {
        $dbConnected = false;
    }

    if (!$dbConnected) {
        $customers = [
            ['customer_id' => 1, 'customer_name' => 'Super Shop A', 'customer_type' => 'Super Shop', 'address' => '+880 1712-345678', 'total_orders' => 25, 'total_value' => 450000, 'last_order_date' => '2024-01-10'],
            ['customer_id' => 2, 'customer_name' => 'Local Market B', 'customer_type' => 'Local Market', 'address' => '+880 1812-345678', 'total_orders' => 18, 'total_value' => 320000, 'last_order_date' => '2024-01-08'],
            ['customer_id' => 3, 'customer_name' => 'Restaurant C', 'customer_type' => 'Local Market', 'address' => '+880 1912-345678', 'total_orders' => 32, 'total_value' => 280000, 'last_order_date' => '2024-01-12'],
            ['customer_id' => 4, 'customer_name' => 'Wholesale D', 'customer_type' => 'Super Shop', 'address' => '+880 1512-345678', 'total_orders' => 8, 'total_value' => 180000, 'last_order_date' => '2023-12-15']
        ];
        $totalCustomers = 85;
        $activeCustomers = 68;
        $newCustomers = 12;
    }
    ?>

    <main>
      <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>
      <div class="card">
        <div class="card-header">
          <span class="card-title">Customer Directory</span>
          <button class="btn btn-primary" onclick="openCustomerModal('add')">
            <i class="fa fa-plus"></i> Add Customer
          </button>
        </div>
        <table>
          <tr>
            <th>Customer ID</th>
            <th>Name</th>
            <th>Type</th>
            <th>Contact</th>
            <th>Total Orders</th>
            <th>Total Value</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
          <?php
          if (!empty($customers)) {
              foreach($customers as $row) {
                  $status = 'Active';
                  $statusClass = 'good';
                  if (!empty($row['last_order_date']) && strtotime($row['last_order_date']) < strtotime('-90 days')) {
                      $status = 'Inactive';
                      $statusClass = 'warning';
                  }
                  $name = htmlspecialchars($row['customer_name'], ENT_QUOTES);
                  $type = htmlspecialchars($row['customer_type'], ENT_QUOTES);
                  $address = htmlspecialchars($row['address'], ENT_QUOTES);
                  $customerIdJs = json_encode($row['customer_id']);
                  $customerNameJs = json_encode($row['customer_name']);
                  $customerTypeJs = json_encode($row['customer_type']);
                  $customerAddressJs = json_encode($row['address']);
                  echo "<tr>
                          <td>CUST-{$row['customer_id']}</td>
                          <td>{$name}</td>
                          <td>{$type}</td>
                          <td>{$address}</td>
                          <td>{$row['total_orders']}</td>
                          <td>" . number_format($row['total_value'], 0) . " BDT</td>
                          <td><span class='status {$statusClass}'>{$status}</span></td>
                          <td>
                            <button class='btn btn-warning btn-sm' onclick='openCustomerModal(\"edit\", {$customerIdJs}, {$customerNameJs}, {$customerTypeJs}, {$customerAddressJs})'>
                              <i class='fa fa-edit'></i>
                            </button>
                          </td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='8'>No customers found</td></tr>";
          }
          ?>
        </table>
      </div>

      <div id="customerModal" class="modal">
        <div class="modal-content">
          <span class="close" onclick="closeCustomerModal()">&times;</span>
          <h2 id="customerModalTitle">Add Customer</h2>
          <form id="customerForm" method="post">
            <input type="hidden" name="action" id="customerAction" value="add_customer" />
            <input type="hidden" name="customer_id" id="customerId" value="" />

            <label for="customer_name">Customer Name</label>
            <input type="text" id="customer_name" name="customer_name" required />

            <label for="customer_type">Customer Type</label>
            <select id="customer_type" name="customer_type" required>
              <option value="Super Shop">Super Shop</option>
              <option value="Local Market">Local Market</option>
            </select>

            <label for="address">Address / Contact</label>
            <textarea id="address" name="address" rows="3" required></textarea>

            <button type="submit" class="btn btn-primary">Save Customer</button>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Customer Insights</span>
        </div>
        <div class="stats-grid">
          <div class="stat-card">
            <i class="fa fa-users"></i>
            <div class="value"><?php echo $totalCustomers; ?></div>
            <div class="label">Total Customers</div>
          </div>
          <div class="stat-card">
            <i class="fa fa-user-plus"></i>
            <div class="value"><?php echo $newCustomers; ?></div>
            <div class="label">New This Month</div>
          </div>
          <div class="stat-card">
            <i class="fa fa-star"></i>
            <div class="value"><?php echo $activeCustomers; ?></div>
            <div class="label">Active Customers</div>
          </div>
          <div class="stat-card">
            <i class="fa fa-user-times"></i>
            <div class="value"><?php echo $totalCustomers - $activeCustomers; ?></div>
            <div class="label">Inactive Customers</div>
          </div>
        </div>
      </div>
    </main>

    <script>
      function openCustomerModal(mode, customerId = '', name = '', type = 'Super Shop', address = '') {
        document.getElementById('customerModalTitle').textContent = mode === 'edit' ? 'Edit Customer' : 'Add Customer';
        document.getElementById('customerAction').value = mode === 'edit' ? 'update_customer' : 'add_customer';
        document.getElementById('customerId').value = customerId;
        document.getElementById('customer_name').value = name;
        document.getElementById('customer_type').value = type;
        document.getElementById('address').value = address;
        document.getElementById('customerModal').style.display = 'block';
      }

      function closeCustomerModal() {
        document.getElementById('customerModal').style.display = 'none';
      }

      window.onclick = function(event) {
        var modal = document.getElementById('customerModal');
        if (event.target == modal) {
          closeCustomerModal();
        }
      }
    </script>

    <style>
      .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
      }

      .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
      }

      .modal-content {
        background-color: #fff;
        margin: 10% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 90%;
        max-width: 500px;
        border-radius: 8px;
      }

      .modal-content label {
        display: block;
        margin-top: 12px;
        margin-bottom: 6px;
      }

      .modal-content input,
      .modal-content select,
      .modal-content textarea {
        width: 100%;
        padding: 8px;
        margin-bottom: 12px;
        border: 1px solid #ccc;
        border-radius: 4px;
      }

      .modal-content .btn {
        width: 100%;
      }
    </style>
  </body>
</html>
