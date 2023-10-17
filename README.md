# Issue #1: Server.php maxing out CPU usage

This could be the small size of my server (a "Nanode" on Linode) talking,
but as soon as someone connects to a command-line tool (like my old game,
"Darkest Hour", recompiled with GNU Readline), BOOM.  100% CPU usage.
The reason is obviously something way above my pay grade for after-hours
fun-coding.  I'm going to look at server.php to see if I can pick out any
kind of obvious "oh, duh" type mistake... but I doubt I'll find it.  Not so
obvious if you haven't been doing socket programming for 10+ years.  This
actually demonstrates exactly why I started this project: so I can host my
command-line apps on a server without having to do crazy socket stuff with
every project.

However, it seems there is a simpler solution in sight:
[websocketd](http://websocketd.com/).  With websocketd, I can program CLI
apps in any language I want, then with a single command, serve it over
WebSockets.  I'm really liking this option, because it's clearly designed
by people who DO know sockets inside-out-and-backwards; proxies and cool
reverse-lookup features and all kinds of crazy fun stuff that I may never
have the time to fully understand even in a lifetime.  But for example, I
ran my game "Darkest Hour" - in Mobile Mainframe and in terminal - using
this one-liner:

`websocketd --binary --port=12345 darkest-hour > /dev/null &`

Then I exited my server and ran it in a terminal with this command:

`websocat --binary ws://104.200.17.247:12345`

Or this one:

`wscat -c ws://104.200.17.247:12345`

Or by typing/pasting `ws://104.200.17.247:12345` into my Mobile Mainframe terminal.  And I'm sure there are lots of other tools out there I haven't messed with yet.  So yeah, the part I most wanted this to accomplish - the "command-line-app-over-the-web hosting" sort of setup - is already done.

But there are some drawbacks to this approach, too.  For one thing, those not-web-based terminals run in "line mode", as does websocketd.  That's great for the vast majority of command-line tools... but I had Mobile Mainframe running vim, bastet and other "real-time" apps, and losing that kinda stinks.  Not that I expect to ever build anything that big... but I mean even a simple Roguelike goes by a single char, without a newline, and I mean what the puck.  I had that.  It maxes out the CPU but I had that. :-D

Okay seriously tho, I'm thinking of putting an issue asking about this up on the websocketd GitHub... I'm thinking it would be an easy enough change for them to make, to add an option telling it "don't append new-lines at the end of every message" or something like that... idk

------------------------------------------------------------------------

# Mobile Mainframe

This fun little project is [explained in better detail here](https://thegeekonskates.github.io/mobile-mainframe/).

# Change Log

## Version 2.1

* Removed code from version 1; we don't need it anymore
* Added some notes to `server.php` explaining things a bit better
* Reorganized things a bit, dropping `ws.php` and `xterm.html` etc.


## Version 2.0

This major update added WebSockets support, bringing any command-line app you want to the web - text editors, language interpreters, even MUDs over telnet.


## Version 1.0

The original release was just an experiment; I knew taking on what I really wanted (which later went on to become version 2.0) was a huge project, so I started out with an HTTP-based approach to communicating between a web server and a terminal.  It's still on GitHub, but it's not nearly as useful - or as cool - as what came next.
