<?php
session_start();
include('ConnectDB.php');

$p_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$u_id = 1; 

if ($p_id > 0) {
    $checkCompare = "SELECT * FROM compare_list WHERE product_id = $p_id AND user_id = $u_id";
    $resCompare = $conn->query($checkCompare);

    if ($resCompare && $resCompare->num_rows > 0) {
        header("Location: HomePage.php?status=already_exist");
    } else {
        $insertCompareSql = "INSERT INTO compare_list (product_id, user_id) VALUES ($p_id, $u_id)";
        if ($conn->query($insertCompareSql)) {
            header("Location: HomePage.php?status=added");
        } else {
            header("Location: HomePage.php?status=error");
        }
    }
} else {
    header("Location: HomePage.php?status=invalid");
}
exit(); 
?>
