# DBMS-SCM Database Population Guide

## Overview
The system is now fully populated with test data for all user roles. Each role has specific credentials and data to work with.

## How to Populate the Database

### Method 1: Master Population (Recommended)
Populates ALL data for the entire system at once:
```
/populate_db.php
```
Access via browser: http://localhost/dbms-scm/populate_db.php

### Method 2: Role-Specific Population
Each role folder also has a populate script for reference:
- Field Supervisor: `/field_supervisor/populate_db.php`
- Super Shop: `/super_shop/populate_db.php`
- Driver: `/driver/populate_db.php`
- Inventory Manager: `/inventory_manager/populate_db.php`
- Quality Officer: `/quality_officer/populate_db.php`
- Sales Manager: `/sales_manager/populate_db.php`
- Transport Manager: `/transport_manager/populate_db.php`
- Farmer: `/farmer/populate_db.php`

## User Credentials

### Field Supervisor
- **Username:** supervisor1 or supervisor2
- **Password:** password123
- **Dashboard:** http://localhost/dbms-scm/field_supervisor/dashboard.php
- **Responsibilities:** Manage farmers, record purchases, supervise batches

### Quality Officer
- **Username:** officer1 or officer2
- **Password:** password123
- **Dashboard:** http://localhost/dbms-scm/quality_officer/dashboard.php
- **Responsibilities:** Approve/reject batches, quality checks

### Inventory Manager
- **Username:** inventory1 or inv2
- **Password:** password123
- **Dashboard:** http://localhost/dbms-scm/inventory_manager/dashboard.php
- **Responsibilities:** Manage stock, track low stock items, update inventory

### Sales Manager
- **Username:** sales1 or sales2
- **Password:** password123
- **Dashboard:** http://localhost/dbms-scm/sales_manager/dashboard.php
- **Responsibilities:** Manage customers, process orders, sales reports

### Transport Manager
- **Username:** transport1
- **Password:** password123
- **Dashboard:** http://localhost/dbms-scm/transport_manager/dashboard.php
- **Responsibilities:** Manage fleet, coordinate deliveries

### Driver
- **Username:** driver1 or driver2
- **Password:** password123
- **Dashboard:** http://localhost/dbms-scm/driver/dashboard.php
- **Responsibilities:** Track deliveries, update delivery status, manage pickups

### Farmer
- **Farmer IDs:** 1-5
- **Dashboard:** http://localhost/dbms-scm/farmer/dashboard.php
- **Farmers:**
  - Rahim Khan (Shariatpur)
  - Karim Ahmed (Madaripur)
  - Salam Hossain (Faridpur)
  - Rahman Khan (Gopalganj)
  - Habib Hassan (Rajbari)

## Database Schema

### Core Tables
- **Users:** System user accounts by role
- **Farmers:** Registered farmers with location and contact info
- **Products:** 12 product types (Rice, Wheat, Potatoes, etc.)
- **Batches:** 6 batches with farmer and product information
- **Quality_Checks:** Quality approval records for batches
- **Inventory:** Current stock levels for all products
- **Customers:** 4 customers (Super Shops and Local Markets)
- **Orders:** 6 orders in various statuses
- **Deliveries:** 6 delivery records with driver assignments
- **Vehicles:** 4 vehicles (Trucks and Vans)

## Sample Data Population

### Products (12 total)
- Grains: Rice, Wheat, Corn
- Vegetables: Potatoes, Tomatoes, Onions, Carrots, Cabbage, Lettuce, Cucumber
- Legumes: Beans, Lentils

### Farmers (5 total)
All farmers have batches in the system for testing

### Inventory Status
- Total Stock: ~26,000+ units
- Low Stock Items: 4 products below 1000 units
- All products have initial stock levels for testing

### Order Status Distribution
- Pending: 2 orders
- Processing: 2 orders
- Shipped: 1 order
- Delivered: 1 order

## Testing Workflow

### Suggested Testing Path
1. **Field Supervisor** → Create a purchase / manage farmers
2. **Quality Officer** → Approve pending batches
3. **Inventory Manager** → Check updated stock levels
4. **Sales Manager** → Create orders from customer requests
5. **Transport Manager** → Assign deliveries
6. **Driver** → Update delivery status during transport

## Resetting Data
To completely reset and repopulate the database:
```bash
php /Applications/XAMPP/xamppfiles/htdocs/dbms-scm/populate_db.php
```

This will:
- Clear all existing data (respecting foreign key constraints)
- Reload all test data from scratch
- Maintain database integrity

## Notes
- All passwords are **password123** (for test environment only)
- The database automatically disables/enables foreign key checks during population
- IDs are auto-generated; check dashboard for actual assigned IDs
- Timestamps are set to current date/time during population
