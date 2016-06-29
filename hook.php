<?php
$client = new \WAMP\WAMPClient('http://localhost:8080');
$sessionId = $client->connect();
$client->disconnect();

?>