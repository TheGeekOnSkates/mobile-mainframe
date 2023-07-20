<?php

// It should be noted that when this is not running in localhost, it
// would be best to put this outside the publicly-accessible web root. :D
// Just stayin'.  But for now, not an issue.
$path = ".";

// Get the user's token.  If one wasn't passed, create a new file to
// store stuff, and return the name of that file.
$token = $_POST['t'];
if (!isset($token)) {
	$token = date("YmdHis");
	file_put_contents("$path/$token.json", "{}");
	exit($token);
}

// Make sure there is a file matching the token; note that down the
// road, I'll have a background job deleting old token files.  Buf for now,
// I don't really care, so just check if it exists and save it to memory.
if (!file_exists("$path/$token.json")) exit("Your token has expired.\r\n");
$data = json_decode(file_get_contents("$path/$token.json"), true);

// Get the user's input
$input = $_POST['i'];
if (!isset($input)) exit("Missing input.\n");

// Welcome message
if ($input == "::welcome::") exit("\r\n\r\n2 days to Friday! :D\r\nUsername:\t");

// Exit message
if ($input == "::end-session::") {
	unlink("$path/$token.json");
	exit("Bye.\r\n");
}

// Obviously this is just for kicks, no SSL or anything (YET), but how
// about a little username check at least?  More proof of concpet stuff.
if (!array_key_exists('user', $data)) {
	if ($input == "geek") {
		$data['user'] = "geek";
		file_put_contents("$path/$token.json", json_encode($data));
		exit("Okay, you're in!\n");
	}
	else exit("Username:\t");
}

// Now we got our command, what do we do with it?  How about something
// really dumb, something I would never do in serious code! Woooo! :D
echo shell_exec($input);

