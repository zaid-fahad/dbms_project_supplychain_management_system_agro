<?php
include "../db.php";

if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
    
    $sql = "SELECT p.product_name, i.current_stock FROM Products p 
            LEFT JOIN Inventory i ON p.product_id = i.product_id 
            WHERE p.product_id = $product_id";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'product_name' => $row['product_name'],
            'current_stock' => $row['current_stock'] ?? 0
        ]);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
} else {
    echo json_encode(['error' => 'Missing product_id']);
}

$conn->close();
?>
