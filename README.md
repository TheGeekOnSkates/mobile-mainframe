# "Mobile Mainframe"

## Basic idea

* A client written in C with curl that sends data to/from the server
* A client writtten in JS that uses xterm.js
* A server written in PHP that receives incoming requests and serves the response as ANSI rather than HTML

## Example and some brainstorming

In PHP I can write a script with just this:

`<?php printf("\x1bc\x1b[1;34mAwesome!\x1b[0m");`

And when I run it, it clears the screen and prints "Awesome!" in blue text.
Now consider a web-based login system: You enter a username and password, and those get processed on the server.  But PHP doesn't know whether it was sent by a pretty-but-bloated web thing or a terminal.  There are headers it could use, but all those can be spoofed.  All it knows is what was sent to it (session cookies, `$_POST` etc.) and how it was programmed to respond.

So the main difference, really, is the client.  Now one drawback to a text-only client is that it doesn't know what data the server expects.  so here's what I'm thinking:

* The client, by itself, has only 2 "commands": open and exit.  "open" requests content from a server; exit exits the program.
* The server will respond with everything the client needs to keep the conversation going.

It might be easier to alk through how I see this working rather than explaining it like I've been doing.  So here we go:

1. I run my client and type "open http://geekonskates.com/mobile-mainframe"
2. The server sends me this data: "user,12,2,20:pass,12,3,20;output"

	In the first part, what we get is the names, coordinates, and lengths
	of the form fields; so the space for the username is at 12, 2 and is up
	to 20 characters long.  Password is the same thing, one line down.  The
	client would understand this info and know how to process user input so
	that it sends what the server wants.  After that, where I put "output",
	instead of the word "output" it would be the text to display (including
	ANSI escape codes).  IN this example, line 1 might be the name of the
	system, line 2 would be "Username:" in blue text and line 3 would be
	"Password:" in blue.

3. The client displays the output, then moves the cursor to 12, 2 (username)
4. User enters his/her username, then presses Tab
5. The client moves the cursor to 12, 3 (next to the word "Password").
6. User enters his/her password, then presses Enter
7. Client sends the user's input to the server
8. Server validates the input and sends back a token to be used like a session cookie (unless curl supports sending cookies - then I can just use sessions).  So let's say the variable/session cookie/whatever is "abc123"; the response might be: "abc123;choice,8,20,2;output"
	
	So in this case it sent the token, expects a "choice" with max 2 chars,
	 at position 8, 20.  The output would probably be a menu listing the
	different options (like pages) the server can do

9. User chooses from the list and the process continues.

A few thoughtss about this strategy:

* All validation is done on the server-side.
* The client is pretty "dumb"; it knows what the server wants, how to display server output, and how to process user input.
* Compare this to just using HTML4 and Lynx... maybe a simpler (and equally light) solution would be to just code server-driven web forms.  They work in a terminal, but they also work on an iThing... might be the easy way out. :)
