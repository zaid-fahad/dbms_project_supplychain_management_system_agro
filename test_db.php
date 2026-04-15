<?php
try {
    $conn = new mysqli('localhost', 'root', '', 'dbms_scms');
    if (!$conn->connect_error) {
        echo "Database connection successful!\n";

        // Check if tables exist
        $tables = ['Orders', 'SuperShop_Orders', 'Customers'];
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result->num_rows > 0) {
                echo "Table '$table' exists.\n";
            } else {
                echo "Table '$table' does not exist.\n";
            }
        }

        // Check sample data
        $result = $conn->query("SELECT COUNT(*) as count FROM Orders");
        $count = $result->fetch_assoc()['count'];
        echo "Orders table has $count records.\n";

        $result = $conn->query("SELECT COUNT(*) as count FROM SuperShop_Orders");
        $count = $result->fetch_assoc()['count'];
        echo "SuperShop_Orders table has $count records.\n";

        $result = $conn->query("SELECT COUNT(*) as count FROM Customers");
        $count = $result->fetch_assoc()['count'];
        echo "Customers table has $count records.\n";

        $conn->close();
    } else {
        echo "Database connection failed: " . $conn->connect_error . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>