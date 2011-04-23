<?php

/**
 * LocalConfig.php contains your personal
 * configuration for the bot.
 */


/**
 * Server
 * -------------------------------------------------
 */
$KriboConfig->serverHost = 'irc.freenode.net';
$KriboConfig->autoJoinChannels = array( '#botwar' );


/**
 * User
 * -------------------------------------------------
 */
$KriboConfig->userChatName = 'Kribo';


/**
 * Authentication
 * -------------------------------------------------
 */
// NickServ login
#$KriboConfig->userAuthID = 'MyAccount';
#$KriboConfig->userAuthPassword = 'mypassword';


/**
 * Commands
 * -------------------------------------------------
 */
$KriboConfig->commandPrefixDefault = '$3: ';


/**
 * Hooks
 * -------------------------------------------------
 */
// Demo hook (sends "Hello #channelname!" message when the bot joins a channel)
$KriboConfig->hookRegistry['afterSendJoin'][] = 'KriboCoreHooks::helloOnJoin';


/**
 * Plugins
 * -------------------------------------------------
 */
require_once( __DIR__ . '/plugins/UserWhitelist.php' );

// Whitelist:
#$KriboConfig->userWhitelist = array( 'yourgroupcloak/JohnDoe' );
