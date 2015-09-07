[![Build Status](https://travis-ci.org/Krinkle/Kribo.svg)](https://travis-ci.org/Krinkle/Kribo)

# Krinkle
_Krinkle IRC Bot_

## Introduction
Kribo is a small PHP-framework for creating simple IRC bots. By default it has little function but its power is in the extensibility through plugins.

Be it custom functionality of the bot (executing commands to a server), or simple commands like "!date", or to log a transcript of the channels, you can do it with Kribo.

## Requirements
* PHP: 5.3.2+
* Shell access to your server
* Server permission: PHP may not be in safe mode and socket connections must be allowed from your account and from PHP.

## Installation
1. Configure Kribo in `LocalConfig.php`
1. Start the bot with:<br>`php Init.php`

## Configuration
One can have per-instance local and shared configuration (e.g. nickname, target channel(s), activated plugins and more).

The configuration is stored in the global `$KriboConfig` object and is populated through `./includes/DefaultConfig.php`. Settings can be overriden or extended through `./LocalConfig.php` and by plugins installed through `./LocalConfig.php`.

### Server
* `serverHost`: Hostname of the IRC server to connect to (e.g. "localhost" or "irc.freenode.net")
* `serverPort`: Port to connect to on serverHost. The default (6667) should be fine in most cases
* `socketHostname`: The IP-address to bind the socket to. Not currently used.
* `autoJoinChannels`: Channels joined on start-up.

### User
* `userChatName`: Default username that the bot will use to identify itself when connecting to serverHost.
* `userAltNamePattern`: What to use if the nickname is already taken or may not be used. You can use `$1` as dynamic substitute for `userChatName`. So if `userChatName = "MyBot";` and `userAltNamePattern = "$1_";`, then it will use "MyBot_" if "MyBot" is taken.
* `userRealName`: The value to use as "real name" in the IRC user information. `$1` will be replaced with value of version.

### Authentication

If all of the next four are not null, then the bot will PRIVMSG `userAuthService` directly after the connection is established (before channels are joined), with the `userAuthPattern`. The default settings are optimized for the "NickServ" service as found on many servers (such as Freenode). If you're using that, then only `userAuthID` and `userAuthPassword` have to be set in `./LocalConfig.php`.

* `userAuthService`: Nickname of the authservice available on serverHost
* `userAuthPattern`: Pattern of the message that is to be send to userAuthServer to identify. $1 will be replaced with userAuthID, and $2 with userAuthPassword.
* `userAuthID`: Auth username
* `userAuthPassword`: Auth password. Be careful when publishing your LocalConfig that this isn't exposed!

### Command info
* `commandPrefixDefault`: The default listener for commands. Individual commands may override this.
 * $1: userChatName
 * $2: userAuthID
 * $3: current nickname.
 * Example values:
 <br>"!" (!foo)
 <br>"$1: " (KriboNick: foo)
 <br>"$2: " (Kribo: foo)
 <br>"$3 " (KriboNick__ foo)
* `commandResponseDefault`: One of `KRIBO_CMD_RE_DEFAULT`, `KRIBO_CMD_RE_CHANNEL`, `KRIBO_CMD_RE_CHANNEL_AT`, `KRIBO_CMD_RE_PRIVATE`, `KRIBO_CMD_RE_PRIVATE_AT`, `KRIBO_CMD_RE_IGNORE`.

### Command registry

* `commandRegistry`: This keeps track of all commands. Keys are the command names. Values are arrays with options. Check [HOOKS](./HOOKS.md) for more info.

### Hooks

* `hookRegistry`: Registry for hooks. For example:

```php
$KriboConfig->hookRegistry['hookname'][] = 'myclass::func';

$KriboMain->unregisterHookFunc( 'myclass::func', 'hookname' );

$KriboConfig->hookRegistry['onReceive'][] = 'KriboCoreHooks::onReceive';
```

### Plugins

Check out https://github.com/Krinkle/Kribo-plugins

## FAQ
1. _Where can I download Kribo?_
   <br>Get started right away with a git clone:
   <br>`git clone https://github.com/Krinkle/Kribo.git`
   <br>Or download the latest master as a ZIP from: https://github.com/Krinkle/Kribo/zipball/master

1. _How do I install Kribo?_
   <br>Check [Installation](#installation).

1. _Where can I get plugins?_
   <br>I mainain a small stock at https://github.com/Krinkle/Kribo-plugins

1. _Where should I report X or request Y?_
   <br>If you encounter any problem, have feedback, suggestions, patches, feature
   requests etc. please enter in the Issue tracker:
   https://github.com/Krinkle/Kribo/issues

1. _Under what license is Kribo available?_
   <br>[Creative Commons Attribution 3.0 Unported](https://creativecommons.org/licenses/by-sa/3.0/)
