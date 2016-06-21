<?php
require 'vendor/autoload.php';
use Ratchet\Server\IoServer;
use MyApp\Chat;

    $server = IoServer::factory(
        new Chat(),
        8080
    );

    $server->run();