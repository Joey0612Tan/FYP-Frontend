<?php
session_start();
include('ConnectDB.php');

error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'No ID provided']);
    exit;
}

$cart_id = isset($_GET['id']) ? intval($_GET['id']) : intval($_POST['id']);
$user_id = 1;

$stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $cart_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
exit;
?>
