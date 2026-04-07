<?php
session_start();
include('ConnectDB.php');

header('Content-Type: application/json');

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    if (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        echo json_encode(['success' => false, 'message' => 'No ID provided']);
    } else {
        header("Location: cart.php?error=1");
    }
    exit;
}

$cart_id = isset($_GET['id']) ? intval($_GET['id']) : intval($_POST['id']);
$user_id = 1;

$stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();

$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($is_ajax || strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
    echo json_encode(['success' => true]);
} else {
    header("Location: cart.php?deleted=1");
}
exit;
?>
