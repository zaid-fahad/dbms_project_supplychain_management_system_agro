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

    <main>
      <div class="stats-grid">
        <div class="stat-card">
          <i class="fa fa-shopping-cart"></i>
          <div class="value">85</div>
          <div class="label">Total Orders</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-check-circle"></i>
          <div class="value">65</div>
          <div class="label">Completed</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-clock-o"></i>
          <div class="value">20</div>
          <div class="label">Pending</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-money"></i>
          <div class="value">1.2M</div>
          <div class="label">Total Sales (BDT)</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Quick Actions</span>
        </div>
        <div class="quick-actions">
          <a href="order_processing.html" class="action-btn">
            <i class="fa fa-shopping-cart"></i>
            <span>Process Orders</span>
          </a>
          <a href="sales_reports.html" class="action-btn">
            <i class="fa fa-bar-chart"></i>
            <span>Sales Reports</span>
          </a>
          <a href="customer_management.html" class="action-btn">
            <i class="fa fa-users"></i>
            <span>Customers</span>
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
            <th>Product</th>
            <th>Quantity</th>
            <th>Amount</th>
            <th>Action</th>
          </tr>
          <tr>
            <td>ORD-001</td>
            <td>Super Shop A</td>
            <td>Rice</td>
            <td>200 kg</td>
            <td>10,000 BDT</td>
            <td>
              <button
                class="btn btn-info"
                onclick="viewOrder('ORD-001', 'Super Shop A', 'Rice', '200 kg', '10,000 BDT', 'Pending')"
              >
                View
              </button>
            </td>
          </tr>
          <tr>
            <td>ORD-002</td>
            <td>Local Market</td>
            <td>Wheat</td>
            <td>150 kg</td>
            <td>7,500 BDT</td>
            <td>
              <button
                class="btn btn-info"
                onclick="viewOrder('ORD-002', 'Local Market', 'Wheat', '150 kg', '7,500 BDT', 'Pending')"
              >
                View
              </button>
            </td>
          </tr>
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
