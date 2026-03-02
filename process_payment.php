<?php
session_start();
include('ConnectDB.php');

$data = json_decode(file_get_contents('php://input'), true);
$ids = $data['cart_ids'] ?? [];

if (!empty($ids)) {
    $id_list = implode(',', array_map('intval', $ids));
    $sql = "DELETE FROM cart WHERE id IN ($id_list) AND user_id = 1";
    
    if ($conn->query($sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}
?>