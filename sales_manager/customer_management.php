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

    <main>
      <div class="card">
        <div class="card-header">
          <span class="card-title">Customer Directory</span>
          <button class="btn btn-primary">
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
          <tr>
            <td>CUST-001</td>
            <td>Super Shop A</td>
            <td>Retail Store</td>
            <td>+880 1712-345678</td>
            <td>25</td>
            <td>450,000 BDT</td>
            <td><span class="status good">Active</span></td>
            <td>
              <button class="btn btn-info btn-sm">
                <i class="fa fa-eye"></i>
              </button>
              <button class="btn btn-warning btn-sm">
                <i class="fa fa-edit"></i>
              </button>
            </td>
          </tr>
          <tr>
            <td>CUST-002</td>
            <td>Local Market B</td>
            <td>Market</td>
            <td>+880 1812-345678</td>
            <td>18</td>
            <td>320,000 BDT</td>
            <td><span class="status good">Active</span></td>
            <td>
              <button class="btn btn-info btn-sm">
                <i class="fa fa-eye"></i>
              </button>
              <button class="btn btn-warning btn-sm">
                <i class="fa fa-edit"></i>
              </button>
            </td>
          </tr>
          <tr>
            <td>CUST-003</td>
            <td>Restaurant C</td>
            <td>Restaurant</td>
            <td>+880 1912-345678</td>
            <td>32</td>
            <td>280,000 BDT</td>
            <td><span class="status good">Active</span></td>
            <td>
              <button class="btn btn-info btn-sm">
                <i class="fa fa-eye"></i>
              </button>
              <button class="btn btn-warning btn-sm">
                <i class="fa fa-edit"></i>
              </button>
            </td>
          </tr>
          <tr>
            <td>CUST-004</td>
            <td>Wholesale D</td>
            <td>Wholesale</td>
            <td>+880 1512-345678</td>
            <td>8</td>
            <td>180,000 BDT</td>
            <td><span class="status warning">Inactive</span></td>
            <td>
              <button class="btn btn-info btn-sm">
                <i class="fa fa-eye"></i>
              </button>
              <button class="btn btn-warning btn-sm">
                <i class="fa fa-edit"></i>
              </button>
            </td>
          </tr>
        </table>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Customer Insights</span>
        </div>
        <div class="stats-grid">
          <div class="stat-card">
            <i class="fa fa-users"></i>
            <div class="value">85</div>
            <div class="label">Total Customers</div>
          </div>
          <div class="stat-card">
            <i class="fa fa-user-plus"></i>
            <div class="value">12</div>
            <div class="label">New This Month</div>
          </div>
          <div class="stat-card">
            <i class="fa fa-star"></i>
            <div class="value">68</div>
            <div class="label">Active Customers</div>
          </div>
          <div class="stat-card">
            <i class="fa fa-user-times"></i>
            <div class="value">17</div>
            <div class="label">Inactive Customers</div>
          </div>
        </div>
      </div>
    </main>

    <style>
      .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
      }
    </style>
  </body>
</html>
