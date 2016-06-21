<?php
require 'vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use ChatApp\Chat;

$loop   = React\EventLoop\Factory::create();
$webSock = new React\Socket\Server($loop);
$webSock->listen(8080, '0.0.0.0'); // Binding to 0.0.0.0 means remotes can connect

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
	        new WampServer(
            new Chat()
            )
        )
    ),
    $webSock
);

$server->run();    

