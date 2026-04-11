<?php
/**
 * Master Database Population Script
 * Populates all core data for the DBMS-SCM system
 */

include "db.php";

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Disable foreign key checks before truncating
    $conn->query("SET FOREIGN_KEY_CHECKS=0");
    
    // Clear existing data (maintain referential integrity)
    echo "Clearing existing data...\n";
    $tables_to_clear = ['Deliveries', 'Orders', 'Quality_Checks', 'Inventory', 'Batches', 'Customers', 'Farmers', 'Users', 'Vehicles', 'Products', 'SuperShop_Order_Items', 'SuperShop_Order_Refs', 'SuperShop_Orders'];
    foreach ($tables_to_clear as $table) {
        $conn->query("TRUNCATE TABLE $table");
    }
    
    // Re-enable foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS=1");

    // === Users ===
    echo "Populating Users...\n";
    $users = [
        ['supervisor1', 'Field Supervisor', 'Ahmed Supervisor', '0171234567'],
        ['supervisor2', 'Field Supervisor', 'Karim Supervisor', '0172345678'],
        ['officer1', 'Quality Officer', 'Fatima Officer', '0173456789'],
        ['officer2', 'Quality Officer', 'Habiba Officer', '0174567890'],
        ['inventory1', 'Inventory Manager', 'Muhammad Inventory', '0175678901'],
        ['inv2', 'Inventory Manager', 'Nadia Inventory', '0176789012'],
        ['sales1', 'Sales Manager', 'Hassan Sales', '0177890123'],
        ['sales2', 'Sales Manager', 'Amina Sales', '0178901234'],
        ['transport1', 'Transport Manager', 'Ibrahim Transport', '0179012345'],
        ['driver1', 'Driver', 'Ali Driver', '0180123456'],
        ['driver2', 'Driver', 'Omar Driver', '0181234567'],
    ];

    foreach ($users as $user) {
        $username = $user[0];
        $role = $user[1];
        $full_name = $user[2];
        $phone = $user[3];
        $password_hash = password_hash('password123', PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO Users (username, password_hash, full_name, role, phone) VALUES ('$username', '$password_hash', '$full_name', '$role', '$phone')";
        if (!$conn->query($sql)) {
            echo "Error inserting user $username: " . $conn->error . "\n";
        }
    }

    // === Products ===
    echo "Populating Products...\n";
    $products = [
        ['Rice', 'Grain'],
        ['Wheat', 'Grain'],
        ['Potatoes', 'Vegetable'],
        ['Tomatoes', 'Vegetable'],
        ['Onions', 'Vegetable'],
        ['Carrots', 'Vegetable'],
        ['Beans', 'Legume'],
        ['Lentils', 'Legume'],
        ['Corn', 'Grain'],
        ['Cabbage', 'Vegetable'],
        ['Lettuce', 'Vegetable'],
        ['Cucumber', 'Vegetable'],
    ];

    foreach ($products as $product) {
        $product_name = $product[0];
        $category = $product[1];
        $sql = "INSERT INTO Products (product_name, category) VALUES ('$product_name', '$category')";
        if (!$conn->query($sql)) {
            echo "Error inserting product $product_name: " . $conn->error . "\n";
        }
    }

    // === Farmers ===
    echo "Populating Farmers...\n";
    $farmers = [
        ['Rahim Khan', 'Shariatpur', '0181111111'],
        ['Karim Ahmed', 'Madaripur', '0182222222'],
        ['Salam Hossain', 'Faridpur', '0183333333'],
        ['Rahman Khan', 'Gopalganj', '0184444444'],
        ['Habib Hassan', 'Rajbari', '0185555555'],
    ];

    $farmer_ids = [];
    foreach ($farmers as $farmer) {
        $name = $farmer[0];
        $location = $farmer[1];
        $contact_info = $farmer[2];
        $sql = "INSERT INTO Farmers (name, location, contact_info) VALUES ('$name', '$location', '$contact_info')";
        if ($conn->query($sql)) {
            $farmer_ids[] = $conn->insert_id;
        } else {
            echo "Error inserting farmer $name: " . $conn->error . "\n";
        }
    }

    // === Batches ===
    echo "Populating Batches...\n";
    $batches = [
        [1, 1, 1, 100.50, 'kg', '2026-04-01'],
        [2, 2, 1, 200.00, 'kg', '2026-04-02'],
        [3, 3, 2, 150.75, 'kg', '2026-04-03'],
        [1, 4, 1, 180.25, 'kg', '2026-04-04'],
        [5, 2, 1, 220.00, 'kg', '2026-04-05'],
        [2, 5, 2, 90.50, 'kg', '2026-04-06'],
    ];

    $batch_ids = [];
    foreach ($batches as $batch) {
        $product_id = $batch[0];
        $farmer_id = $batch[1];
        $supervisor_id = $batch[2];
        $quantity = $batch[3];
        $unit = $batch[4];
        $date = $batch[5];
        $batch_number = 'B' . time() . rand(1000, 9999);
        
        $sql = "INSERT INTO Batches (batch_number, product_id, farmer_id, supervisor_id, quantity, unit, purchase_date) 
                VALUES ('$batch_number', '$product_id', '$farmer_id', '$supervisor_id', '$quantity', '$unit', '$date')";
        if ($conn->query($sql)) {
            $batch_ids[] = $conn->insert_id;
        } else {
            echo "Error inserting batch: " . $conn->error . "\n";
        }
    }

    // === Quality Checks ===
    echo "Populating Quality Checks...\n";
    $quality_tags = ['Approved', 'Approved', 'Approved', 'Rejected', 'Approved', 'Pending'];
    $quality_comments = [
        'Good quality, fresh produce',
        'Excellent condition',
        'Meets all standards',
        'Slight damage detected',
        'Premium quality',
        'Under review'
    ];

    foreach ($batch_ids as $idx => $batch_id) {
        $officer_id = ($idx % 2) + 3; // Alternate between officer1 and officer2 (IDs 3, 4)
        $quality_tag = $quality_tags[$idx] ?? 'Pending';
        $comments = $quality_comments[$idx] ?? '';
        $sql = "INSERT INTO Quality_Checks (batch_id, officer_id, quality_tag, comments, check_date) 
                VALUES ('$batch_id', '$officer_id', '$quality_tag', '$comments', NOW())";
        if (!$conn->query($sql)) {
            echo "Error inserting quality check: " . $conn->error . "\n";
        }
    }

    // === Inventory ===
    echo "Populating Inventory...\n";
    $inventory_data = [
        [1, 5000.50],
        [2, 3200.75],
        [3, 8500.00],
        [4, 1200.25],
        [5, 450.50],
        [6, 2200.00],
        [7, 800.75],
        [8, 1500.00],
        [9, 3300.50],
        [10, 600.00],
        [11, 950.25],
        [12, 1100.50],
    ];

    foreach ($inventory_data as $inv) {
        $product_id = $inv[0];
        $stock = $inv[1];
        $sql = "INSERT INTO Inventory (product_id, current_stock, last_updated) 
                VALUES ('$product_id', '$stock', NOW())";
        if (!$conn->query($sql)) {
            echo "Error inserting inventory: " . $conn->error . "\n";
        }
    }

    // === Customers ===
    echo "Populating Customers...\n";
    $customers = [
        ['Super Shop Dhaka', 'Super Shop', 'Motijheel, Dhaka'],
        ['Super Shop Bogra', 'Super Shop', 'Gabtoli, Bogra'],
        ['Local Market Shariatpur', 'Local Market', 'Bazaar Rd, Shariatpur'],
        ['Local Market Rajshahi', 'Local Market', 'Station Rd, Rajshahi'],
    ];

    $customer_ids = [];
    foreach ($customers as $customer) {
        $name = $customer[0];
        $type = $customer[1];
        $address = $customer[2];
        $sql = "INSERT INTO Customers (customer_name, customer_type, address) 
                VALUES ('$name', '$type', '$address')";
        if ($conn->query($sql)) {
            $customer_ids[] = $conn->insert_id;
        } else {
            echo "Error inserting customer: " . $conn->error . "\n";
        }
    }

    // === Orders ===
    echo "Populating Orders...\n";
    $orders = [
        [1, 7, 'Pending', 45000.00],
        [2, 7, 'Processing', 62000.00],
        [1, 8, 'Shipped', 35000.00],
        [3, 7, 'Pending', 28500.00],
        [2, 8, 'Delivered', 71000.00],
        [4, 8, 'Processing', 42000.00],
    ];

    $order_ids = [];
    foreach ($orders as $order) {
        $customer_id = $order[0];
        $sales_manager_id = $order[1];
        $status = $order[2];
        $total_amount = $order[3];
        $sql = "INSERT INTO Orders (customer_id, sales_manager_id, order_date, status, total_amount) 
                VALUES ('$customer_id', '$sales_manager_id', NOW(), '$status', '$total_amount')";
        if ($conn->query($sql)) {
            $order_ids[] = $conn->insert_id;
        } else {
            echo "Error inserting order: " . $conn->error . "\n";
        }
    }

    // === Vehicles ===
    echo "Populating Vehicles...\n";
    $vehicles = [
        ['TR-001', 'Truck'],
        ['TR-002', 'Truck'],
        ['VAN-001', 'Van'],
        ['VAN-002', 'Van'],
    ];

    $vehicle_ids = [];
    foreach ($vehicles as $vehicle) {
        $license_plate = $vehicle[0];
        $vehicle_type = $vehicle[1];
        $sql = "INSERT INTO Vehicles (license_plate, vehicle_type) 
                VALUES ('$license_plate', '$vehicle_type')";
        if ($conn->query($sql)) {
            $vehicle_ids[] = $conn->insert_id;
        } else {
            echo "Error inserting vehicle: " . $conn->error . "\n";
        }
    }

    // === Deliveries ===
    echo "Populating Deliveries...\n";
    $deliveries = [
        [1, 1, 9, 1, 'Assigned'],
        [2, 2, 9, 2, 'In Transit'],
        [3, 1, 9, 3, 'Completed'],
        [4, 1, 9, 1, 'Assigned'],
        [5, 2, 9, 2, 'Completed'],
        [6, 1, 9, 4, 'In Transit'],
    ];

    foreach ($deliveries as $delivery) {
        $order_id = $delivery[0];
        $driver_id = $delivery[1];
        $transport_manager_id = $delivery[2];
        $vehicle_id = $delivery[3];
        $status = $delivery[4];
        $sql = "INSERT INTO Deliveries (order_id, driver_id, transport_manager_id, vehicle_id, status, pickup_time, delivery_time) 
                VALUES ('$order_id', '$driver_id', '$transport_manager_id', '$vehicle_id', '$status', NOW(), NULL)";
        if (!$conn->query($sql)) {
            echo "Error inserting delivery: " . $conn->error . "\n";
        }
    }

    // === SuperShop Orders ===
    echo "Populating SuperShop Orders...\n";
    $supershop_orders = [
        ['Super Shop Dhaka', '123 Main Street, Dhaka', '2024-12-15', 'Urgent delivery needed', 'Delivered', 2500.00],
        ['Super Shop Chittagong', '456 Market Road, Chittagong', '2024-12-16', 'Handle with care', 'Processing', 1800.00],
        ['Super Shop Khulna', '789 River View, Khulna', '2024-12-17', 'Evening delivery', 'Shipped', 3200.00],
        ['Super Shop Rajshahi', '321 College Road, Rajshahi', '2024-12-18', 'Contact before delivery', 'Pending', 1500.00],
        ['Super Shop Sylhet', '654 Tea Garden, Sylhet', '2024-12-19', 'Fragile items', 'Delivered', 2100.00],
        ['Super Shop Barisal', '987 River Side, Barisal', '2024-12-20', 'Large order', 'Processing', 2800.00],
    ];

    $supershop_order_ids = [];
    foreach ($supershop_orders as $order) {
        $customer_name = $order[0];
        $delivery_address = $order[1];
        $delivery_date = $order[2];
        $notes = $order[3];
        $status = $order[4];
        $total_amount = $order[5];
        $sql = "INSERT INTO SuperShop_Orders (customer_name, delivery_address, delivery_date, notes, status, total_amount, order_date) 
                VALUES ('$customer_name', '$delivery_address', '$delivery_date', '$notes', '$status', '$total_amount', DATE_SUB(NOW(), INTERVAL " . rand(1, 30) . " DAY))";
        if ($conn->query($sql)) {
            $supershop_order_ids[] = $conn->insert_id;
        } else {
            echo "Error inserting SuperShop order: " . $conn->error . "\n";
        }
    }

    // === SuperShop Order Items ===
    echo "Populating SuperShop Order Items...\n";
    $supershop_items = [
        [1, 1, 50.00, 'kg', 25.00], // Rice
        [1, 3, 30.00, 'kg', 15.00], // Potatoes
        [1, 5, 20.00, 'kg', 20.00], // Onions
        [2, 2, 40.00, 'kg', 30.00], // Wheat
        [2, 4, 25.00, 'kg', 18.00], // Tomatoes
        [3, 1, 60.00, 'kg', 26.00], // Rice
        [3, 6, 35.00, 'kg', 22.00], // Carrots
        [3, 8, 15.00, 'kg', 45.00], // Lentils
        [4, 9, 45.00, 'kg', 28.00], // Corn
        [4, 10, 20.00, 'kg', 12.00], // Cabbage
        [5, 11, 30.00, 'kg', 16.00], // Lettuce
        [5, 12, 25.00, 'kg', 14.00], // Cucumber
        [6, 7, 40.00, 'kg', 35.00], // Beans
        [6, 2, 55.00, 'kg', 31.00], // Wheat
    ];

    foreach ($supershop_items as $index => $item) {
        $product_id = $item[0];
        $quantity = $item[1];
        $unit_price = $item[2];
        $unit = $item[3];
        $line_total = $item[4] * $quantity; // unit_price * quantity
        
        // Distribute items across orders
        $order_index = floor($index / 2) % count($supershop_order_ids);
        $super_shop_order_id = $supershop_order_ids[$order_index];
        
        $sql = "INSERT INTO SuperShop_Order_Items (super_shop_order_id, product_id, quantity, unit, unit_price, line_total) 
                VALUES ('$super_shop_order_id', '$product_id', '$quantity', '$unit', '$unit_price', '$line_total')";
        if (!$conn->query($sql)) {
            echo "Error inserting SuperShop order item: " . $conn->error . "\n";
        }
    }

    echo "\n✅ Database population completed successfully!\n";
    echo "Summary:\n";
    echo "- Users: " . count($users) . "\n";
    echo "- Products: " . count($products) . "\n";
    echo "- Farmers: " . count($farmers) . "\n";
    echo "- Batches: " . count($batches) . "\n";
    echo "- Quality Checks: " . count($batch_ids) . "\n";
    echo "- Inventory: " . count($inventory_data) . "\n";
    echo "- Customers: " . count($customers) . "\n";
    echo "- Orders: " . count($orders) . "\n";
    echo "- Vehicles: " . count($vehicles) . "\n";
    echo "- Deliveries: " . count($deliveries) . "\n";
    echo "- SuperShop Orders: " . count($supershop_orders) . "\n";
    echo "- SuperShop Order Items: " . count($supershop_items) . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
?></content>
<parameter name="filePath">/Applications/XAMPP/xamppfiles/htdocs/dbms-scm/populate_db.php