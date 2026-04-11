<?php
/**
 * Farmer Populate DB
 * Role-specific data for farmer operations  
 */

include "../db.php";

echo "Farmer Data Population\n";
echo "======================\n\n";

try {
    echo "✅ Farmer data ready.\n";
    echo "Visit: http://localhost/dbms-scm/farmer/dashboard.php\n";
    echo "\nFarmers in System:\n";
    
    $result = $conn->query("SELECT farmer_id, name, location FROM Farmers ORDER BY name");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "- ID " . $row['farmer_id'] . ": " . $row['name'] . " (" . $row['location'] . ")\n";
        }
    }
    
    echo "\nFarmer Dashboard Credentials:\n";
    echo "- Farmer ID 1: Rahim Khan (Shariatpur)\n";
    echo "- Farmer ID 2: Karim Ahmed (Madaripur)\n";
    echo "- Farmer ID 3: Salam Hossain (Faridpur)\n";
    echo "- Farmer ID 4: Rahman Khan (Gopalganj)\n";
    echo "- Farmer ID 5: Habib Hassan (Rajbari)\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>