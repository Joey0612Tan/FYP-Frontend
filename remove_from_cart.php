<?php
session_start();
include('ConnectDB.php');

if (isset($_GET['id'])) {
    $cart_id = intval($_GET['id']);  
    $user_id = 1; 

    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
}

header("Location: cart.php");
exit;
?>
