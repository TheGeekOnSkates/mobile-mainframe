# Mobile Mainframe

This fun little project is [explained in better detail here](https://thegeekonskates.github.io/mobile-mainframe/).


## TO-DO's for version 2.0:

1. As I go, keep adding features to the client:
	- Better key input processing for the URL-reading code in `onInput`
	- Maybe a disconnect shortcut/button?
	- Make sure my changes don't break the 1.0 support :-)
2. Get the script to support multiple connections, and lock it down
	- From what I've read, the multiple connection thing is done with `socket_select` (I'm sure the PHP docs will show how it's done)
	- When a user disconnects, have the client ask for an address again
	- If the user closes the process opened with `proc_open`, disconnect him/her.  This way there is not room for doing other stuff... like kinda makes me wonder what is possible if I hit :q in vim...
3. Write that chat app - I can do this in any language now!  Maybe C/ncurses
4. Once it seems the web version is good enough to call done... which in and of itself would be *amazing*... build the terminal client (using C and libwebsockets).
5. Test, polish, WTFM, push to master, call it done-for-tnow, and start on the stuff I wanted to use this for! :-)


