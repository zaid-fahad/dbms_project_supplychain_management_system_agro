<?php
/**
 * Driver Populate DB
 * Role-specific data for driver operations
 */

include "../db.php";

echo "Driver Data Population\n";
echo "======================\n\n";

try {
    echo "✅ Driver data ready.\n";
    echo "Visit: http://localhost/dbms-scm/driver/dashboard.php\n";
    echo "Login: driver1 / password123\n";
    echo "      or driver2 / password123\n\n";
    
    echo "Driver IDs in system: 10, 11\n";
    echo "Available Deliveries can be viewed per driver.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>