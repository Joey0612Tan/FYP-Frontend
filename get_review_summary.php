<?php
include('ConnectDB.php');
if(!isset($_GET['id'])) exit;
$product_id = intval($_GET['id']); 

$query = "SELECT comment FROM reviews WHERE product_id = $product_id"; 
$result = mysqli_query($conn, $query);

$all_reviews = "";
while($row = mysqli_fetch_assoc($result)) {
    $all_reviews .= $row['comment'] . " | ";
}

if (empty($all_reviews)) {
    echo "No review exists yet.";
} else {
    $url = 'https://fyp-ai-backend.onrender.com/summarize_reviews';
    $data = array('reviews' => $all_reviews);

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ),
    );
    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
    $resData = json_decode($response, true);
    echo $resData['summary'];
}

?>
