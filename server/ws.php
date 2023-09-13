<?php

/**
 * Creates a server socket for communicating over WebSockets
 * @param {string} The address (usually "localhost")
 * @param {unsigned int} $port The port number
 * @returns {Socket|NULL) The server socket, or NULL if something fails
 */
function WS_CreateServer($address, $port) {
	try {
		$server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($server === false) throw new Exception(socket_last_error());
		socket_set_option($server, SOL_SOCKET, SO_REUSEADDR, 1);
		socket_bind($server, $address, $port);
		socket_listen($server);
		return $server;
	}
	catch(Exception $e) {
		error_log("WS_CreateServer: " . $e->getMessage() . PHP_EOL
			. $e->getTraceAsString());
		return NULL;
	}
}

/**
 * Creates a client socket, sending the required HTTP "handshake" headers
 * @param {Socket} $server A server socket created by WS_CreateServer
 * @returns {Socket|NULL) The server socket, or NULL if something fails
 */
function WS_CreateClient($server) {
	try {
		$client = socket_accept($server);
		if ($client === false) throw new Exception(socket_last_error());
		$request = socket_read($client, 5000);
		preg_match('#Sec-WebSocket-Key: (.*)\r\n#', $request, $matches);
		$key = base64_encode(pack('H*', sha1($matches[1]
			. '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
		$headers = "HTTP/1.1 101 Switching Protocols\r\n";
		$headers .= "Upgrade: websocket\r\n";
		$headers .= "Connection: Upgrade\r\n";
		$headers .= "Sec-WebSocket-Version: 13\r\n";
		$headers .= "Sec-WebSocket-Accept: $key\r\n\r\n";
		$sent = socket_write($client, $headers, strlen($headers));
		if ($sent === false) throw new Exception(socket_last_error());
		return $client;
	}
	catch(Exception $e) {
		error_log("WS_CreateClient: " . $e->getMessage() . PHP_EOL
			. $e->getTraceAsString());
		return NULL;
	}
}


// End of WebSockets micro-library
