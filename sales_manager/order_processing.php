<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Process Orders - Sales Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Order Processing'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
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
            <th>Status</th>
            <th>Action</th>
          </tr>
          <tr>
            <td>ORD-001</td>
            <td>Super Shop A</td>
            <td>Rice</td>
            <td>200 kg</td>
            <td>10,000 BDT</td>
            <td><span class="status pending">Pending</span></td>
            <td>
              <button class="btn btn-success" onclick="processOrder('ORD-001')">
                <i class="fa fa-check"></i> Process
              </button>
            </td>
          </tr>
          <tr>
            <td>ORD-002</td>
            <td>Local Retailer B</td>
            <td>Wheat</td>
            <td>150 kg</td>
            <td>6,750 BDT</td>
            <td><span class="status pending">Pending</span></td>
            <td>
              <button class="btn btn-success" onclick="processOrder('ORD-002')">
                <i class="fa fa-check"></i> Process
              </button>
            </td>
          </tr>
          <tr>
            <td>ORD-003</td>
            <td>Restaurant C</td>
            <td>Potatoes</td>
            <td>100 kg</td>
            <td>2,500 BDT</td>
            <td><span class="status processing">Processing</span></td>
            <td>
              <button class="btn btn-info" onclick="viewOrder('ORD-003')">
                <i class="fa fa-eye"></i> View
              </button>
            </td>
          </tr>
        </table>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Recent Processed Orders</span>
        </div>
        <table>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Amount</th>
            <th>Processed Date</th>
          </tr>
          <tr>
            <td>ORD-004</td>
            <td>Super Shop D</td>
            <td>Rice</td>
            <td>300 kg</td>
            <td>15,000 BDT</td>
            <td>2024-01-15</td>
          </tr>
          <tr>
            <td>ORD-005</td>
            <td>Local Market E</td>
            <td>Tomatoes</td>
            <td>80 kg</td>
            <td>3,200 BDT</td>
            <td>2024-01-14</td>
          </tr>
        </table>
      </div>
    </main>

    <script>
      function processOrder(orderId) {
        if (confirm(`Process order ${orderId}?`)) {
          alert(`Order ${orderId} has been processed successfully!`);
          location.reload();
        }
      }

      function viewOrder(orderId) {
        alert(`Viewing details for order ${orderId}`);
      }
    </script>
  </body>
</html>
