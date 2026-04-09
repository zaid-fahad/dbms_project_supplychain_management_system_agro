<?php
include "../db.php";
$message = '';
$message_type = '';
$current_stock = 0;

if (isset($_POST['submit'])) {
    $product_id = intval($_POST['product_id']);
    $quantity_in = floatval($_POST['quantity_in']);
    $quantity_out = floatval($_POST['quantity_out']);
    $location = $conn->real_escape_string($_POST['location']);

    // Validation
    if ($product_id <= 0) {
        $message = 'Please select a valid product.';
        $message_type = 'error';
    } elseif ($quantity_in < 0 || $quantity_out < 0) {
        $message = 'Quantities cannot be negative.';
        $message_type = 'error';
    } elseif ($quantity_in == 0 && $quantity_out == 0) {
        $message = 'Please enter either quantity in or quantity out.';
        $message_type = 'warning';
    } else {
        $net_change = $quantity_in - $quantity_out;

        $check_sql = "SELECT inventory_id, current_stock FROM Inventory WHERE product_id=$product_id";
        $check_result = $conn->query($check_sql);

        if ($check_result && $check_result->num_rows > 0) {
            $row = $check_result->fetch_assoc();
            $new_stock = $row['current_stock'] + $net_change;
            
            if ($new_stock < 0) {
                $message = 'ERROR: Cannot remove more stock than available. Current: ' . $row['current_stock'] . ' kg, Out: ' . $quantity_out . ' kg';
                $message_type = 'error';
            } else {
                $sql = "UPDATE Inventory SET current_stock=$new_stock, last_updated=NOW() WHERE product_id=$product_id";
                if ($conn->query($sql) === TRUE) {
                    $message = 'Stock updated successfully! (In: ' . $quantity_in . ' kg, Out: ' . $quantity_out . ' kg, Net Change: +' . ($net_change >= 0 ? '' : '') . $net_change . ' kg)';
                    $message_type = 'success';
                } else {
                    $message = 'Error updating stock: ' . $conn->error;
                    $message_type = 'error';
                }
            }
        } else {
            $sql = "INSERT INTO Inventory (product_id, current_stock, last_updated) VALUES ($product_id, $net_change, NOW())";
            if ($conn->query($sql) === TRUE) {
                $message = 'New inventory created! Initial stock: ' . $net_change . ' kg';
                $message_type = 'success';
            } else {
                $message = 'Error creating inventory: ' . $conn->error;
                $message_type = 'error';
            }
        }
    }
}

$products = $conn->query("SELECT product_id, product_name FROM Products ORDER BY product_name");
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Update Stock - Inventory Manager</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <style>
      .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
      }
      
      .form-full {
        grid-column: 1 / -1;
      }
      
      .stock-info {
        background: #e8f4f8;
        border-left: 4px solid #0288d1;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
      }
      
      .stock-info.low {
        background: #fff3e0;
        border-left-color: #f57c00;
      }
      
      .stock-info.good {
        background: #e8f5e9;
        border-left-color: #4caf50;
      }
      
      .stock-value {
        font-size: 24px;
        font-weight: bold;
        color: #1976d2;
      }
      
      .stock-info.low .stock-value {
        color: #f57c00;
      }
      
      .stock-info.good .stock-value {
        color: #4caf50;
      }
      
      .summary-box {
        background: #f5f5f5;
        border: 2px dashed #bbb;
        padding: 15px;
        border-radius: 4px;
        margin-top: 20px;
        display: none;
      }
      
      .summary-box.show {
        display: block;
      }
      
      .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #ddd;
      }
      
      .summary-row:last-child {
        border-bottom: none;
        font-weight: bold;
        padding-top: 10px;
        color: #1976d2;
      }
      
      .summary-label {
        color: #666;
      }
      
      .summary-value {
        font-weight: 600;
      }
      
      .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        gap: 10px;
      }
      
      .alert-success {
        background: #c8e6c9;
        color: #2e7d32;
        border-left: 4px solid #4caf50;
      }
      
      .alert-error {
        background: #ffcdd2;
        color: #c62828;
        border-left: 4px solid #f44336;
      }
      
      .alert-warning {
        background: #fff9c4;
        color: #f57f17;
        border-left: 4px solid #ffeb3b;
      }
      
      .alert i {
        font-size: 18px;
      }
      
      .field-icon {
        position: absolute;
        top: 32px;
        right: 12px;
        color: #999;
        font-size: 16px;
      }
      
      .form-group {
        position: relative;
      }
      
      .form-buttons {
        display: flex;
        gap: 10px;
        margin-top: 30px;
      }
      
      .btn-reset {
        flex: 1;
        padding: 12px;
        background: #f5f5f5;
        color: #666;
        border: 1px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s;
      }
      
      .btn-reset:hover {
        background: #e0e0e0;
      }
      
      .btn-submit {
        flex: 1;
      }
      
      @media (max-width: 600px) {
        .form-row {
          grid-template-columns: 1fr;
        }
      }
    </style>
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Update Stock Levels'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card" style="max-width: 700px; margin: 0 auto">
        <div class="card-header">
          <span class="card-title"><i class="fa fa-cubes"></i> Update Inventory Stock</span>
        </div>
        
        <?php if ($message): ?>
          <div class="alert alert-<?php echo $message_type; ?>" role="alert">
            <i class="fa fa-<?php 
              if ($message_type == 'success') echo 'check-circle';
              elseif ($message_type == 'error') echo 'exclamation-circle';
              else echo 'exclamation-triangle';
            ?>"></i>
            <span><?php echo $message; ?></span>
          </div>
        <?php endif; ?>

        <form action="" method="POST" id="stockForm">
          <!-- Product Selection -->
          <div class="form-group form-full">
            <label for="product_id"><i class="fa fa-leaf"></i> Product</label>
            <select id="product_id" name="product_id" required onchange="updateStockInfo()">
              <option value="">-- Select Product --</option>
              <?php while ($row = $products->fetch_assoc()) {
                echo '<option value="' . $row['product_id'] . '">(' . $row['product_id'] . ') ' . htmlspecialchars($row['product_name']) . '</option>';
              } ?>
            </select>
          </div>

          <!-- Current Stock Info -->
          <div id="stockInfoContainer"></div>

          <!-- Quantity Section -->
          <div class="form-row">
            <div class="form-group">
              <label for="quantity_in"><i class="fa fa-arrow-down"></i> Quantity In (kg)</label>
              <input
                type="number"
                id="quantity_in"
                name="quantity_in"
                step="0.01"
                min="0"
                value="0"
                placeholder="Stock received"
                oninput="updateSummary()"
              />
            </div>
            <div class="form-group">
              <label for="quantity_out"><i class="fa fa-arrow-up"></i> Quantity Out (kg)</label>
              <input
                type="number"
                id="quantity_out"
                name="quantity_out"
                step="0.01"
                min="0"
                value="0"
                placeholder="Stock used/sold"
                oninput="updateSummary()"
              />
            </div>
          </div>

          <!-- Location Section -->
          <div class="form-group form-full">
            <label for="location"><i class="fa fa-warehouse"></i> Storage Location</label>
            <select id="location" name="location" required>
              <option value="">-- Select Warehouse --</option>
              <option value="dhaka">Dhaka Warehouse</option>
              <option value="barisal">Barisal Warehouse</option>
              <option value="chittagong">Chittagong Warehouse</option>
            </select>
          </div>

          <!-- Summary Box -->
          <div id="summaryBox" class="summary-box">
            <div class="summary-row">
              <span class="summary-label"><i class="fa fa-arrow-down"></i> Quantity In:</span>
              <span class="summary-value" id="summaryIn">0 kg</span>
            </div>
            <div class="summary-row">
              <span class="summary-label"><i class="fa fa-arrow-up"></i> Quantity Out:</span>
              <span class="summary-value" id="summaryOut">0 kg</span>
            </div>
            <div class="summary-row">
              <span class="summary-label"><i class="fa fa-exchange"></i> Net Change:</span>
              <span class="summary-value" id="summaryNet">0 kg</span>
            </div>
          </div>

          <!-- Buttons -->
          <div class="form-buttons">
            <button type="reset" class="btn-reset" onclick="resetForm()">
              <i class="fa fa-refresh"></i> Clear
            </button>
            <button type="submit" name="submit" class="btn-submit">
              <i class="fa fa-check"></i> Update Stock
            </button>
          </div>
        </form>
      </div>
    </main>

    <script>
      function updateStockInfo() {
        const productId = document.getElementById('product_id').value;
        const container = document.getElementById('stockInfoContainer');
        
        if (productId) {
          // Fetch current stock via AJAX
          fetch('get-stock-info.php?product_id=' + productId)
            .then(response => response.json())
            .then(data => {
              const stockClass = data.current_stock < 500 ? 'low' : (data.current_stock > 1000 ? 'good' : '');
              const statusText = data.current_stock < 500 ? '⚠ Low Stock' : '✓ Good Stock';
              
              container.innerHTML = `
                <div class="stock-info ${stockClass}">
                  <strong>${data.product_name}</strong><br>
                  <span class="stock-value">${parseFloat(data.current_stock).toFixed(2)} kg</span><br>
                  <small>${statusText}</small>
                </div>
              `;
            })
            .catch(error => console.error('Error:', error));
        } else {
          container.innerHTML = '';
        }
        updateSummary();
      }

      function updateSummary() {
        const quantityIn = parseFloat(document.getElementById('quantity_in').value) || 0;
        const quantityOut = parseFloat(document.getElementById('quantity_out').value) || 0;
        const netChange = quantityIn - quantityOut;
        
        document.getElementById('summaryIn').textContent = quantityIn.toFixed(2) + ' kg';
        document.getElementById('summaryOut').textContent = quantityOut.toFixed(2) + ' kg';
        document.getElementById('summaryNet').textContent = (netChange >= 0 ? '+' : '') + netChange.toFixed(2) + ' kg';
        
        const summaryBox = document.getElementById('summaryBox');
        if (quantityIn > 0 || quantityOut > 0) {
          summaryBox.classList.add('show');
        } else {
          summaryBox.classList.remove('show');
        }
      }

      function resetForm() {
        document.getElementById('stockForm').reset();
        document.getElementById('stockInfoContainer').innerHTML = '';
        document.getElementById('summaryBox').classList.remove('show');
      }
    </script>
  </body>
</html>

