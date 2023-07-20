<?php

// I'm going with POST rather than sessions for 3 reasons:
//
//		1. For some reason, Python doesn't seem to care to save them.
//			I've followed tutorials, scoured obscure SO posts, etc. and no
//			dice.  Nothing seems to work, because {reasons}.  Don't care.
//			If this gets bigger than it was ever meant to be, version 2.0
//			can use cookies.  If we even need that.
//
//		2. When I go to build the JS terminal, POST makes more sense since
//			I can send it from my code (no such animal with sessions).  This
//			is especially important if the terminals will support multiple
//			websites - sessions are fine, but CORS can be a witch. :D
//
//		3. In a way, a POST "token" is more flexible than sessions; I can
//			refresh them, expire them, etc. in ways that are a bit harder
//			in PHP (session_unset, session_destory, etc. etc. etc.).
//			So if this DID get big enough to use for more than just the
//			funzos, it's almost a little more... idk, I just know it's not
//			necessarily wrong.  Just different.  Like this project. :D
if (!isset($_POST['t'])) exit(date("YmdHis"));

// Handle user error
// this one doesn't tak 300 lines of comments to explain :D
if (!isset($_POST['c'])) exit("Missing command.\n");

// Now we got our command, what do we do with it?  How about something really dumb, something I would never do in serious code? :D
$command = $_POST['c'];

echo shell_exec($command);
