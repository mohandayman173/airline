<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "airline";
try {
    $conn= new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $err) {
    echo "Connection failed: " . $err->getMessage();
}
?>