-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 09, 2026 at 06:53 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbms_scms`
--

-- --------------------------------------------------------

--
-- Table structure for table `Batches`
--

CREATE TABLE `Batches` (
  `batch_id` int(11) NOT NULL,
  `batch_number` varchar(50) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `farmer_id` int(11) DEFAULT NULL,
  `supervisor_id` int(11) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `purchase_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Batches`
--

INSERT INTO `Batches` (`batch_id`, `batch_number`, `product_id`, `farmer_id`, `supervisor_id`, `quantity`, `unit`, `purchase_date`) VALUES
(2, 'B001', 1, 1, 1, 100.00, 'kg', '2023-12-31 18:00:00'),
(4, 'B003', 3, 1, 1, 190.00, 'kg', '2026-04-07 18:00:00'),
(5, 'B004', 1, 1, 1, 80.00, 'kg', '2024-02-09 18:00:00'),
(6, 'B005', 2, 2, 1, 120.00, 'kg', '2024-02-29 18:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `Customers`
--

CREATE TABLE `Customers` (
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_type` enum('Super Shop','Local Market') NOT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Deliveries`
--

CREATE TABLE `Deliveries` (
  `delivery_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `transport_manager_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `status` enum('Assigned','In Transit','Completed') DEFAULT 'Assigned',
  `pickup_time` datetime DEFAULT NULL,
  `delivery_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Farmers`
--

CREATE TABLE `Farmers` (
  `farmer_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `contact_info` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Farmers`
--

INSERT INTO `Farmers` (`farmer_id`, `name`, `location`, `contact_info`) VALUES
(1, 'John Doe', 'Dhaka', NULL),
(2, 'Jane Smith', 'Chittagong', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Inventory`
--

CREATE TABLE `Inventory` (
  `inventory_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `current_stock` decimal(10,2) DEFAULT 0.00,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `IoT_Devices`
--

CREATE TABLE `IoT_Devices` (
  `device_id` int(11) NOT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `device_serial` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `IoT_Logs`
--

CREATE TABLE `IoT_Logs` (
  `log_id` bigint(20) NOT NULL,
  `device_id` int(11) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `speed` decimal(5,2) DEFAULT NULL,
  `gyroscope_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gyroscope_data`)),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Orders`
--

CREATE TABLE `Orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `sales_manager_id` int(11) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Verified','Processing','Shipped','Delivered') DEFAULT 'Pending',
  `total_amount` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Products`
--

CREATE TABLE `Products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Products`
--

INSERT INTO `Products` (`product_id`, `product_name`, `category`) VALUES
(1, 'Rice', NULL),
(2, 'Wheat', NULL),
(3, 'Potato', NULL),
(4, 'Rice', 'Grain'),
(5, 'Wheat', 'Grain'),
(6, 'Potato', 'Vegetable'),
(7, 'Rice', 'Grain'),
(8, 'Wheat', 'Grain'),
(9, 'Potato', 'Vegetable'),
(10, 'Rice', 'Grain'),
(11, 'Wheat', 'Grain'),
(12, 'Potato', 'Vegetable');

-- --------------------------------------------------------

--
-- Table structure for table `Quality_Checks`
--

CREATE TABLE `Quality_Checks` (
  `check_id` int(11) NOT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `officer_id` int(11) DEFAULT NULL,
  `quality_tag` varchar(50) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `check_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Quality_Checks`
--

INSERT INTO `Quality_Checks` (`check_id`, `batch_id`, `officer_id`, `quality_tag`, `comments`, `check_date`) VALUES
(2, 2, 2, 'Approved', 'Good quality', '2026-04-08 06:22:09'),
(3, 4, 2, 'Approved', 'Excellent', '2026-04-08 06:22:09'),
(4, 6, 2, 'Approved', 'High quality', '2026-04-08 06:22:09');

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('Field Supervisor','Quality Officer','Inventory Manager','Sales Manager','Transport Manager','Driver') NOT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`user_id`, `username`, `password_hash`, `full_name`, `role`, `phone`) VALUES
(1, 'supervisor1', 'hash', 'Supervisor One', 'Field Supervisor', '1234567890'),
(2, 'officer1', 'hash', 'Officer One', 'Quality Officer', '0987654321'),
(3, 'supervisor1', 'hash', 'Supervisor One', 'Field Supervisor', '1234567890'),
(4, 'officer1', 'hash', 'Officer One', 'Quality Officer', '0987654321');

-- --------------------------------------------------------

--
-- Table structure for table `Vehicles`
--

CREATE TABLE `Vehicles` (
  `vehicle_id` int(11) NOT NULL,
  `license_plate` varchar(20) DEFAULT NULL,
  `vehicle_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Batches`
--
ALTER TABLE `Batches`
  ADD PRIMARY KEY (`batch_id`),
  ADD UNIQUE KEY `batch_number` (`batch_number`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `farmer_id` (`farmer_id`),
  ADD KEY `supervisor_id` (`supervisor_id`);

--
-- Indexes for table `Customers`
--
ALTER TABLE `Customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `Deliveries`
--
ALTER TABLE `Deliveries`
  ADD PRIMARY KEY (`delivery_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `transport_manager_id` (`transport_manager_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `Farmers`
--
ALTER TABLE `Farmers`
  ADD PRIMARY KEY (`farmer_id`);

--
-- Indexes for table `Inventory`
--
ALTER TABLE `Inventory`
  ADD PRIMARY KEY (`inventory_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `IoT_Devices`
--
ALTER TABLE `IoT_Devices`
  ADD PRIMARY KEY (`device_id`),
  ADD UNIQUE KEY `device_serial` (`device_serial`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `IoT_Logs`
--
ALTER TABLE `IoT_Logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `device_id` (`device_id`);

--
-- Indexes for table `Orders`
--
ALTER TABLE `Orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `sales_manager_id` (`sales_manager_id`);

--
-- Indexes for table `Products`
--
ALTER TABLE `Products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `Quality_Checks`
--
ALTER TABLE `Quality_Checks`
  ADD PRIMARY KEY (`check_id`),
  ADD KEY `batch_id` (`batch_id`),
  ADD KEY `officer_id` (`officer_id`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `Vehicles`
--
ALTER TABLE `Vehicles`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD UNIQUE KEY `license_plate` (`license_plate`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Batches`
--
ALTER TABLE `Batches`
  MODIFY `batch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `Customers`
--
ALTER TABLE `Customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Deliveries`
--
ALTER TABLE `Deliveries`
  MODIFY `delivery_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Farmers`
--
ALTER TABLE `Farmers`
  MODIFY `farmer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Inventory`
--
ALTER TABLE `Inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `IoT_Devices`
--
ALTER TABLE `IoT_Devices`
  MODIFY `device_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `IoT_Logs`
--
ALTER TABLE `IoT_Logs`
  MODIFY `log_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Orders`
--
ALTER TABLE `Orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Products`
--
ALTER TABLE `Products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `Quality_Checks`
--
ALTER TABLE `Quality_Checks`
  MODIFY `check_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Vehicles`
--
ALTER TABLE `Vehicles`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Batches`
--
ALTER TABLE `Batches`
  ADD CONSTRAINT `batches_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `Products` (`product_id`),
  ADD CONSTRAINT `batches_ibfk_2` FOREIGN KEY (`farmer_id`) REFERENCES `Farmers` (`farmer_id`),
  ADD CONSTRAINT `batches_ibfk_3` FOREIGN KEY (`supervisor_id`) REFERENCES `Users` (`user_id`);

--
-- Constraints for table `Deliveries`
--
ALTER TABLE `Deliveries`
  ADD CONSTRAINT `deliveries_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `Orders` (`order_id`),
  ADD CONSTRAINT `deliveries_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `Users` (`user_id`),
  ADD CONSTRAINT `deliveries_ibfk_3` FOREIGN KEY (`transport_manager_id`) REFERENCES `Users` (`user_id`),
  ADD CONSTRAINT `deliveries_ibfk_4` FOREIGN KEY (`vehicle_id`) REFERENCES `Vehicles` (`vehicle_id`);

--
-- Constraints for table `Inventory`
--
ALTER TABLE `Inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `Products` (`product_id`);

--
-- Constraints for table `IoT_Devices`
--
ALTER TABLE `IoT_Devices`
  ADD CONSTRAINT `iot_devices_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `Vehicles` (`vehicle_id`);

--
-- Constraints for table `IoT_Logs`
--
ALTER TABLE `IoT_Logs`
  ADD CONSTRAINT `iot_logs_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `IoT_Devices` (`device_id`);

--
-- Constraints for table `Orders`
--
ALTER TABLE `Orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `Customers` (`customer_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`sales_manager_id`) REFERENCES `Users` (`user_id`);

--
-- Constraints for table `Quality_Checks`
--
ALTER TABLE `Quality_Checks`
  ADD CONSTRAINT `quality_checks_ibfk_1` FOREIGN KEY (`batch_id`) REFERENCES `Batches` (`batch_id`),
  ADD CONSTRAINT `quality_checks_ibfk_2` FOREIGN KEY (`officer_id`) REFERENCES `Users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
