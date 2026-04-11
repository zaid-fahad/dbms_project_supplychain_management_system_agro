<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Local Market Dashboard</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Local Market Dashboard'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="stats-grid">
        <div class="stat-card">
          <i class="fa fa-cart-arrow-down"></i>
          <div class="value">60</div>
          <div class="label">Total Orders</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-check-circle"></i>
          <div class="value">55</div>
          <div class="label">Fulfilled</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-clock-o"></i>
          <div class="value">5</div>
          <div class="label">Pending</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-money"></i>
          <div class="value">750,000</div>
          <div class="label">Total Value</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Quick Actions</span>
        </div>
        <div class="quick-actions">
          <a href="market_orders.html" class="action-btn">
            <i class="fa fa-cart-arrow-down"></i>
            <span>Market Orders</span>
          </a>
          <a href="demand_forecast.html" class="action-btn">
            <i class="fa fa-line-chart"></i>
            <span>Demand Forecast</span>
          </a>
          <a href="price_trends.html" class="action-btn">
            <i class="fa fa-bar-chart"></i>
            <span>Price Trends</span>
          </a>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Market Orders</span>
        </div>
        <table>
          <tr>
            <th>Order ID</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price/kg</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
          <tr>
            <td>LM-001</td>
            <td>Rice</td>
            <td>500 kg</td>
            <td>50 BDT</td>
            <td><span class="status completed">Fulfilled</span></td>
            <td>
              <button
                class="btn btn-info"
                onclick="viewOrder('LM-001', 'Rice', '500 kg', '50 BDT', 'Fulfilled')"
              >
                View
              </button>
            </td>
          </tr>
          <tr>
            <td>LM-002</td>
            <td>Potatoes</td>
            <td>300 kg</td>
            <td>25 BDT</td>
            <td><span class="status pending">Pending</span></td>
            <td>
              <button
                class="btn btn-info"
                onclick="viewOrder('LM-002', 'Potatoes', '300 kg', '25 BDT', 'Pending')"
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
      function viewOrder(orderId, product, quantity, pricePerKg, status) {
        document.getElementById("modalBody").innerHTML = `
                <div class="detail-row">
                    <span class="detail-label">Order ID:</span>
                    <span class="detail-value">${orderId}</span>
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
                    <span class="detail-label">Price/kg:</span>
                    <span class="detail-value">${pricePerKg} BDT</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value"><span class="status ${
                      status === "Fulfilled" ? "completed" : "pending"
                    }">${status}</span></span>
                </div>
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <a href="market_orders.html" class="btn btn-primary">Place Order</a>
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
