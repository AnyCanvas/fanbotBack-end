<?php
require('vendor/autoload.php');

use WebSocket\Client;

$client = new Client("ws://104.236.71.12/");
$client->send("Hello WebSocket.org!");

echo $client->receive(); // Will output 'Hello WebSocket.org!'
?>