<?php
session_start();
include('ConnectDB.php');

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $user_id = 1; 

    $stmt = $conn->prepare("DELETE FROM compare_list WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
}
exit; 
?>
