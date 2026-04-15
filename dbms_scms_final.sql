-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 15, 2026 at 12:30 PM
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
(1, 'B17759102828385', 1, 1, 1, 100.50, 'kg', '2026-03-31 18:00:00'),
(2, 'B17759102823428', 2, 2, 1, 200.00, 'kg', '2026-04-01 18:00:00'),
(3, 'B17759102822452', 3, 3, 2, 150.75, 'kg', '2026-04-02 18:00:00'),
(4, 'B17759102827202', 1, 4, 1, 180.25, 'kg', '2026-04-03 18:00:00'),
(5, 'B17759102826780', 5, 2, 1, 220.00, 'kg', '2026-04-04 18:00:00'),
(6, 'B17759102827307', 2, 5, 2, 90.50, 'kg', '2026-04-05 18:00:00'),
(7, 'B1775912080', 1, 2, 1, 2.00, 'kg', '2026-04-10 18:00:00');

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

--
-- Dumping data for table `Customers`
--

INSERT INTO `Customers` (`customer_id`, `customer_name`, `customer_type`, `address`) VALUES
(1, 'Super Shop Dhaka', 'Super Shop', 'Motijheel, Dhaka'),
(2, 'Super Shop Bogra', 'Super Shop', 'Gabtoli, Bogra'),
(3, 'Local Market Shariatpur', 'Local Market', 'Bazaar Rd, Shariatpur'),
(4, 'Local Market Rajshahi', 'Local Market', 'Station Rd, Rajshahi'),
(5, 'Super Shop', 'Super Shop', 'iub'),
(6, 'zaid oop', 'Super Shop', 'asd'),
(7, 'local', 'Local Market', 'qq');

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

--
-- Dumping data for table `Deliveries`
--

INSERT INTO `Deliveries` (`delivery_id`, `order_id`, `driver_id`, `transport_manager_id`, `vehicle_id`, `status`, `pickup_time`, `delivery_time`) VALUES
(1, 1, 1, 9, 1, 'Completed', '2026-04-11 18:24:42', '2026-04-11 18:59:14'),
(2, 2, 2, 9, 2, 'In Transit', '2026-04-11 18:24:42', NULL),
(3, 3, 1, 9, 3, 'In Transit', '2026-04-11 18:24:42', NULL),
(4, 4, 1, 9, 1, 'Assigned', '2026-04-11 18:24:42', NULL),
(5, 5, 2, 9, 2, 'Completed', '2026-04-11 18:24:42', NULL),
(6, 6, 1, 9, 4, 'In Transit', '2026-04-11 18:24:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Demand_Forecast`
--

CREATE TABLE `Demand_Forecast` (
  `id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `current_stock` int(11) NOT NULL,
  `weekly_demand` int(11) NOT NULL,
  `monthly_forecast` int(11) NOT NULL,
  `recommended_stock` int(11) NOT NULL,
  `status` enum('Adequate','Low Stock','Overstock') DEFAULT 'Adequate',
  `forecast_date` date DEFAULT curdate(),
  `accuracy_percentage` int(11) DEFAULT 85
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Demand_Forecast`
--

INSERT INTO `Demand_Forecast` (`id`, `product_name`, `current_stock`, `weekly_demand`, `monthly_forecast`, `recommended_stock`, `status`, `forecast_date`, `accuracy_percentage`) VALUES
(1, 'Rice', 1200, 800, 3200, 4000, 'Low Stock', '2026-04-11', 85),
(2, 'Wheat', 900, 600, 2400, 3000, 'Adequate', '2026-04-11', 85),
(3, 'Potatoes', 500, 400, 1600, 2000, 'Low Stock', '2026-04-11', 85),
(4, 'Tomatoes', 300, 250, 1000, 1200, 'Adequate', '2026-04-11', 85),
(5, 'Onions', 400, 350, 1400, 1600, 'Adequate', '2026-04-11', 85);

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
(1, 'Rahim Khan', 'Shariatpur', '0181111111'),
(2, 'Karim Ahmed', 'Madaripur', '0182222222'),
(3, 'Salam Hossain', 'Faridpur', '0183333333'),
(4, 'Rahman Khan', 'Gopalganj', '0184444444'),
(5, 'Habib Hassan', 'Rajbari', '0185555555'),
(7, 'zaid', 'var3', 'dasd');

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

--
-- Dumping data for table `Inventory`
--

INSERT INTO `Inventory` (`inventory_id`, `product_id`, `current_stock`, `last_updated`) VALUES
(1, 1, 5000.50, '2026-04-11 12:24:42'),
(2, 2, 3200.75, '2026-04-11 12:24:42'),
(3, 3, 8500.00, '2026-04-11 12:24:42'),
(4, 4, 1200.25, '2026-04-11 12:24:42'),
(5, 5, 450.50, '2026-04-11 12:24:42'),
(6, 6, 2200.00, '2026-04-11 12:24:42'),
(7, 7, 790.75, '2026-04-11 12:56:40'),
(8, 8, 1500.00, '2026-04-11 12:24:42'),
(9, 9, 3300.50, '2026-04-11 12:24:42'),
(10, 10, 600.00, '2026-04-11 12:24:42'),
(11, 11, 950.25, '2026-04-11 12:24:42'),
(12, 12, 1100.50, '2026-04-11 12:24:42');

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
-- Table structure for table `Market_Orders`
--

CREATE TABLE `Market_Orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(20) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_kg` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Fulfilled','Cancelled') DEFAULT 'Pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_name` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Market_Orders`
--

INSERT INTO `Market_Orders` (`id`, `order_id`, `product_name`, `quantity`, `price_per_kg`, `total_amount`, `status`, `order_date`, `customer_name`, `notes`) VALUES
(1, 'LM-001', 'Rice', 500, 50.00, 25000.00, 'Fulfilled', '2026-04-11 12:03:03', 'Super Shop A', NULL),
(2, 'LM-002', 'Potatoes', 300, 25.00, 7500.00, 'Pending', '2026-04-11 12:03:03', 'Local Retailer B', NULL),
(3, 'LM-003', 'Tomatoes', 200, 40.00, 8000.00, 'Fulfilled', '2026-04-11 12:03:03', 'Restaurant C', NULL),
(4, 'LM-004', 'Onions', 400, 30.00, 12000.00, 'Pending', '2026-04-11 12:03:03', 'Wholesale D', NULL),
(5, 'LM-005', 'Carrots', 150, 35.00, 5250.00, 'Fulfilled', '2026-04-11 12:03:03', 'Grocery Store E', NULL);

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

--
-- Dumping data for table `Orders`
--

INSERT INTO `Orders` (`order_id`, `customer_id`, `sales_manager_id`, `order_date`, `status`, `total_amount`) VALUES
(1, 1, 7, '2026-04-11 12:24:42', 'Processing', 45000.00),
(2, 2, 7, '2026-04-11 12:24:42', 'Processing', 62000.00),
(3, 1, 8, '2026-04-11 12:24:42', 'Shipped', 35000.00),
(4, 3, 7, '2026-04-11 12:24:42', 'Processing', 28500.00),
(5, 2, 8, '2026-04-11 12:24:42', 'Delivered', 71000.00),
(6, 4, 8, '2026-04-11 12:24:42', 'Processing', 42000.00),
(7, 5, NULL, '2026-04-11 13:00:43', 'Processing', 0.00),
(8, 7, NULL, '2026-04-14 18:00:00', 'Processing', 122.00);

-- --------------------------------------------------------

--
-- Table structure for table `Price_Trends`
--

CREATE TABLE `Price_Trends` (
  `id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `current_price` decimal(10,2) NOT NULL,
  `last_week_price` decimal(10,2) DEFAULT NULL,
  `last_month_price` decimal(10,2) DEFAULT NULL,
  `trend` enum('Rising','Falling','Stable') DEFAULT 'Stable',
  `recorded_date` date DEFAULT curdate(),
  `source` varchar(50) DEFAULT 'Market Survey'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Price_Trends`
--

INSERT INTO `Price_Trends` (`id`, `product_name`, `current_price`, `last_week_price`, `last_month_price`, `trend`, `recorded_date`, `source`) VALUES
(1, 'Rice', 50.00, 48.00, 45.00, 'Rising', '2026-04-11', 'Market Survey'),
(2, 'Wheat', 45.00, 45.00, 44.00, 'Stable', '2026-04-11', 'Market Survey'),
(3, 'Potatoes', 25.00, 26.00, 28.00, 'Falling', '2026-04-11', 'Market Survey'),
(4, 'Tomatoes', 40.00, 38.00, 35.00, 'Rising', '2026-04-11', 'Market Survey'),
(5, 'Onions', 30.00, 32.00, 33.00, 'Falling', '2026-04-11', 'Market Survey'),
(6, 'Carrots', 35.00, 34.00, 32.00, 'Rising', '2026-04-11', 'Market Survey');

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
(1, 'Rice', 'Grain'),
(2, 'Wheat', 'Grain'),
(3, 'Potatoes', 'Vegetable'),
(4, 'Tomatoes', 'Vegetable'),
(5, 'Onions', 'Vegetable'),
(6, 'Carrots', 'Vegetable'),
(7, 'Beans', 'Legume'),
(8, 'Lentils', 'Legume'),
(9, 'Corn', 'Grain'),
(10, 'Cabbage', 'Vegetable'),
(11, 'Lettuce', 'Vegetable'),
(12, 'Cucumber', 'Vegetable');

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
(1, 1, 3, 'Approved', 'Good quality, fresh produce', '2026-04-11 12:24:42'),
(2, 2, 4, 'Approved', 'Excellent condition', '2026-04-11 12:24:42'),
(3, 3, 3, 'Approved', 'Meets all standards', '2026-04-11 12:24:42'),
(4, 4, 4, 'Rejected', 'Slight damage detected', '2026-04-11 12:24:42'),
(5, 5, 3, 'Approved', 'Premium quality', '2026-04-11 12:24:42'),
(6, 6, 4, 'Pending', 'Under review', '2026-04-11 12:24:42');

-- --------------------------------------------------------

--
-- Table structure for table `SuperShop_Orders`
--

CREATE TABLE `SuperShop_Orders` (
  `super_shop_order_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL DEFAULT 'Super Shop',
  `delivery_address` text NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('Pending','Verified','Processing','Shipped','Delivered') DEFAULT 'Pending',
  `total_amount` decimal(12,2) DEFAULT 0.00,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `SuperShop_Orders`
--

INSERT INTO `SuperShop_Orders` (`super_shop_order_id`, `customer_name`, `delivery_address`, `delivery_date`, `notes`, `status`, `total_amount`, `order_date`) VALUES
(1, 'Super Shop Dhaka', '123 Main Street, Dhaka', '2024-12-15', 'Urgent delivery needed', 'Delivered', 2500.00, '2026-03-22 12:24:42'),
(2, 'Super Shop Chittagong', '456 Market Road, Chittagong', '2024-12-16', 'Handle with care', 'Processing', 1800.00, '2026-03-22 12:24:42'),
(3, 'Super Shop Khulna', '789 River View, Khulna', '2024-12-17', 'Evening delivery', 'Shipped', 3200.00, '2026-03-24 12:24:42'),
(4, 'Super Shop Rajshahi', '321 College Road, Rajshahi', '2024-12-18', 'Contact before delivery', 'Pending', 1500.00, '2026-03-21 12:24:42'),
(5, 'Super Shop Sylhet', '654 Tea Garden, Sylhet', '2024-12-19', 'Fragile items', 'Delivered', 2100.00, '2026-03-12 12:24:42'),
(6, 'Super Shop Barisal', '987 River Side, Barisal', '2024-12-20', 'Large order', 'Processing', 2800.00, '2026-03-14 12:24:42'),
(7, 'Super Shop', 'iub', '2026-04-23', 'iub', 'Processing', 0.00, '2026-04-11 13:00:43');

-- --------------------------------------------------------

--
-- Table structure for table `SuperShop_Order_Items`
--

CREATE TABLE `SuperShop_Order_Items` (
  `order_item_id` int(11) NOT NULL,
  `super_shop_order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(20) NOT NULL DEFAULT 'kg',
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `SuperShop_Order_Items`
--

INSERT INTO `SuperShop_Order_Items` (`order_item_id`, `super_shop_order_id`, `product_id`, `quantity`, `unit`, `unit_price`, `line_total`) VALUES
(1, 1, 1, 1.00, 'kg', 50.00, 25.00),
(2, 1, 1, 3.00, 'kg', 30.00, 45.00),
(3, 2, 1, 5.00, 'kg', 20.00, 100.00),
(4, 2, 2, 2.00, 'kg', 40.00, 60.00),
(5, 3, 2, 4.00, 'kg', 25.00, 72.00),
(6, 3, 3, 1.00, 'kg', 60.00, 26.00),
(7, 4, 3, 6.00, 'kg', 35.00, 132.00),
(8, 4, 3, 8.00, 'kg', 15.00, 360.00),
(9, 5, 4, 9.00, 'kg', 45.00, 252.00),
(10, 5, 4, 10.00, 'kg', 20.00, 120.00),
(11, 6, 5, 11.00, 'kg', 30.00, 176.00),
(12, 6, 5, 12.00, 'kg', 25.00, 168.00),
(13, 1, 6, 7.00, 'kg', 40.00, 245.00),
(14, 1, 6, 2.00, 'kg', 55.00, 62.00),
(15, 7, 6, 1.97, 'kg', 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `SuperShop_Order_Refs`
--

CREATE TABLE `SuperShop_Order_Refs` (
  `super_shop_order_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `SuperShop_Order_Refs`
--

INSERT INTO `SuperShop_Order_Refs` (`super_shop_order_id`, `order_id`) VALUES
(7, 7);

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
(1, 'supervisor1', '$2y$10$JxwBkoivOcWXwWt2P8chveDwgKFUX3oR.df2wvqPLUkLbXWlzvoIW', 'Ahmed Supervisor', 'Field Supervisor', '0171234567'),
(2, 'supervisor2', '$2y$10$Te8hM.NUkfG94DQD/.opVeNUPTUCx7LBesSDydYmaQx7UQka8tdqi', 'Karim Supervisor', 'Field Supervisor', '0172345678'),
(3, 'officer1', '$2y$10$u8XPTL6F8todOWrtuxbaIuSn9JtPht5xhzErJdhFbLWOBjYPnoq7W', 'Fatima Officer', 'Quality Officer', '0173456789'),
(4, 'officer2', '$2y$10$pNY/5M1/tdbdjTBOYsLPuOH5Q7P82e6PTpny8dK5Sb165UwyWROVm', 'Habiba Officer', 'Quality Officer', '0174567890'),
(5, 'inventory1', '$2y$10$930h4vHjQRHOiboe7JvBz.pUeU3PuRpCvlk5K7lbRxn0n3A7sPkpm', 'Muhammad Inventory', 'Inventory Manager', '0175678901'),
(6, 'inv2', '$2y$10$aMWuJLv.EZ5gPKwo2TmXxe/NTMMnMjY.gD1bT4qAYrrOSt4Gux5xu', 'Nadia Inventory', 'Inventory Manager', '0176789012'),
(7, 'sales1', '$2y$10$st54JtCqjOik4d02z1afaulU0po2RN7bgYdEiKpaDQX9OxfQXXxWW', 'Hassan Sales', 'Sales Manager', '0177890123'),
(8, 'sales2', '$2y$10$ZxZkyyWv/2wAQtfAL4aLWuXYM1v0g0WXxDEJFT0ir287ha2haNMGC', 'Amina Sales', 'Sales Manager', '0178901234'),
(9, 'transport1', '$2y$10$vZ96IqHYWwhopv3iwhQp4.yl7v5xVD5.9FPxrvI.RzjeCfyFtVlPu', 'Ibrahim Transport', 'Transport Manager', '0179012345'),
(10, 'driver1', '$2y$10$nN8U1dZalsm0f0./WaGbNuEe4GHxjbh0KPO/cZ0LIywNnBnK9K1WS', 'Ali Driver', 'Driver', '0180123456'),
(11, 'driver2', '$2y$10$u.zVcbtcjD6brZKxoe1LrOvxpTy9I4/aaC6x7VsTJVxHjRMkUWOea', 'Omar Driver', 'Driver', '0181234567');

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
-- Dumping data for table `Vehicles`
--

INSERT INTO `Vehicles` (`vehicle_id`, `license_plate`, `vehicle_type`) VALUES
(1, 'TR-001', 'Truck'),
(2, 'TR-002', 'Truck'),
(3, 'VAN-001', 'Van'),
(4, 'VAN-002', 'Van');

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
-- Indexes for table `Demand_Forecast`
--
ALTER TABLE `Demand_Forecast`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `Market_Orders`
--
ALTER TABLE `Market_Orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Orders`
--
ALTER TABLE `Orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `sales_manager_id` (`sales_manager_id`);

--
-- Indexes for table `Price_Trends`
--
ALTER TABLE `Price_Trends`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `SuperShop_Orders`
--
ALTER TABLE `SuperShop_Orders`
  ADD PRIMARY KEY (`super_shop_order_id`);

--
-- Indexes for table `SuperShop_Order_Items`
--
ALTER TABLE `SuperShop_Order_Items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `super_shop_order_id` (`super_shop_order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `SuperShop_Order_Refs`
--
ALTER TABLE `SuperShop_Order_Refs`
  ADD PRIMARY KEY (`super_shop_order_id`,`order_id`),
  ADD KEY `order_id` (`order_id`);

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
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `Deliveries`
--
ALTER TABLE `Deliveries`
  MODIFY `delivery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `Demand_Forecast`
--
ALTER TABLE `Demand_Forecast`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Farmers`
--
ALTER TABLE `Farmers`
  MODIFY `farmer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `Inventory`
--
ALTER TABLE `Inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
-- AUTO_INCREMENT for table `Market_Orders`
--
ALTER TABLE `Market_Orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Orders`
--
ALTER TABLE `Orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `Price_Trends`
--
ALTER TABLE `Price_Trends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `Products`
--
ALTER TABLE `Products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `Quality_Checks`
--
ALTER TABLE `Quality_Checks`
  MODIFY `check_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `SuperShop_Orders`
--
ALTER TABLE `SuperShop_Orders`
  MODIFY `super_shop_order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `SuperShop_Order_Items`
--
ALTER TABLE `SuperShop_Order_Items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `Vehicles`
--
ALTER TABLE `Vehicles`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
