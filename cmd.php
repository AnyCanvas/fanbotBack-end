<?php
use Ratchet\Server\IoServer;
use ChatApp\Chat;

    require dirname(__DIR__) . '/html/vendor/autoload.php';

    $server = IoServer::factory(
        new Chat(),
        8080
    );

    $server->run();