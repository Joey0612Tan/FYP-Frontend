<?php
session_start();
include('ConnectDB.php');

$user_id = 1;
$cart_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($cart_id > 0) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

echo "success";
exit; 
?>
