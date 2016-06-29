<?php
$client = new \src\WAMP\WAMPClient('http://localhost:8080');
$sessionId = $client->connect();
$client->disconnect();

?>