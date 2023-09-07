window.onload = function() {
	var term = new Terminal();
	term.open(terminal);
	term.write('c[34mTO-DO:[0m\r\n');
	term.write("\t1. Get this terminal talking to the 1.0 server\r\n");
	term.write("\t2. Review how to talk to WebSockets from JS\r\n");
	term.write("\t3. Write a WebSockets server program in PHP\r\n");
	term.write("\t4. Get this terminal talking to that program\r\n");
	term.write("\t5. Learn libwebsockets and build the C client\r\n");
	term.write("\t6. Get the C client talking to the PHP program\r\n");
	term.write("\t7. Test, polish, WTFM, repeat. :-)\r\n\n\n");
};
