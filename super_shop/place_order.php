<?php
include "../db.php";

$message = '';
$error = '';
$productOptions = [];

$priceMap = [
  'Rice' => 50.00,
  'Wheat' => 45.00,
  'Potato' => 30.00,
];

$product_result = $conn->query("SELECT MIN(product_id) AS product_id, product_name FROM Products GROUP BY product_name ORDER BY product_name");
if ($product_result) {
  while ($product = $product_result->fetch_assoc()) {
    $productOptions[] = $product;
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
  $product_id = intval($_POST['product_id'] ?? 0);
  $quantity = floatval($_POST['quantity'] ?? 0);
  $unit = $conn->real_escape_string(trim($_POST['unit'] ?? 'kg'));
  $delivery_date = $conn->real_escape_string(trim($_POST['delivery_date'] ?? ''));
  $delivery_address = $conn->real_escape_string(trim($_POST['delivery_address'] ?? ''));
  $notes = $conn->real_escape_string(trim($_POST['notes'] ?? ''));

  if ($product_id <= 0 || $quantity <= 0 || $delivery_address === '' || $delivery_date === '') {
    $error = 'Please complete all required fields.';
  } else {
    $product_name_result = $conn->query("SELECT product_name FROM Products WHERE product_id = $product_id LIMIT 1");
    if ($product_name_result && $product_name_result->num_rows > 0) {
      $product_name = $product_name_result->fetch_assoc()['product_name'];
      $unit_price = $priceMap[$product_name] ?? 0.00;
      $line_total = $quantity * $unit_price;
      $shared_customer_id = 0;

      $conn->begin_transaction();

      $customer_lookup_sql = "SELECT customer_id FROM Customers WHERE customer_name='Super Shop' AND customer_type='Super Shop' LIMIT 1";
      $customer_lookup_result = $conn->query($customer_lookup_sql);
      if ($customer_lookup_result && $customer_lookup_result->num_rows > 0) {
        $shared_customer_id = (int) $customer_lookup_result->fetch_assoc()['customer_id'];
      } else {
        $customer_insert_sql = "INSERT INTO Customers (customer_name, customer_type, address) VALUES ('Super Shop', 'Super Shop', '$delivery_address')";
        if ($conn->query($customer_insert_sql)) {
          $shared_customer_id = (int) $conn->insert_id;
        } else {
          $conn->rollback();
          $error = 'Customer could not be saved: ' . $conn->error;
        }
      }

      if ($error === '') {
        $shared_order_sql = "INSERT INTO Orders (customer_id, sales_manager_id, status, total_amount) VALUES ($shared_customer_id, NULL, 'Pending', '$line_total')";
        if ($conn->query($shared_order_sql)) {
          $shared_order_id = (int) $conn->insert_id;

          $order_sql = "INSERT INTO SuperShop_Orders (customer_name, delivery_address, delivery_date, notes, status, total_amount) VALUES ('Super Shop', '$delivery_address', '$delivery_date', '$notes', 'Pending', '$line_total')";
          if ($conn->query($order_sql)) {
            $order_id = (int) $conn->insert_id;
            $item_sql = "INSERT INTO SuperShop_Order_Items (super_shop_order_id, product_id, quantity, unit, unit_price, line_total) VALUES ($order_id, $product_id, $quantity, '$unit', '$unit_price', '$line_total')";
            $ref_sql = "INSERT INTO SuperShop_Order_Refs (super_shop_order_id, order_id) VALUES ($order_id, $shared_order_id)";

            if ($conn->query($item_sql) && $conn->query($ref_sql)) {
              $conn->commit();
              $message = 'Order placed successfully. Shared Order ID: ' . $shared_order_id;
            } else {
              $conn->rollback();
              $error = 'Order details could not be saved: ' . $conn->error;
            }
          } else {
            $conn->rollback();
            $error = 'Super Shop order could not be saved: ' . $conn->error;
          }
        } else {
          $conn->rollback();
          $error = 'Shared order could not be saved: ' . $conn->error;
        }
      }
    } else {
      $error = 'Selected produce is invalid.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Place Order - Super Shop</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Place Order'; include '../components/header.html'; ?>

    <main>
      <div class="card" style="max-width: 600px; margin: 0 auto">
        <div class="card-header">
          <span class="card-title">New Order Form</span>
        </div>
        <?php if ($message): ?>
          <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form action="" method="POST">
          <div class="form-group">
            <label for="product_id">Produce Type</label>
            <select id="product_id" name="product_id" required>
              <option value="">Select Produce</option>
              <?php foreach ($productOptions as $product): ?>
                <option value="<?php echo htmlspecialchars($product['product_id']); ?>"><?php echo htmlspecialchars($product['product_name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="quantity">Quantity (kg)</label>
            <input
              type="number"
              step="0.01"
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
            <label for="delivery_date">Required Delivery Date</label>
            <input
              type="date"
              id="delivery_date"
              name="delivery_date"
              required
            />
          </div>
          <div class="form-group">
            <label for="delivery_address">Delivery Address</label>
            <input
              type="text"
              id="delivery_address"
              name="delivery_address"
              placeholder="Enter delivery address"
              required
            />
          </div>
          <div class="form-group">
            <label for="notes">Special Instructions</label>
            <textarea
              id="notes"
              name="notes"
              rows="2"
              placeholder="Any special requirements"
            ></textarea>
          </div>
          <button type="submit" name="submit" class="btn-submit">
            <i class="fa fa-check"></i> Submit Order
          </button>
        </form>
      </div>
    </main>
  </body>
</html>