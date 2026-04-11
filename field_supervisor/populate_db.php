<?php
/**
 * Field Supervisor Populate DB
 * Role-specific data for field supervisor operations
 */

include "../db.php";

echo "Field Supervisor Data Population\n";
echo "==================================\n\n";

try {
    // Field Supervisor specific operations would go here
    // For now, the main populate_db.php handles all shared data
    
    echo "✅ Field Supervisor data ready.\n";
    echo "Visit: http://localhost/dbms-scm/field_supervisor/dashboard.php\n";
    echo "Login: supervisor1 / password123\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>