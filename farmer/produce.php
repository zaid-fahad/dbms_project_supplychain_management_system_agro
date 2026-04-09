<?php 
include "../db.php";

if (isset($_POST['submit'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $purchase_date = $_POST['harvest-date'];

    // Assume farmer_id = 1, supervisor_id = 1
    $farmer_id = 1;
    $supervisor_id = 1;
    $batch_number = 'B' . time();

    $sql = "INSERT INTO Batches (batch_number, product_id, farmer_id, supervisor_id, quantity, unit, purchase_date) VALUES ('$batch_number', '$product_id', '$farmer_id', '$supervisor_id', '$quantity', '$unit', '$purchase_date')";

    $result = $conn->query($sql);

    if ($result == TRUE) {
        echo '<div class="alert alert-success" role="alert">Produce submitted successfully!</div>';
        header("refresh:2; url=./dashboard.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}

// Get products for dropdown
$products_sql = "SELECT product_id, product_name FROM Products";
$products_result = $conn->query($products_sql);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Produce - Farmer</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Add Produce'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card" style="max-width: 600px; margin: 0 auto">
        <div class="card-header">
          <span class="card-title">Submit New Produce</span>
        </div>
        <form action="" method="POST">
          <div class="form-group">
            <label for="product_id">Produce Type</label>
            <select id="product_id" name="product_id" required>
              <option value="">Select Produce</option>
              <?php
              if ($products_result->num_rows > 0) {
                while ($product = $products_result->fetch_assoc()) {
                  echo '<option value="' . $product['product_id'] . '">' . $product['product_name'] . '</option>';
                }
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="quantity">Quantity</label>
            <input
              type="number"
              id="quantity"
              name="quantity"
              placeholder="Enter quantity"
              required
            />
          </div>
          <div class="form-group">
            <label for="unit">Unit</label>
            <input
              type="text"
              id="unit"
              name="unit"
              placeholder="e.g., kg"
              value="kg"
              required
            />
          </div>
          <div class="form-group">
            <label for="harvest-date">Purchase Date</label>
            <input type="date" id="harvest-date" name="harvest-date" required />
          </div>
          <button type="submit" name="submit" class="btn-submit">
            <i class="fa fa-check"></i> Submit Produce
          </button>
        </form>
      </div>
    </main>
  </body>
</html>

