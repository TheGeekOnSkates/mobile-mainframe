<?php

// Path to where session JSON files are stored
// It should be noted that when this is not running in localhost, it
// would be best to put this outside the publicly-accessible web root. :D
// Just stayin'.  But for now, not an issue.
$path = ".";

// Get the user's token.  If one wasn't passed, create a new file to
// store stuff, and return the name of that file.
//
// Random thought for version 2.0: Once the terminal can do cookie-based
// sessions (which I've heard libwebsockets can do), use $_SESSION instead
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

