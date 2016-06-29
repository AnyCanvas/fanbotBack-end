<?php
use WAMP\WAMPClient;
$client = new WAMP(
			new WAMPClient('http://localhost:8080')
		);
$sessionId = $client->connect();
$client->disconnect();

?>