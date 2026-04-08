<?php 
include "../db.php";
$farmer_id = 1; // Assuming farmer_id=1 for demo; replace with session variable in production

if (isset($_POST['update'])) {
    $batch_id = $_POST['batch_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $purchase_date = $_POST['purchase_date'];

    $sql = "UPDATE Batches SET product_id='$product_id', quantity='$quantity', unit='$unit', purchase_date='$purchase_date' WHERE batch_id='$batch_id' AND farmer_id=$farmer_id";

    $result = $conn->query($sql);

    if ($result == TRUE) {
        echo '<div class="alert alert-success" role="alert">Batch updated successfully.</div>';
        header("refresh:2; url=./status.php");
    } else {
        echo "Error:" . $sql . "<br>" . $conn->error;
    }
}

if (isset($_GET['batch_id'])) {
    $batch_id = $_GET['batch_id'];

    $sql = "SELECT b.*, p.product_name FROM Batches b JOIN Products p ON b.product_id = p.product_id WHERE b.batch_id='$batch_id' AND b.farmer_id=$farmer_id";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $batch_number = $row['batch_number'];
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];
        $unit = $row['unit'];
        $purchase_date = $row['purchase_date'];
        $batch_id = $row['batch_id'];

        $products_sql = "SELECT product_id, product_name FROM Products";
        $products_result = $conn->query($products_sql);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Update Batch</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include 'components/topbar.html'; ?>

    <header>
      <div class="header-left">
        <img src="../logo.png" alt="Logo" class="logo" />
        <span class="title">Update Batch</span>
      </div>
      <a href="status.php" class="back-btn"
        ><i class="fa fa-arrow-left"></i> Back</a
      >
    </header>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card" style="max-width: 600px; margin: 0 auto">
        <div class="card-header">
          <span class="card-title">Update Batch</span>
        </div>
        <form action="" method="POST">
          <input type="hidden" name="batch_id" value="<?php echo $batch_id; ?>">
          <div class="form-group">
            <label for="batch_number">Batch Number</label>
            <input type="text" name="batch_number" value="<?php echo $batch_number; ?>" readonly>
          </div>
          <div class="form-group">
            <label for="product_id">Product</label>
            <select name="product_id" required>
              <?php while ($prod = $products_result->fetch_assoc()) { ?>
                <option value="<?php echo $prod['product_id']; ?>" <?php if ($prod['product_id'] == $product_id) echo 'selected'; ?>><?php echo $prod['product_name']; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" name="quantity" value="<?php echo $quantity; ?>" required>
          </div>
          <div class="form-group">
            <label for="unit">Unit</label>
            <input type="text" name="unit" value="<?php echo $unit; ?>" required>
          </div>
          <div class="form-group">
            <label for="purchase_date">Purchase Date</label>
            <input type="date" name="purchase_date" value="<?php echo $purchase_date; ?>" required>
          </div>
          <button type="submit" name="update" class="btn-submit">
            <i class="fa fa-edit"></i> Update Batch
          </button>
        </form>
      </div>
    </main>
  </body>
</html>

<?php
    } else {
        header('Location: status.php');
    }
}
?>