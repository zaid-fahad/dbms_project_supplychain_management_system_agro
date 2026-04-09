<?php
include "db.php";

// Dummy data for Quality_Checks
$conn->query("INSERT INTO Quality_Checks (batch_id, officer_id, quality_tag, comments) VALUES (2, 2, 'Approved', 'Good quality') ON DUPLICATE KEY UPDATE quality_tag='Approved'");
$conn->query("INSERT INTO Quality_Checks (batch_id, officer_id, quality_tag, comments) VALUES (4, 2, 'Approved', 'Excellent') ON DUPLICATE KEY UPDATE quality_tag='Approved'");
$conn->query("INSERT INTO Quality_Checks (batch_id, officer_id, quality_tag, comments) VALUES (6, 2, 'Approved', 'High quality') ON DUPLICATE KEY UPDATE quality_tag='Approved'");
// Leave some without quality checks for pending status

echo "Dummy data inserted successfully!";
$conn->close();
?></content>
<parameter name="filePath">/Applications/XAMPP/xamppfiles/htdocs/dbms-scm/populate_db.php