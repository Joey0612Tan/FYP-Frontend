<?php
session_start();
include('ConnectDB.php');

$user_id = 1;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['qty'])) {
    foreach ($_POST['qty'] as $cart_id => $qty) {
        $cart_id = intval($cart_id);
        $qty = intval($qty);

        if ($qty > 0) {
            $sql = "UPDATE cart SET quantity = $qty WHERE id = $cart_id AND user_id = $user_id";
            $conn->query($sql);
        }
    }
    echo "success";
    exit;
}
?>