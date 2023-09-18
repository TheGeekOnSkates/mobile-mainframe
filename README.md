# Mobile Mainframe

This fun little project is [explained in better detail here](https://thegeekonskates.github.io/mobile-mainframe/).


## TO-DO's for version 2.0:

1. I'm nearly done with the multiple-connection thing.  But it's got bugs:
	- After one client disconnects, my server goes BANANAS with "supplied resource is not a valid Socket resource".  It makes sense that it would do this about trying to read from a closed connection - but unsetting the thing doesn't seem to help (and sometimes the error is triggered by `socket_select`, which makes no sense except to my God and maybe The Inventor Of The Internet). 😆
	- Once that's fixed, the other thing I want to make sure I do is to make sure all child processes are closed and all pipes are closed and all sockets are closed, and... yeah, when it shuts down, it needs to SHUT THE PUCK DOWN. 😆
2. As I go, keep adding features to the client:
	- Better key input processing for the URL-reading code in `onInput`
	- Maybe a disconnect shortcut/button?
	- Make sure my changes don't break the 1.0 support :-)
3. Write that chat app - I can do this in any language now!  Maybe C/ncurses
4. Once it seems the web version is good enough to call done... which in and of itself would be *amazing*... build the terminal client (using C and libwebsockets).
5. Test, polish, WTFM, push to master, call it done-for-tnow, and start on the stuff I wanted to use this for! :-)

