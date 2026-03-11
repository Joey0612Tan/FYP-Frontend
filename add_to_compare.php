<?php
session_start();
include('ConnectDB.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Missing ID"]);
    exit;
}

$id = (int)$_GET['id'];

if (!isset($_SESSION['compare'])) {
    $_SESSION['compare'] = [];
}

if (in_array($id, $_SESSION['compare'])) {
    http_response_code(409); // Conflict
    exit;
}

if (count($_SESSION['compare']) >= 4) {
    http_response_code(403); // Forbidden
    echo "Limit reached";
    exit;
}

$_SESSION['compare'][] = $id;

http_response_code(200);
echo json_encode(["success" => true, "count" => count($_SESSION['compare'])]);
exit;
?>
