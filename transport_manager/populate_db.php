<?php
/**
 * Transport Manager Populate DB
 * Role-specific data for transport operations
 */

$transport_manager_dir = dirname(__FILE__);
if (!file_exists($transport_manager_dir)) {
    mkdir($transport_manager_dir, 0755, true);
}

include "../db.php";

echo "Transport Manager Data Population\n";
echo "==================================\n\n";

try {
    echo "✅ Transport Manager data ready.\n";
    echo "Visit: http://localhost/dbms-scm/transport_manager/dashboard.php\n";
    echo "Login: transport1 / password123\n\n";
    
    echo "Fleet & Delivery Summary:\n";
    $result = $conn->query("SELECT COUNT(*) as cnt FROM Vehicles");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "- Total Vehicles: " . $row['cnt'] . "\n";
    }
    
    $result = $conn->query("SELECT COUNT(*) as cnt FROM Deliveries");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "- Total Deliveries: " . $row['cnt'] . "\n";
    }
    
    $result = $conn->query("SELECT COUNT(*) as cnt FROM Deliveries WHERE status = 'Completed'");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "- Completed Deliveries: " . $row['cnt'] . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>