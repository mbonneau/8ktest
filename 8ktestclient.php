<?php

use React\Socket\ConnectionInterface;
use React\Socket\Connector;

require_once __DIR__ . "/vendor/autoload.php";

$loop   = \React\EventLoop\Factory::create();

$connector = new Connector($loop);

$connector->connect('tcp://127.0.0.1:9005')->then(function (ConnectionInterface $connection) {
    $buffer = '';
    $connection->on('data', function ($data) use ($connection, &$buffer) {
        $buffer .= $data;
        if (substr($buffer, -1) === ' ') {
            $connection->write($buffer);
            $buffer = '';
        }
    });
});

$loop->run();
