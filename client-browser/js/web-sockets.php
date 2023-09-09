<?php

/**
 * Creates the first socket, with the necessary settings
 * @param {string} $address An IP address
 * @param {number} $port A port number
 * @returns {Socket} A Socket object "bound" to the address/port.
 * This object uses TCP (for more info ask the Duck about those
 * constants - SOCK_STREAM, SO_REUSEADDR etc.  These come from C,
 * )but the PHP wrappers are well-documented on php.net).
 */
function ws_create($address, $port) {
	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
	socket_bind($socket, $address, $port);
	socket_listen($socket);
	return $socket;
}

/**
 * Creates a client socket, and sends WebSocket handshake headers
 * @param {Socket} $socket An object returned by ws_create() above
 * @returns {Socket} Another Socket object; this is the one that
 * you'll be reading from and writing to most of the time.
 */
function ws_create_client($socket) {
	$client = socket_accept($socket);
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
	$headers .= "Sec-WebSocket-Accept: $key\r\n\r\n";
	socket_write($client, $headers, strlen($headers));	
	return $client;
}

/**
 * Writes data to a web socket
 * @param {Socket} $client An object returned by ws_create_client() above
 * @param {string} $data The data to send using that socket
 * @remarks The WebSockets protocol uses an unusual format for sending and
 * receiving data; this just converts $data into that format before writing.
 */
function ws_write($client, $data) {
	$response = chr(129) . chr(strlen($data)) . $data;
	socket_write($client, $response);
}

/**
 * Reads data from a web socket
 * @param {Socket} $client An object returned by ws_create_client()
 * @param {number} $max The max number of bytes to read
 * @param {number} $flags The flags to pass to socket_recv (Duck it)
 * @returns {string|bool} The data, or false if there was no data
 * or if there was an error (use socket_strerror(socket_last_error()) to
 * debug if it returns false and you think there was an error)
 */
function ws_read($client, $max = 500, $flags = MSG_DONTWAIT) {
	$socketData = "The message will go here";
	$result = socket_recv($client, $socketData, $max, $flags);
	if (!$result) return false;
	
	$length = ord($socketData[1]) & 127;
	if ($length == 126) {
		$masks = substr($socketData, 4, 4);
		$data = substr($socketData, 8);
	}
	else if ($length == 127) {
		$masks = substr($socketData, 10, 4);
		$data = substr($socketData, 14);
	}
	else {
		$masks = substr($socketData, 2, 4);
		$data = substr($socketData, 6);
	}
	$socketData = "";
	for ($i = 0; $i < strlen($data); ++$i) {
		$socketData .= $data[$i] ^ $masks[$i%4];
	}
	return $socketData;
}

// End of WebSockets micro-library

