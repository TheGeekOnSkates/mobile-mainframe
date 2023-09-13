<?php

include_once("./ws.php");

// Create the server socket
$server = WS_CreateServer("localhost", 12345);
if (is_null($server)) exit("Error creating server");

// Create the client socket
$client = WS_CreateClient($server);
if (is_null($client)) exit("Error creating client");

// Send messages into WebSocket in a loop.
while (true) {
    sleep(1);
    $content = "\033c\033[34mNow: \033[0m" . time();
    $response = chr(129) . chr(strlen($content)) . $content;
    socket_write($client, $response);
}

// End of test server program
