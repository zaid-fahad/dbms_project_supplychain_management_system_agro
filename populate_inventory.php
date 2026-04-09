<?php
include "db.php";

// Insert dummy inventory data
$inventory_data = [
    ['product_id' => 1, 'current_stock' => 800.50],
    ['product_id' => 2, 'current_stock' => 650.25],
    ['product_id' => 3, 'current_stock' => 450.75],
    ['product_id' => 4, 'current_stock' => 920.00],
    ['product_id' => 5, 'current_stock' => 780.50],
    ['product_id' => 6, 'current_stock' => 320.25],
    ['product_id' => 7, 'current_stock' => 1100.00],
    ['product_id' => 8, 'current_stock' => 850.75],
    ['product_id' => 9, 'current_stock' => 380.50],
    ['product_id' => 10, 'current_stock' => 950.25],
    ['product_id' => 11, 'current_stock' => 700.00],
    ['product_id' => 12, 'current_stock' => 480.50],
];

echo "Inserting inventory data...\n";

foreach ($inventory_data as $item) {
    // Check if inventory already exists for this product
    $check_sql = "SELECT inventory_id FROM Inventory WHERE product_id=" . $item['product_id'];
    $check_result = $conn->query($check_sql);
    
    if ($check_result && $check_result->num_rows === 0) {
        $sql = "INSERT INTO Inventory (product_id, current_stock, last_updated) 
                VALUES (" . $item['product_id'] . ", " . $item['current_stock'] . ", NOW())";
        
        if ($conn->query($sql) === TRUE) {
            echo "✓ Inserted inventory for product_id " . $item['product_id'] . ": " . $item['current_stock'] . " kg\n";
        } else {
            echo "✗ Error inserting inventory for product_id " . $item['product_id'] . ": " . $conn->error . "\n";
        }
    } else {
        echo "⊘ Inventory already exists for product_id " . $item['product_id'] . "\n";
    }
}

echo "\nInventory data population complete!\n";
$conn->close();
?>
