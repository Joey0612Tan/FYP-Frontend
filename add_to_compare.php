<?php

session_start();

$id = $_GET['id'];

if(!isset($_SESSION['compare'])){
    $_SESSION['compare'] = [];
}

if(in_array($id, $_SESSION['compare'])){

    http_response_code(409);
    exit;

}

$_SESSION['compare'][] = $id;

http_response_code(200);
