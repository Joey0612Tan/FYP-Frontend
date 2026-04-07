<?php
session_start();
include('ConnectDB.php');

error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['updates']) || empty($data['updates'])) {
    echo json_encode(['success' => false, 'message' => 'No updates provided']);
    exit;
}

$user_id = 1;
$success = true;

foreach ($data['updates'] as $update) {
    $cart_id = intval($update['id']);
    $quantity = intval($update['quantity']);
    
    if ($quantity < 1) $quantity = 1;
    
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
    
    if (!$stmt->execute()) {
        $success = false;
    }
}

echo json_encode(['success' => $success]);
exit;
?>
