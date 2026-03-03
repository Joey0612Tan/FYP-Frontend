<?php
$host = "mysql-bff3581-fyp-ai-project.d.aivencloud.com";
$port = "20090"; 
$user = "avnadmin";
$pass = "AVNS_XKCyPutVMtlvtPhs03c";
$db   = "defaultdb";    

$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

$success = mysqli_real_connect($conn, $host, $user, $pass, $db, $port);

if (!$success) {
    die("Connect Error: " . mysqli_connect_error());
}

$db = $conn;
?>
