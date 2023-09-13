# Mobile Mainframe

This fun little project is [explained in better detail here](https://thegeekonskates.github.io/mobile-mainframe/).


## TO-DO's for version 2.0:

1. As I go, keep adding features to the client:
	- Better key input processing for the URL-reading code in `onInput`
	- Maybe a disconnect shortcut/button?
	- Make sure my changes don't break the 1.0 support :-)
2. Write a chat program on the server-side:
	- What I have (from the tutorial) works, but only for one client at a time.  Get it to support multiple connections.
	- When one client sends a message, broadcast it to them all
	- Add error-handling; if it can't send to somebody, disconnect.
	- Maybe write a micro-library for this (wait... didn't I have one of those?  I think I misplaced it in client-browser, then deleted it.  Check recent commits...).
3. See if I can get PHP to just relay data between the client and a Linux app (text editor, maybe a game? etc. :-D)
	- Start with something crazy-simple.  Maybe /bin/ed; maybe GNU Chess.
	- Work my way up to vim, bastet, and the Lunduke BBS :-)
4. Once it seems the web version is good enough to call done... which in and of itself would be *amazing*... build the terminal client (using C and libwebsockets).  Maybe a little chat program that browser and terminal clients can get on together.
5. Test, polish, WTFM, and push to master! :-)


