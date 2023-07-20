# "Mobile Mainframe"

## Next steps

This guy is officially at the proof-of-concept stage!  Yay! :D

Seriously though, there are a few things I'd like to do at this point:

1. I kinda want to nail down the "spec", the details of how clients will connect to the server.  Version 1.0 is obviously gonna be crazy simple.  Client sends request, server sends response.  The question is how to track state info.  Like okay, for one-off commands it's easy.  There is no interactivity.  But let's say I wanted something like /bin/ed, or one of the command-line apps I've built for myself.  Those ask me for info in a more back-and-forth interactive way.  If the server is doing all the processing, it's gotta have some kind of way of saving that info.
2. Once that's done, I kinda want to add one more feature to my Python-based client: a way to open different URLs (so it's not "hard-coded" like I have it now).
3. I'd like to create a browser-based terminal, simpler than xterm.js.  Version 1.0 of this project is gonna be much simpler.  Less like xterm, more like the old DEC dumb terminals.  No cursor positioning, no color, simple.

Once I have these things in place, *then* I can do fancy-ANSI stuff and all that jazz... but that's a long way off. :)
