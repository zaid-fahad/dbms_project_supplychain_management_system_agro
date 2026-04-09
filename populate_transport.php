<?php
include "db.php";

// Insert dummy vehicle data
$vehicle_data = [
    ['license_plate' => 'DHA-001', 'vehicle_type' => 'Truck'],
    ['license_plate' => 'DHA-002', 'vehicle_type' => 'Van'],
    ['license_plate' => 'DHA-003', 'vehicle_type' => 'Truck'],
    ['license_plate' => 'BAR-001', 'vehicle_type' => 'Van'],
    ['license_plate' => 'BAR-002', 'vehicle_type' => 'Truck'],
    ['license_plate' => 'CHI-001', 'vehicle_type' => 'Van'],
    ['license_plate' => 'CHI-002', 'vehicle_type' => 'Truck'],
    ['license_plate' => 'CHI-003', 'vehicle_type' => 'Van'],
];

echo "Inserting vehicle data...\n";

foreach ($vehicle_data as $item) {
    $check_sql = "SELECT vehicle_id FROM Vehicles WHERE license_plate='" . $item['license_plate'] . "'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result && $check_result->num_rows === 0) {
        $sql = "INSERT INTO Vehicles (license_plate, vehicle_type) VALUES ('" . $item['license_plate'] . "', '" . $item['vehicle_type'] . "')";
        
        if ($conn->query($sql) === TRUE) {
            echo "✓ Inserted vehicle: " . $item['license_plate'] . " (" . $item['vehicle_type'] . ")\n";
        } else {
            echo "✗ Error inserting vehicle " . $item['license_plate'] . ": " . $conn->error . "\n";
        }
    } else {
        echo "⊘ Vehicle " . $item['license_plate'] . " already exists\n";
    }
}

// Insert drivers if they don't exist
$drivers = [
    ['username' => 'driver1', 'full_name' => 'Rafiq Ahmed', 'phone' => '01712345678'],
    ['username' => 'driver2', 'full_name' => 'Hasan Khan', 'phone' => '01823456789'],
    ['username' => 'driver3', 'full_name' => 'Rashid Ali', 'phone' => '01934567890'],
    ['username' => 'driver4', 'full_name' => 'Karim Hassan', 'phone' => '01745678901'],
];

echo "\nInserting driver data...\n";

foreach ($drivers as $driver) {
    $check_sql = "SELECT user_id FROM Users WHERE username='" . $driver['username'] . "'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result && $check_result->num_rows === 0) {
        $sql = "INSERT INTO Users (username, password_hash, full_name, role, phone) 
                VALUES ('" . $driver['username'] . "', 'hash', '" . $driver['full_name'] . "', 'Driver', '" . $driver['phone'] . "')";
        
        if ($conn->query($sql) === TRUE) {
            echo "✓ Added driver: " . $driver['full_name'] . "\n";
        } else {
            echo "✗ Error adding driver: " . $conn->error . "\n";
        }
    } else {
        echo "⊘ Driver " . $driver['username'] . " already exists\n";
    }
}

// Insert dummy deliveries (only with valid orders)
echo "\nInserting delivery data...\n";

// Get existing orders
$order_result = $conn->query("SELECT order_id FROM Orders LIMIT 3");
$order_ids = [];
if ($order_result && $order_result->num_rows > 0) {
    while ($row = $order_result->fetch_assoc()) {
        $order_ids[] = $row['order_id'];
    }
}

$delivery_data = [
    ['order_id' => $order_ids[0] ?? null, 'driver_id' => 1, 'vehicle_id' => 1, 'status' => 'In Transit', 'pickup_time' => '2026-04-09 08:00:00', 'delivery_time' => null],
    ['order_id' => $order_ids[1] ?? null, 'driver_id' => 2, 'vehicle_id' => 2, 'status' => 'Assigned', 'pickup_time' => '2026-04-09 09:30:00', 'delivery_time' => null],
    ['order_id' => null, 'driver_id' => 3, 'vehicle_id' => 3, 'status' => 'Completed', 'pickup_time' => '2026-04-08 10:00:00', 'delivery_time' => '2026-04-08 14:30:00'],
];

foreach ($delivery_data as $item) {
    if ($item['order_id'] === null) {
        $order_id = 'NULL';
    } else {
        $order_id = $item['order_id'];
    }
    
    $pickup_time = $item['pickup_time'] ? "'" . $item['pickup_time'] . "'" : "NULL";
    $delivery_time = $item['delivery_time'] ? "'" . $item['delivery_time'] . "'" : "NULL";
    
    $sql = "INSERT INTO Deliveries (order_id, driver_id, vehicle_id, status, pickup_time, delivery_time) 
            VALUES ($order_id, " . $item['driver_id'] . ", " . $item['vehicle_id'] . ", '" . $item['status'] . "', $pickup_time, $delivery_time)";
    
    if ($conn->query($sql) === TRUE) {
        echo "✓ Added delivery: Status=" . $item['status'] . " Vehicle ID=" . $item['vehicle_id'] . "\n";
    } else {
        echo "✗ Error adding delivery: " . $conn->error . "\n";
    }
}

echo "\nTransport data population complete!\n";
$conn->close();
?>
