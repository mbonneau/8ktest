<?php

use React\Socket\ConnectionInterface;
use React\Socket\Connector;

require_once __DIR__ . "/vendor/autoload.php";

$loop   = \React\EventLoop\Factory::create();

$socket = new \React\Socket\Server('127.0.0.1:9005', $loop);

$socket->on('connection', function (ConnectionInterface $connection) {
    // we will send "messages" and wait for them to be echoed back from the client
    $msg = str_repeat('*', 8192); // try 8192 and 8193 for message sizes

    echo "Connection opened...\n";

    $buffer = '';
    $msgCount = 0;
    $start = microtime(true);
    $connection->on('data', function ($data) use (&$msg, &$buffer, $connection, &$msgCount, $start) {
        $buffer .= $data;

        if ($buffer === $msg) {
            $buffer = '';
            $msgCount++;
            if ($msgCount === 100) {
                echo 'With message size ' . strlen($msg) . ', got ' . $msgCount . ' responses in ' . (microtime(true) - $start) . ' seconds.' . PHP_EOL;

                if (strlen($msg) > 8192) {
                    $connection->close();
                    return;
                }
                // one more character
                $msg .= '*';
                // reset counter
                $msgCount = 0;
            }
            $connection->write($msg);
        }
    });

    $connection->write($msg);

    $connection->on('close', function () {
        echo "Connection closed...\n";
    });
});

$loop->run();