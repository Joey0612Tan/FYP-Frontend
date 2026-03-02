<?php
session_start();
include('ConnectDB.php');

$p_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$color = isset($_GET['color']) ? $_GET['color'] : 'Standard';
$qty = isset($_GET['qty']) ? intval($_GET['qty']) : 1;
$user_id = 1; 

if ($p_id > 0) {
    $check = "SELECT * FROM cart WHERE product_id = $p_id AND user_id = $user_id AND selected_color = '$color'";
    $res = $conn->query($check);

    if ($res && $res->num_rows > 0) {
        $sql = "UPDATE cart SET quantity = quantity + $qty WHERE product_id = $p_id AND user_id = $user_id AND selected_color = '$color'";
    } else {
        $sql = "INSERT INTO cart (product_id, quantity, user_id, selected_color) VALUES ($p_id, $qty, $user_id, '$color')";
    }

    if ($conn->query($sql)) {
        echo "<script>alert('Successfully added to bag!'); window.location.href='cart.php';</script>";
    } else {
        die("Database Error: " . $conn->error); 
    }
} else {
    echo "Invalid Product ID";
}
?>