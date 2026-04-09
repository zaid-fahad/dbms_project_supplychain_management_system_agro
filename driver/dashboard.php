<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Driver Dashboard</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Driver Dashboard'; include '../components/header.html'; ?>

    <!-- <nav>
        <a href="delivery_tracking.html"><i class="fa fa-map-marker"></i> My Deliveries</a>
        <a href="pickup_instructions.html"><i class="fa fa-truck"></i> Pickup Info</a>
        <a href="delivery_status.html"><i class="fa fa-refresh"></i> Update Status</a>
    </nav> -->

    <main>
      <div class="stats-grid">
        <div class="stat-card">
          <i class="fa fa-check-circle"></i>
          <div class="value">35</div>
          <div class="label">Completed</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-truck"></i>
          <div class="value">2</div>
          <div class="label">Active</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-clock-o"></i>
          <div class="value">3</div>
          <div class="label">Pending</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-money"></i>
          <div class="value">85,000</div>
          <div class="label">Earnings</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Quick Actions</span>
        </div>
        <div class="quick-actions">
          <a href="delivery_tracking.html" class="action-btn">
            <i class="fa fa-map-marker"></i>
            <span>My Deliveries</span>
          </a>
          <a href="pickup_instructions.html" class="action-btn">
            <i class="fa fa-truck"></i>
            <span>Pickup Info</span>
          </a>
          <a href="delivery_status.html" class="action-btn">
            <i class="fa fa-refresh"></i>
            <span>Update Status</span>
          </a>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">My Deliveries</span>
        </div>
        <table>
          <tr>
            <th>Delivery ID</th>
            <th>Pickup From</th>
            <th>Destination</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
          <tr>
            <td>DEL-001</td>
            <td>Farmer Rahim</td>
            <td>Dhaka Warehouse</td>
            <td><span class="status in-transit">In Transit</span></td>
            <td>
              <button
                class="btn btn-info"
                onclick="viewDriverDelivery('DEL-001', 'Farmer Rahim', 'Dhaka Warehouse', 'In Transit')"
              >
                View
              </button>
            </td>
          </tr>
          <tr>
            <td>DEL-002</td>
            <td>Warehouse</td>
            <td>Super Shop A</td>
            <td><span class="status pending">Pending</span></td>
            <td>
              <button
                class="btn btn-info"
                onclick="viewDriverDelivery('DEL-002', 'Warehouse', 'Super Shop A', 'Pending')"
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
      function viewDriverDelivery(deliveryId, pickupFrom, destination, status) {
        document.getElementById("modalBody").innerHTML = `
                <div class="detail-row">
                    <span class="detail-label">Delivery ID:</span>
                    <span class="detail-value">${deliveryId}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Pickup From:</span>
                    <span class="detail-value">${pickupFrom}</span>
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
                    <a href="delivery_status.html" class="btn btn-primary">Update Status</a>
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
