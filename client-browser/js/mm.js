// -------------------------------------------------------------------------
// GLOBAL VARIABLES
// -------------------------------------------------------------------------

// term: The terminal; url: the URL you're connected to;
// line: The user's input when in HTTP mode; token: also for HTTP mode;
// on: 1 if connected, 0 if not
var term, url = "", on = 0, line = "", token = "", ws = 0, socket;



// -------------------------------------------------------------------------
// FUNCTIONS
// -------------------------------------------------------------------------

/**
 * Sends user input to the server, using the 1.0 spec for HTTP(S)
 * @param {string} input The input
 */
async function send(input) {
	try {
		var r = await fetch(url, {
			method: 'POST',
			headers:{'Content-Type':'application/x-www-form-urlencoded'},
			body: "i=" + encodeURIComponent(input) + "&&t=" + token
		});
		r = await r.text();
		term.write(r + "\n");
		if (input == "::end-session::") {
			term.write("Connection closed.\n\n");
			term.write("----------------------------------------------------------------------------\n\n");
			term.write("\x1b[34mEnter address:   \x1b[0m");
		}
	} catch(wtf) {
		term.write("\x1b[31m" + wtf + "\x1b[0m\n");
		on = 0;	// Assuming the connection is closed, for now
	}
}

/**
 * Handles incoming user input
 * @param {string} data The user's input
 */
async function onInput(data) {
	if (on == 2) {
		// Connected in the version 2 setup, using WebSockets
		socket.send(data);
		//term.write(data);
		return;
	}
	
	if (on) {
		// Connected to HTTP, behave like the 1.0 terminal
		if (data == "\x7f") {
			// Backspace
			if (line) return;
			term.write("\x1b[D\x1b[K");
			line = line.substr(0, url.length - 1);
			return;
		}
		
		if (data == "\r") {
			// Enter
			term.write("\n");
			if (line == "exit") {
				// If the line is "exit", close the connection
				send("::end-session::");
				url = line = "";
				on = 0;
				return;
			}
			send(line);
			line = "";
			return;
		}
		
		line += data;
		term.write(data);
		return;
	}
	
	if (data == "\x7f") {
		// Backspace
		if (!url) return;
		term.write("\x1b[D\x1b[K");
		url = url.substr(0, url.length - 1);
		return;
	}
	
	if (data == "\r") {
		// Enter
		if (url.startsWith("ws://") || url.startsWith("wss://")) {
			socket = new WebSocket(url);
			socket.onmessage = function(e) {
				term.write(e.data);
			};
			socket.onclose = function() {
				term.write("Connection closed\r\n\n");
				on = 0;
			};
			line = "";
			on = 2;
		} else {
			var r = await fetch(url);
			token = await r.text();
			send("::welcome::");
			line = "";
			on = 1;
		}
		return;
	}
	
	// Otherwise, add it to the URL :)
	url += data;
	term.write(data);
}

window.onload = function() {
	term = new Terminal({
		rows: 24, cols: 80,
		convertEol: true, screenReaderMode: true
	});
	term.open(terminal);
	term.onData(onInput);
	term.write("\x1b[33mMOBILE MAINFRAME 2.0\x1b[0m\n\n");
	term.write("\x1b[34mEnter address:   \x1b[0m");
	term.focus();
	on = 0;
	url = line = token = "";
};

/*
Looks like the it works like this:
ws = new WebSocket("ws://my-url-here");
ws.on("open", function() {
	console.log("connected");
});
ws.on("message", function(message) {
	// Output message to the terminal
});
ws.send("some message here");


*/
