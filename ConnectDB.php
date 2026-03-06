<?php
$host = "p:mysql-bff3581-fyp-ai-project.d.aivencloud.com";
$port = 20090; 
$user = "avnadmin";
$pass = "AVNS_XKCyPutVMtlvtPhs03c";
$db   = "defaultdb";    

$conn = mysqli_init();
mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 10);

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
    die("Connect Error: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
$db = $conn;
?>
