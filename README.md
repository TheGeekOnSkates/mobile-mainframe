# "Mobile Mainframe"

# url = "http://localhost/mobile-mainframe/mm.php"

## Next steps

### To finish version 1.0

* Of course, if I get it working with xterm.js, that opens up more options in terms of color, but then we get into ANSI graphics and all that jazz... just don't think I'm ready for that yet.
* I kinda want to flesh out the server-side of this a bit more.  Like most of what I have here should really be in like a setup.php, so app-specific functionality can go in the page actually being requested.
* Then I'd like to create a server program that's not a full-on shell.  If I want a live demo, maybe a program that asks for your name and then runs it thru cowsay or something funny like that. :)

### Ideas for version 2.0

* I found out there's a "libwebsockets" - C API for WebSockets!  This means Mobile Mainframe 2.0 can support WebSockets in both the terminal AND the browser.  That would be awesome! :-)
* And if I'm gonna do that, may as well get the xterm.js version working.  I mean come on, what's a mobile terminal without ANSI escape codes? :-)
* I also recently scored a free Python (Bottle) web hosting account, so I'd kinda like to see if I can build a Mobile Mainframe server for Bottle too. :)

