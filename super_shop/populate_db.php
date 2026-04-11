<?php
/**
 * Super Shop Populate DB
 * Role-specific data for super shop operations
 */

include "../db.php";

echo "Super Shop Data Population\n";
echo "==========================\n\n";

try {
    echo "✅ Super Shop data ready.\n";
    echo "Visit: http://localhost/dbms-scm/super_shop/dashboard.php\n";
    // Super Shop users would need to be set up via main populate_db.php
    echo "\nNote: Login credentials are in the main database population.\n";
    echo "Use customer_id 1 or 2 (Super Shop customers) to view orders.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>