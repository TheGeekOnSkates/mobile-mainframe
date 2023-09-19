<?php

// ------------------------------------------------------------------------
// SETTINGS
// ------------------------------------------------------------------------

$hostname = "localhost";		// Pretty sure this is always localhost
$port = 12345;					// The port number
$command = "vim";				// The command to run



// ------------------------------------------------------------------------
// SERVER CODE STARTS HERE
// ------------------------------------------------------------------------


include_once("./ws.php");

// Let the script hang around waiting for connections
set_time_limit(0);

// Create the server socket
$server = WS_CreateServer($hostname, $port);
if (is_null($server)) exit("Error creating server");

// Create an array to store client sockets for socket_select purposes
$clients = array($server);

// And this array will store the client sockets AND associated processes
$users = array();

function on_error($as_number, $as_string, $file, $line) {
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

// End of server program
