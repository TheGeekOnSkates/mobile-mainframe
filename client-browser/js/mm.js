// -------------------------------------------------------------------------
// GLOBAL VARIABLES
// -------------------------------------------------------------------------

// term: The terminal; url: the URL you're connected to;
// on: 1 if connected, 0 if not
var term, url = "", on = 0;



// -------------------------------------------------------------------------
// FUNCTIONS
// -------------------------------------------------------------------------

function onInput(data) {
	if (on) {
		// Send to a WebSocket
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
		term.write("\r\n\nGoing to " + url + "\r\n\n");
		url = "";
		return;
	}
	
	// Otherwise, add it to the URL :)
	url += data;
	term.write(data);
}

window.onload = function() {
	term = new Terminal();
	term.open(terminal);
	term.onData(onInput);
	term.focus();
};
