<?php

/* Server */

$KriboConfig->serverHost = 'irc.freenode.net';
$KriboConfig->autoJoinChannels = array(
	'#botwar',
);


/* User */

$KriboConfig->userChatName = 'Kribo';


/* Authentication */

// NickServ login
$KriboConfig->userAuthID = 'MyAccount';
$KriboConfig->userAuthPassword = 'mypassword';


/* Commands */

$KriboConfig->commandPrefixDefault = '$3: ';

/* Hooks */

// Demo hook (sends "Hello #channelname!" message when the bot joins a channel)
$KriboConfig->hookRegistry['afterSendJoin'][] = 'KriboCoreHooks::helloOnJoin';


/* Whitelist */

#$KriboConfig->userWhitelist = array( 'yourgroupcloak/JohnDoe' );


/* Plugins */

#require_once( $KriboDir . '/plugins/FooBar/FooBar.php' );
