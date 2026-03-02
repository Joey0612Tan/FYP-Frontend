<?php
session_start();
include('ConnectDB.php');

$user_id = 1;
$cart_id = (int)$_GET['id'];

$conn->query("
    DELETE FROM cart
    WHERE id = $cart_id AND user_id = $user_id
");

header("Location: cart.php");
exit;
