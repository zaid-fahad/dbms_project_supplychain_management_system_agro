<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Transport Manager Dashboard</title>
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
        <a
          href="../transport_manager/dashboard.html"
          class="topbar-user active"
        >
          <i class="fa fa-car"></i>
          <span>Transport</span>
        </a>
        <a href="../driver/dashboard.html" class="topbar-user">
          <i class="fa fa-road"></i>
          <span>Driver</span>
        </a>
        <a href="../super_shop/dashboard.html" class="topbar-user">
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
        <span class="title">Transport Manager Dashboard</span>
      </div>
      <a href="../index.html" class="back-btn"
        ><i class="fa fa-arrow-left"></i> Back</a
      >
    </header>

    <!-- <nav>
        <a href="pickup_requests.html"><i class="fa fa-truck"></i> Pickup Requests</a>
        <a href="delivery_orders.html"><i class="fa fa-send"></i> Delivery Orders</a>
        <a href="fleet_management.html"><i class="fa fa-car"></i> Fleet Management</a>
    </nav> -->

    <main>
      <div class="stats-grid">
        <div class="stat-card">
          <i class="fa fa-truck"></i>
          <div class="value">45</div>
          <div class="label">Pickups</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-send"></i>
          <div class="value">40</div>
          <div class="label">Deliveries</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-car"></i>
          <div class="value">8</div>
          <div class="label">Vehicles</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-user"></i>
          <div class="value">10</div>
          <div class="label">Drivers</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Quick Actions</span>
        </div>
        <div class="quick-actions">
          <a href="pickup_requests.html" class="action-btn">
            <i class="fa fa-truck"></i>
            <span>Pickup Requests</span>
          </a>
          <a href="delivery_orders.html" class="action-btn">
            <i class="fa fa-send"></i>
            <span>Delivery Orders</span>
          </a>
          <a href="fleet_management.html" class="action-btn">
            <i class="fa fa-car"></i>
            <span>Fleet Management</span>
          </a>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Active Deliveries</span>
        </div>
        <table>
          <tr>
            <th>Order ID</th>
            <th>Driver</th>
            <th>Vehicle</th>
            <th>Destination</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
          <tr>
            <td>DEL-001</td>
            <td>Rafiq</td>
            <td>Truck-001</td>
            <td>Dhaka</td>
            <td><span class="status in-transit">In Transit</span></td>
            <td>
              <button
                class="btn btn-info"
                onclick="viewDelivery('DEL-001', 'Rafiq', 'Truck-001', 'Dhaka', 'In Transit')"
              >
                View
              </button>
            </td>
          </tr>
          <tr>
            <td>DEL-002</td>
            <td>Hasan</td>
            <td>Van-002</td>
            <td>Barisal</td>
            <td><span class="status pending">Assigned</span></td>
            <td>
              <button
                class="btn btn-info"
                onclick="viewDelivery('DEL-002', 'Hasan', 'Van-002', 'Barisal', 'Assigned')"
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
          <h3>Delivery Details</h3>
          <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div id="modalBody"></div>
      </div>
    </div>

    <script>
      function viewDelivery(orderId, driver, vehicle, destination, status) {
        document.getElementById("modalBody").innerHTML = `
                <div class="detail-row">
                    <span class="detail-label">Order ID:</span>
                    <span class="detail-value">${orderId}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Driver:</span>
                    <span class="detail-value">${driver}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Vehicle:</span>
                    <span class="detail-value">${vehicle}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Destination:</span>
                    <span class="detail-value">${destination}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value"><span class="status ${
                      status === "In Transit" ? "in-transit" : "pending"
                    }">${status}</span></span>
                </div>
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button class="btn btn-primary">Assign</button>
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
