<?php

include_once("setup.php");

// Welcome message - I was watching a pirate flick tonight, so this works
if ($input == "::welcome::") exit("\r\n\r\nAvast, ye scallywag!\nWho dares to com aboard me ship? :-)\n");

// Exit message
if ($input == "::end-session::") {
	unlink("$path/$token.json");
	exit("alright then, walk the plank!\n\n");
}

// Obviously this is just for kicks, no SSL or anything (YET), but how
// about a little username check at least?  More proof of concpet stuff.
if (!array_key_exists('user', $data)) {
	if ($input == "geek") {
		$data['user'] = "geek";
		file_put_contents("$path/$token.json", json_encode($data));
		exit("Welcome aboard, matey!\n");
	}
	else exit("Arrrrrg... Password?\t");
}

// Now we got our command, what do we do with it?  How about something
// really dumb, something I would never do in serious code! Woooo! :D
if (preg_match("~^cowsay \".{1,}\"\\\$~", $input) === 0) {
	exit(shell_exec("/usr/games/$input"));
}
exit("Ya scurvy scoundrel!  Ye can't do that!\n");

