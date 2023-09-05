<?php

include_once("setup.php");

// Welcome message - I was watching a pirate flick tonight, so this works
if ($input == "::welcome::") exit("\n\n\n\nAvast, ye scallywag!\n\nI was listening to a fun pirate movie when I coded this goofy demo.\nBut if ye want to come aboard, you'll have to give me the password.\n");

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
		printf("Welcome aboard, matey!\n\n");
		printf("So here's what ye can do here:\n");
		printf("    To see some big text, type shout {your message}\n");
		printf("    To see a list of files below dec, type files\n");
		printf("    If yer a landlover, ye can run cowsay here.  No quotes.  Capn's orders.\n");
		printf("    To play a little game, type game\n\n");
		printf("    To abandon ship, type exit\n\n");
		exit();
	}
	else exit("\nTry again, ya sea dog!\t");
}

// If the user wants to play guess-a-number, start the game.
if ($input == "game") {
	$data["game"] = 1;
	file_put_contents("$path/$token.json", json_encode($data));
	exit("This be a game as old as Davy Jones, and as stupid as Captain Hook would be if he came to Florida... guess a number. :-)\nIf ya get tired of this, type done\n\n");
}

// If the user is playing guess-a-number already, handle that here
if (array_key_exists('game', $data)) {
	if (strtolower($input) == "done") {
		unset($data["game"]);
		file_put_contents("$path/$token.json", json_encode($data));
		exit("Fine!  But the treasure be mine...\nOh well, what do ya want to do next?");
	}
	if (!is_numeric($input)) exit("That's not a number.  now swab the deck!");
	$number = intval($input);
	if ($number < 7) exit("Higher");
	if ($number > 7) exit("Lower");
	unset($data["game"]);
	file_put_contents("$path/$token.json", json_encode($data));
	exit("Ya guessed it!  Shiver me timbers! :-)");
}

// If the user sends "files", list files
if (preg_match("~^files\$~", $input)) {
	exit(shell_exec("ls -la"));
}

// If the user wants "cowsay", do that
if (preg_match("~^cowsay ~", $input)) {
	$input = str_replace("cowsay ", "", $input);
	$args = escapeshellarg($input);
	exit(shell_exec("/usr/games/cowsay $args"));
}

// If the user wants to see big text, do that
if (preg_match("~^shout ~", $input)) {
	$input = str_replace("shout ", "", $input);
	$args = escapeshellarg($input);
	exit(shell_exec("/usr/bin/toilet $args"));
}

// Otherwise...
exit("Ya scurvy landlover!  Ye can't do that on this ship! :-)");
