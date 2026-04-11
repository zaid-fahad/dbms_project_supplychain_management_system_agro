<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbms_scms";
$port = 3310;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>