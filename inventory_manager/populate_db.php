<?php
/**
 * Inventory Manager Populate DB
 * Role-specific data for inventory operations
 */

include "../db.php";

echo "Inventory Manager Data Population\n";
echo "==================================\n\n";

try {
    echo "✅ Inventory Manager data ready.\n";
    echo "Visit: http://localhost/dbms-scm/inventory_manager/dashboard.php\n";
    echo "Login: inventory1 / password123\n";
    echo "      or inv2 / password123\n\n";
    
    echo "Inventory Summary:\n";
    $result = $conn->query("SELECT COUNT(*) as cnt FROM Inventory WHERE current_stock < 1000");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "- Low Stock Items: " . $row['cnt'] . "\n";
    }
    
    $result = $conn->query("SELECT SUM(current_stock) as total FROM Inventory");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "- Total Stock Value: " . number_format($row['total'], 2) . " units\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>