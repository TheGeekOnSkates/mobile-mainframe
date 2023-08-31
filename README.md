# "Mobile Mainframe"

## Next steps

First, I found a minor bug: The web client is sending a message along with `::welcome::`; that's why my welcome message and password bouncer message show at the same time.  To fix this:
	- On the JS side, if it's sending `::welcome``, set it up to not send anything else.
	- On the server side, include a password request in the welcome message
	- Make sure it works the same in both clients

After that... I'm at a place where I'm cool with calling Mobile Mainframe 1.0 complete!  It works great in both clients, so all it really needs now is docs.  Maybe turn the page I started on earlier tonight into a "[GitHub Page](https://www.perplexity.ai/search/3fca39c4-0633-4650-99d2-532343f349c4?s=u)"

## Thoughts about future 1.x releases

* I'd kinda like to turn setup.php into a kind of library, something mobile mainframes can use without having to do all those file_get/put_contents calls directly... but I might not bother going there.  Any spare time I have for this is would probably be more fun working on 2.0 :-)
* I do, tho, want to put a live demo up (for myself) so I can test it on my iPhone.  I might keep a personal 1.0 instance up and running, maybe use it for some slightly more useful tools.  But the live demo might lead to some CSS tweaks, JS fixes (cuz Safari is the new IE, lol) and stuff like that.
* I'd kinda like to see if my Python client will run in Termux - that might lead to a patch or towo too.


## Game plan for version 2.0

Here's where things really start to get interesting!

### Clients

* I found out there's a "libwebsockets" - a C API for WebSockets!  This means Mobile Mainframe 2.0 can support WebSockets in both the terminal AND the browser.  That would be awesome!  Probably won't run on Termux... I'd be shocked if it had a libwebsockets port (but then again maybe).  But Who cares?  If we're talking WebSockets, we're talking browsers - and everything has a browser.
* And speaking of which, at this point it'll finally be time for xterm.js.  Of course I envisioned this thing using xterm.js from day 1, so I was always heading in this direction for clients.  Not a big deal in v1, but WebSockets changes things.  Over HTTP, who cares... but once you're talking WebSockets, who knows!  I've seen Vim, bastet, even full-on SSH shells running over WebSockets.  In fact... I think one of our customers at work is using it as the front-end to an actual mainframe. :-D


### Servers

* I also recently scored a free Python (Bottle) web hosting account, so I'd like to see if I can build a Mobile Mainframe server for Bottle.  Of course, there are more languages out there - C#, Java, Ruby - and hundreds more frameworks - Django, Flask, CodeIgniter, Laravel, Rails etc. - so there's no way I'll be able to support them all.  But PHP is what, 80% of the internet?  Whatever.  I think my point at this stage of the game is, to define the "protocol" (for lack of a better word) for servers.  Things like `::welcome::` that are not user input, but input from clients themselves.
* Back in PHP-land, with libwebsockets comes cookie-based sessions!  So instead of using those JSON files for user sessions, I can just use $_SESSION like any other web app.  Maybe I'll find a free web hosting with SSL included (my Python server has SSL) and do an actual login!
