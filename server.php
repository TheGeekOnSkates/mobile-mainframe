<?php

// ------------------------------------------------------------------------
// SETTINGS (This is what you should edit when setting up a server)
// ------------------------------------------------------------------------

$hostname = "localhost";					// The server name/IP address
$port = 12345;								// The port number
$command = "telnet alteraeon.com 3000";		// The command to run



// ------------------------------------------------------------------------
// SERVER CODE STARTS HERE (End of server settings)
// ------------------------------------------------------------------------

// Let the script hang around waiting for connections
set_time_limit(0);

// Create the server socket
$server = WS_CreateServer($hostname, $port);
if (is_null($server)) exit("Error creating server");

// Create an array to store client sockets for socket_select purposes
$clients = array($server);

// And this array will store the client sockets AND associated processes
$users = array();

// Set up a custom error handler
function on_error($as_number, $as_string, $file, $line) {
	// Apparently, error #32 (broken pipe) happens "because the *SERVER*
	// socket has closed and is no longer listening..." but a disconnected
	// client is still trying to send it data.  Not sure how accurate that
	// is, but if that's the case, then we can safely ignore that error.
	// Otherwise, it gets repeated in an infinite loop, and we don't need to
	// overload error_log or any other file with all that garbage
	// See https://stackoverflow.com/questions/17678146/how-to-handle-socket-broken-pipe-error-32-in-php
	// Another site says "The socket listener is closing the connection", so
	// it seems that is the case (server stopped listening)...
	// https://www.appsloveworld.com/php/72/writing-to-a-socket-and-handling-broken-pipes
	if (strpos($as_string, "Broken pipe") !== false) return;
	echo $file . ", line " . $line . ": " . $as_string . PHP_EOL;
}
set_error_handler("on_error");

while (true) {
    // Copy the array of client sockets
    $read = $clients;

    // Check for changes in the socket status
    socket_select($read, $write, $except, 0);
	
    // Handle new client connections
    if (in_array($server, $read)) {
        $newSocket = WS_CreateClient($server);
		if (is_null($newSocket)) {
			error_log("WS_CreateClient failed");	// socket_strerror...
		} else {
			socket_set_nonblock($newSocket);
			$clients[] = $newSocket;
			$index = array_search($server, $read);
			unset($read[$index]);
			
			// Run a command-line app in a separate process; when the client
			// connects, PHP will just be a sort of "middle-man", relaying
			// info between this child process and the client.
			$descriptorspec = array(
			   0 => array("pipe", "r"),  // stdin
			   1 => array("pipe", "w"),  // stdout
			   2 => array("pipe", "w"),  // stderr
			);
			$process = proc_open("stdbuf -o0 $command 2>&1",
				$descriptorspec, $pipes);
			stream_set_blocking($pipes[0], 0);
			stream_set_blocking($pipes[1], 0);
			stream_set_blocking($pipes[2], 0);
			
			// Push the new client and precess into the $users array
			array_push($users, array(
				"socket" => $newSocket, "process" => $process,
				"stdin" => $pipes[0], "stdout" => $pipes[1],
				"stderr" => $pipes[2]
			));
		}
	}
	
	// Handle each client's I/O stuff
	foreach($users as &$user) {
		// If the socket is NULL, skip it
		if (is_null($user["socket"])) continue;
		
		// If the process is no longer running, disconnect the user
		$status = proc_get_status($user["process"]);
		if (!$status["running"]) {
			if (!is_null($user["socket"])) {
				socket_close($user["socket"]);
				$index = array_search($user["socket"], $clients);
				unset($clients[$index]);
				$user["socket"] = NULL;
			}
			continue;
		}
		
		/*
		// If the user has been disconnected, kill the process
		// This sometimes spits errors; this is my next bug battle :-D
		$status = socket_get_status($user["socket"]);
		if ($status["eof"]) {
			// Client disconnected
			if (!is_null($user["socket"])) {
				socket_close($user["socket"]);
				$index = array_search($user["socket"], $clients);
				unset($clients[$index]);
				$user["socket"] = NULL;
			}
			fclose($user["stdin"]);
			fclose($user["stdout"]);
			fclose($user["stderr"]);
			proc_close($user["process"]);
		}
		*/
		
		// Get output from the process, if there is any,
		// and if so send it to the client
		$output = fgetc($user["stdout"]);
		if ($output !== false) WS_Write($user["socket"], $output);
		
		// Read input from the client, and if there is any,
		// send it to the process
		$input = "";
		$input = socket_read($user["socket"], 1024);
        if ($input === false || $input === "") continue;
		$input = WS_Translate($input);
		fputs($user["stdin"], $input);
	}
}



// ------------------------------------------------------------------------
// MY WSBSOCKETS MICRO-LIBRARY
// ------------------------------------------------------------------------

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


// End of server program
