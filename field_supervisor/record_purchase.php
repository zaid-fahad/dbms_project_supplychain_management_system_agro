<?php
include "../db.php";
$message = '';

if (isset($_POST['submit'])) {
    $farmer_id = intval($_POST['farmer_id']);
    $product_id = intval($_POST['product_id']);
    $quantity = floatval($_POST['quantity']);
    $unit = $conn->real_escape_string($_POST['unit']);
    $purchase_date = $conn->real_escape_string($_POST['purchase_date']);
    $supervisor_id = 1;
    $batch_number = 'B' . time();

    $sql = "INSERT INTO Batches (batch_number, product_id, farmer_id, supervisor_id, quantity, unit, purchase_date) VALUES ('$batch_number', '$product_id', '$farmer_id', '$supervisor_id', '$quantity', '$unit', '$purchase_date')";

    if ($conn->query($sql) === TRUE) {
        $message = 'Purchase recorded successfully.';
    } else {
        $message = 'Error recording purchase: ' . $conn->error;
    }
}

$farmer_sql = "SELECT farmer_id, name, location FROM Farmers";
$farmer_result = $conn->query($farmer_sql);
$product_sql = "SELECT product_id, product_name FROM Products";
$product_result = $conn->query($product_sql);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Record Purchase - Field Supervisor</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Record Purchase'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card" style="max-width: 600px; margin: 0 auto">
        <div class="card-header">
          <span class="card-title">Record Purchase from Farmer</span>
        </div>
        <?php if ($message): ?>
          <div class="alert alert-success" role="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="" method="POST">
          <div class="form-group">
            <label for="farmer_id">Farmer</label>
            <select id="farmer_id" name="farmer_id" required>
              <option value="">Select Farmer</option>
              <?php if ($farmer_result->num_rows > 0) {
                while ($farmer = $farmer_result->fetch_assoc()) {
                    echo '<option value="' . $farmer['farmer_id'] . '">' . htmlspecialchars($farmer['name']) . ' (' . htmlspecialchars($farmer['location']) . ')</option>';
                }
              } ?>
            </select>
          </div>
          <div class="form-group">
            <label for="product_id">Produce Type</label>
            <select id="product_id" name="product_id" required>
              <option value="">Select Produce</option>
              <?php if ($product_result->num_rows > 0) {
                while ($product = $product_result->fetch_assoc()) {
                    echo '<option value="' . $product['product_id'] . '">' . htmlspecialchars($product['product_name']) . '</option>';
                }
              } ?>
            </select>
          </div>
          <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" step="0.01" id="quantity" name="quantity" placeholder="Enter quantity" required />
          </div>
          <div class="form-group">
            <label for="unit">Unit</label>
            <input type="text" id="unit" name="unit" placeholder="e.g., kg" value="kg" required />
          </div>
          <div class="form-group">
            <label for="purchase_date">Purchase Date</label>
            <input type="date" id="purchase_date" name="purchase_date" required />
          </div>
          <button type="submit" name="submit" class="btn-submit">
            <i class="fa fa-check"></i> Record Purchase
          </button>
        </form>
      </div>
    </main>
  </body>
</html>

