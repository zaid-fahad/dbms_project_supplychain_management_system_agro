<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quality Officer Dashboard</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Quality Officer Dashboard'; include '../components/header.html'; ?>

    <!-- 
    <nav>
        <a href="quality_check.html"><i class="fa fa-check-square-o"></i> Quality Check</a>
        <a href="batch_approval.html"><i class="fa fa-check-circle"></i> Batch Approval</a>
        <a href="quality_reports.html"><i class="fa fa-bar-chart"></i> Quality Reports</a>
    </nav> -->

    <main>
      <div class="stats-grid">
        <div class="stat-card">
          <i class="fa fa-check-square-o"></i>
          <div class="value">120</div>
          <div class="label">Total Checks</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-check-circle"></i>
          <div class="value">95</div>
          <div class="label">Passed</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-times-circle"></i>
          <div class="value">15</div>
          <div class="label">Failed</div>
        </div>
        <div class="stat-card">
          <i class="fa fa-clock-o"></i>
          <div class="value">10</div>
          <div class="label">Pending</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Quick Actions</span>
        </div>
        <div class="quick-actions">
          <a href="quality_check.html" class="action-btn">
            <i class="fa fa-check-square-o"></i>
            <span>Quality Check</span>
          </a>
          <a href="batch_approval.html" class="action-btn">
            <i class="fa fa-check-circle"></i>
            <span>Batch Approval</span>
          </a>
          <a href="quality_reports.html" class="action-btn">
            <i class="fa fa-bar-chart"></i>
            <span>Quality Reports</span>
          </a>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Pending Quality Checks</span>
        </div>
        <table>
          <tr>
            <th>Batch ID</th>
            <th>Produce</th>
            <th>Quantity</th>
            <th>Submitted</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
          <tr>
            <td>B002</td>
            <td>Wheat</td>
            <td>300 kg</td>
            <td>Field Supervisor</td>
            <td>2026-03-14</td>
            <td>
              <a href="quality_check.html" class="btn btn-primary">Check</a>
            </td>
          </tr>
          <tr>
            <td>B004</td>
            <td>Vegetables</td>
            <td>150 kg</td>
            <td>Field Supervisor</td>
            <td>2026-03-15</td>
            <td>
              <a href="quality_check.html" class="btn btn-primary">Check</a>
            </td>
          </tr>
        </table>
      </div>
    </main>

    <div class="modal" id="detailsModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Quality Check Details</h3>
          <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div id="modalBody"></div>
      </div>
    </div>

    <script>
      function viewDetails(
        id,
        produce,
        quantity,
        submitted,
        date,
        score,
        grade
      ) {
        document.getElementById("modalBody").innerHTML = `
                <div class="detail-row">
                    <span class="detail-label">Batch ID:</span>
                    <span class="detail-value">${id}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Produce:</span>
                    <span class="detail-value">${produce}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Quantity:</span>
                    <span class="detail-value">${quantity}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Submitted By:</span>
                    <span class="detail-value">${submitted}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value">${date}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Quality Score:</span>
                    <span class="detail-value">${score}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Grade:</span>
                    <span class="detail-value">${grade}</span>
                </div>
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button class="btn btn-primary">View Full Report</button>
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
