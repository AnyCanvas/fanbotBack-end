<?php
require('vendor/autoload.php');

$body = @file_get_contents('php://input');
$data = json_decode($body,true);
use WebSocket\Client;
$data['text'] = $data['data'];
$msg = json_encode($data);

$client = new Client("ws://104.236.71.12:8080");
$client->send($msg);

// echo $client->receive(); // Will output 'Hello WebSocket.org!'
?>