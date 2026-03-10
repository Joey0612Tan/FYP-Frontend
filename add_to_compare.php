<?php
session_start();
include('ConnectDB.php');

$p_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$u_id = 1; 

if ($p_id > 0) {
    $check = $conn->prepare("SELECT 1 FROM compare_list WHERE product_id = ? AND user_id = ?");
    $check->bind_param("ii", $p_id, $u_id);
    $check->execute();
    $exists = $check->get_result()->num_rows > 0;

    if ($exists) {
        http_response_code(409); 
    } else {
        $insert = $conn->prepare("INSERT INTO compare_list (product_id, user_id) VALUES (?, ?)");
        $insert->bind_param("ii", $p_id, $u_id);
        $insert->execute();
        http_response_code(200); 
    }
}
exit;
?>
