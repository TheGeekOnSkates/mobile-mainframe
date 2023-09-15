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

socket_set_nonblock($client);


// Still experimenting with passing data to/from command-line apps
$descriptorspec = array(
   0 => array("pipe", "r"),  // stdin
   1 => array("pipe", "w"),  // stdout
   2 => array("pipe", "w"),  // stderr
);
$cwd = '/var/www/html/mm';
$process = proc_open('stdbuf -o0 /usr/bin/vim 2>&1', $descriptorspec, $pipes, $cwd);

stream_set_blocking($pipes[0], 0);
stream_set_blocking($pipes[1], 0);
stream_set_blocking($pipes[2], 0);

// Send messages into WebSocket in a loop.
while (true) {
	$output = fgetc($pipes[1]);
	if ($output !== false) WS_Write($client, $output);
	
	// Is this not sending to $pipes[0]?
	$input = "";
    $input = socket_read($client, 1024);
	if ($input !== false) {
		$input = WS_Translate($input);
		fputs($pipes[0], $input);
	}
	
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
