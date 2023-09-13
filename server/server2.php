<?php

/*
YES!!!!
https://medium.com/@cn007b/super-simple-php-websocket-example-ea2cd5893575

Okay, now that this actually works, what's next:

1. Get the client to connect over WebSockets.

// Code from the tutorial, with a minor mod on my part:
var host = 'ws://0.0.0.0:12345/server2.php';
var socket = new WebSocket(host);
socket.onmessage = function(e) {
    console.log(e.data);
    term.write(e.data + "\r\n");
};

2. Mod the server so that if a client disconnects, instead of showing an
error, log it and quit trying to talk to that client.

3. Then the fun really starts! :-)
	- Create a better interactive app in PHP; maybe a chat platform
	- Try to get PHP playing middle-man for a Linux app, such as...
		* Darkest Hour
		* Vim (if not, maybe just ed)
		* Telnet (???!!???)
	- If (Lord willing, WHEN) it can do that... start on the C client.
*/
$address = '0.0.0.0';
$port = 12345;

// Create WebSocket.
$server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($server, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($server, $address, $port);
socket_listen($server);
$client = socket_accept($server);

// Send WebSocket handshake headers.
$request = socket_read($client, 5000);
preg_match('#Sec-WebSocket-Key: (.*)\r\n#', $request, $matches);
$key = base64_encode(pack(
    'H*',
    sha1($matches[1] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')
));
$headers = "HTTP/1.1 101 Switching Protocols\r\n";
$headers .= "Upgrade: websocket\r\n";
$headers .= "Connection: Upgrade\r\n";
$headers .= "Sec-WebSocket-Version: 13\r\n";

