<?php

include_once("./ws.php");

// Let the script hang around waiting for connections
set_time_limit(0);

// Create the server socket
$server = WS_CreateServer("localhost", 12345);
if (is_null($server)) exit("Error creating server");

// Create the client socket
$client = WS_CreateClient($server);
if (is_null($client)) exit("Error creating client");

/*
// Experiment: Run ed and relay info between ed and the browser
// This was also AI-generated, and didn't work, but I'm saving it cuz I
// plan to revisit it tomorrow nite :-)
$descriptorspec = array(
   array("pipe", "r"),  // stdin
   array("pipe", "w"),  // stdout
   array("pipe", "w")   // stderr
);
$process = proc_open("/usr/games/gnuchess", $descriptorspec, $pipes);
*/

// Send messages into WebSocket in a loop.
while (true) {
	$input = "";
    $input = socket_read($client, 1024);
	$input = WS_Translate($input);
	
	WS_Write($client, "Received: " . $input);
	
	/*
	// Send $input to gnuchess
	fwrite($pipes[0], $input);

	// Read output from gnuchess and send it to the client
	WS_Write($client, stream_get_contents($pipes[1]));
	*/
	
	/*
	// Old code (worked great)
    sleep(1);
	WS_Write($client, "\033c\033[34mTime: \033[33m" . date("H:i:s"));
	*/
}

// End of test server program
