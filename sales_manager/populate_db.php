<?php
/**
 * Sales Manager Populate DB
 * Role-specific data for sales operations
 */

$sales_manager_dir = dirname(__FILE__);
if (!file_exists($sales_manager_dir)) {
    mkdir($sales_manager_dir, 0755, true);
}

include "../db.php";

echo "Sales Manager Data Population\n";
echo "==============================\n\n";

try {
    echo "✅ Sales Manager data ready.\n";
    echo "Visit: http://localhost/dbms-scm/sales_manager/dashboard.php\n";
    echo "Login: sales1 / password123\n";
    echo "      or sales2 / password123\n\n";
    
    echo "Sales Summary:\n";
    $result = $conn->query("SELECT COUNT(*) as cnt FROM Orders");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "- Total Orders: " . $row['cnt'] . "\n";
    }
    
    $result = $conn->query("SELECT SUM(total_amount) as total FROM Orders");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "- Total Order Value: BDT " . number_format($row['total'], 2) . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>