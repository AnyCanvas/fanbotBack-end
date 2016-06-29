<?php
use WAMP\WAMPClient;
$client = new WAMPClient('http://localhost:8080');

$sessionId = $client->connect();
$client->disconnect();

?>