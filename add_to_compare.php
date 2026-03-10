<?php
session_start();
include('ConnectDB.php');

$p_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$u_id = 1;

if ($p_id > 0) {
    $check = $conn->query("SELECT * FROM compare_list WHERE product_id = $p_id AND user_id = $u_id");
    
    if ($check->num_rows > 0) {
        echo json_encode(['status' => 'exists', 'message' => 'Already in list!']);
    } else {
        $conn->query("INSERT INTO compare_list (product_id, user_id) VALUES ($p_id, $u_id)");
        echo json_encode(['status' => 'success', 'message' => 'Added to compare list!']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
}
exit; 
?>
