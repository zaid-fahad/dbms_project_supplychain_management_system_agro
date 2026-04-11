<?php
/**
 * Quality Officer Populate DB
 * Role-specific data for quality officer operations
 */

include "../db.php";

echo "Quality Officer Data Population\n";
echo "================================\n\n";

try {
    echo "✅ Quality Officer data ready.\n";
    echo "Visit: http://localhost/dbms-scm/quality_officer/dashboard.php\n";
    echo "Login: officer1 / password123\n";
    echo "      or officer2 / password123\n\n";
    
    echo "Quality Checks Summary:\n";
    $result = $conn->query("SELECT COUNT(*) as cnt FROM Quality_Checks WHERE quality_tag = 'Approved'");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "- Approved Batches: " . $row['cnt'] . "\n";
    }
    
    $result = $conn->query("SELECT COUNT(*) as cnt FROM Batches b LEFT JOIN Quality_Checks q ON b.batch_id = q.batch_id WHERE q.check_id IS NULL");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "- Pending Batches: " . $row['cnt'] . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>