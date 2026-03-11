<?php
session_start();
include('ConnectDB.php');

$p_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$u_id = 1; 

if ($p_id <= 0) {
    http_response_code(400);
    echo "Invalid Product ID";
    exit;
}
$checkCompare = "SELECT * FROM compare_list WHERE product_id = $p_id AND user_id = $u_id";
$resCompare = $conn->query($checkCompare);

if ($resCompare && $resCompare->num_rows > 0) {
    http_response_code(409); 
    exit;
}

$insertCompareSql = "INSERT INTO compare_list (product_id, user_id) VALUES ($p_id, $u_id)";
if ($conn->query($insertCompareSql)) {
    http_response_code(200); 
} else {
    http_response_code(500); 
    echo $conn->error;
}
?>
