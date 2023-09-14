<?php

/**
 * Creates a server socket for communicating over WebSockets
 * @param {string} The address (usually "localhost")
 * @param {unsigned int} $port The port number
 * @param {bool} [$nonBlocking] If true, the socket will be non-blocking
 * @returns {Socket|NULL) The server socket, or NULL if something fails
 */
function WS_CreateServer($address, $port, $nonBlocking = false) {
	try {
		$server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($server === false) throw new Exception(socket_last_error());
		socket_set_option($server, SOL_SOCKET, SO_REUSEADDR, 1);
		socket_bind($server, $address, $port);
		socket_listen($server);
		if ($nonBlocking && !socket_set_nonblock($server))
			throw new Exception(socket_last_error());
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

function WS_Write($client, $data) {
	$response = chr(129) . chr(strlen($data)) . $data;
	socket_write($client, $response);
}

/**
 * Decodes/parses data we get from the client
 * @param {string} $message The message to be "translated"
 * @returns {string} The decoded/parsed message
 * @remarks Most of this came from AI, though my version is slightly edited
 * so it actually works (lol).  But AI gets its input from humans, so credit
 * where credit is due:
 * https://www.perplexity.ai/search/7a4bacc8-1f21-4c08-8502-4b5b67e85ed8?s=u
 */
function WS_Translate($message) {
	try {
		$payload = '';
		$payload_length = 0;
		$payload_offset = 0;
		$fin = (ord($message[0]) & 0x80) == 0x80;
		$opcode = ord($message[0]) & 0x0f;
		$mask = (ord($message[1]) & 0x80) == 0x80;
		$payload_length = ord($message[1]) & 0x7f;
		if ($payload_length == 126) {
			$payload_length = unpack('n', substr($message, 2, 2))[1];
			$payload_offset = 4;
		} elseif ($payload_length == 127) {
			$payload_length = unpack('J', substr($message, 2, 8))[1];
			$payload_offset = 10;
		} else $payload_offset = 2;
		if ($mask) {
			$masking_key = substr($message, $payload_offset, 4);
			$payload_offset += 4;
		}
		$payload = substr($message, $payload_offset);
		if ($mask) {
			for ($i = 0; $i < strlen($payload); $i++) {
				$payload[$i] = chr(ord($payload[$i]) ^ ord($masking_key[$i % 4]));
			}
		}
		/*
		return array(
			'fin' => $fin,
			'opcode' => $opcode,
			'payload' => $payload,
		);
		*/
		return $payload;
	}
	catch(Exception $e) {
		error_log("WS_Translate: " . $e->getMessage() . PHP_EOL
			. $e->getTraceAsString());
		return NULL;
	}
}


// End of WebSockets micro-library
