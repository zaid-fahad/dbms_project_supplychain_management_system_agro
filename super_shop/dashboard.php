<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Super Shop Dashboard</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <div class="topbar">
      <div class="topbar-users">
        <a href="../farmer/dashboard.html" class="topbar-user">
          <i class="fa fa-leaf"></i>
          <span>Farmer</span>
        </a>
        <a href="../field_supervisor/dashboard.html" class="topbar-user">
          <i class="fa fa-truck"></i>
          <span>Field Supervisor</span>
        </a>
        <a href="../quality_officer/dashboard.html" class="topbar-user">
          <i class="fa fa-check-square-o"></i>
          <span>Quality Officer</span>
        </a>
        <a href="../inventory_manager/dashboard.html" class="topbar-user">
          <i class="fa fa-cubes"></i>
          <span>Inventory</span>
        </a>
        <a href="../sales_manager/dashboard.html" class="topbar-user">
          <i class="fa fa-shopping-cart"></i>
          <span>Sales</span>
        </a>
        <a href="../transport_manager/dashboard.html" class="topbar-user">
          <i class="fa fa-car"></i>
          <span>Transport</span>
        </a>
        <a href="../driver/dashboard.html" class="topbar-user">
          <i class="fa fa-road"></i>
          <span>Driver</span>
        </a>
        <a href="../super_shop/dashboard.html" class="topbar-user active">
          <i class="fa fa-shopping-bag"></i>
          <span>Super Shop</span>
        </a>
        <a href="../local_market/dashboard.html" class="topbar-user">
          <i class="fa fa-cart-arrow-down"></i>
          <span>Local Market</span>
        </a>
      </div>
    </div>

    <header>
      <div class="header-left">
        <img src="../logo.png" alt="Logo" class="logo" />
        <span class="title">Super Shop Dashboard</span>
      </div>
      <a href="../index.html" class="back-btn"
        ><i class="fa fa-arrow-left"></i> Back</a
      >
    </header>

    <!-- <nav>
        <a href="place_order.html"><i class="fa fa-cart-plus"></i> Place Order</a>
        <a href="order_status.html"><i class="fa fa-list-alt"></i> Order Status</a>
        <a href="order_history.html"><i class="fa fa-history"></i> Order History</a>
    </nav> -->

    <main>
      <div class="stats-grid">
        <div class="stat-card">
          <i class="fa fa-shopping-bag"></i>
          <div class="value">25</div>
          <div class="label">Total Orders</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-check-circle"></i>
          <div class="value">20</div>
          <div class="label">Delivered</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-clock-o"></i>
          <div class="value">5</div>
          <div class="label">In Transit</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-money"></i>
          <div class="value">450,000</div>
          <div class="label">Total Spent</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Quick Actions</span>
        </div>
        <div class="quick-actions">
          <a href="place_order.html" class="action-btn">
            <i class="fa fa-cart-plus"></i>
            <span>Place Order</span>
          </a>
          <a href="order_status.html" class="action-btn">
            <i class="fa fa-list-alt"></i>
            <span>Order Status</span>
          </a>
          <a href="order_history.html" class="action-btn">
            <i class="fa fa-history"></i>
            <span>Order History</span>
          </a>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Recent Orders</span>
        </div>
        <table>
          <tr>
            <th>Order ID</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
          <tr>
            <td>ORD-001</td>
            <td>Rice</td>
            <td>200 kg</td>
            <td>10,000 BDT</td>
            <td><span class="status in-transit">In Transit</span></td>
            <td>
              <button
                class="btn btn-info"
                onclick="viewOrder('ORD-001', 'Rice', '200 kg', '10,000 BDT', 'In Transit')"
              >
                View
              </button>
            </td>
          </tr>
          <tr>
            <td>ORD-002</td>
            <td>Wheat</td>
            <td>150 kg</td>
            <td>7,500 BDT</td>
            <td><span class="status completed">Delivered</span></td>
            <td>
              <button
                class="btn btn-info"
                onclick="viewOrder('ORD-002', 'Wheat', '150 kg', '7,500 BDT', 'Delivered')"
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
      function viewOrder(orderId, product, quantity, amount, status) {
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
                    <span class="detail-label">Amount:</span>
                    <span class="detail-value">${amount}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value"><span class="status ${
                      status === "Delivered"
                        ? "completed"
                        : status === "In Transit"
                        ? "in-transit"
                        : "pending"
                    }">${status}</span></span>
                </div>
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <a href="place_order.html" class="btn btn-primary">Reorder</a>
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
