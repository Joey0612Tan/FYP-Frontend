<?php
$host = "mysql-bff3581-fyp-ai-project.d.aivencloud.com";
$port = 20090;
$user = "avnadmin";
$pass = "AVNS_XKCyPutVMtlvtPhs03c";
$db   = "defaultdb";    

$conn = mysqli_init();

if (!$conn) {
    die("mysqli_init failed");
}

$success = mysqli_real_connect(
    $conn, 
    $host, 
    $user, 
    $pass, 
    $db, 
    $port, 
    NULL, 
    MYSQLI_CLIENT_SSL 
);

if (!$success) {
    die("Connect Error (" . mysqli_connect_errno() . "): " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

$db = $conn;
?>
