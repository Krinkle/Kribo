<?php

/**
 * DefaultConfig.php contains custom settings
 *
 * DO *NOT* EDIT ANYTHING HERE !!
 * OVERRIDE IN LocalConfig.php INSTEAD.
 *
 */

/**
 * Init
 * -------------------------------------------------
 */
$KriboConfig = new KriboConfig();


/**
 * Meta
 * -------------------------------------------------
 */
$KriboConfig->version = '0.1-alpha';


/**
 * Server
 * -------------------------------------------------
 */
$KriboConfig->serverHost = 'localhost';
$KriboConfig->serverPort = 6667;
$KriboConfig->socketHostname = 0;
$KriboConfig->autoJoinChannels = array();


/**
 * Logging and debugging
 * -------------------------------------------------
 */
// Send to these channels
$KriboConfig->debugChannels = array();
$KriboConfig->logChannels = array();

// Send to STOUT (echo)
$KriboConfig->debugToConsole = true;
$KriboConfig->logToConsole = true;

// Append to a file (set to an absolute filepath)
$KriboConfig->debugToFile = null;
$KriboConfig->logToFile = null;

// If debugToFile is used, should it be cleared
// when the bot starts ?
$KriboConfig->debugToFileClearStart = true;


/**
 * User
 * -------------------------------------------------
 */
$KriboConfig->userChatName = 'Kribo';
$KriboConfig->userAltNamePattern = '$1_'; // $1: userChatName

// $1 will be replaced with kcVersion in initialization
$KriboConfig->userRealName = 'KrinkleIRCBot Package $1';


/**
 * Authentication
 * -------------------------------------------------
 *
 * If the next fou are not null,
 * AuthService will be PRIVMSG'ed directly
 * after the connection is established (before channels are joined)
 */
$KriboConfig->userAuthService = 'NickServ'; // Nickname of the authservice on the network
$KriboConfig->userAuthPattern = 'IDENTIFY $1 $2'; // $1: AuthID, $1: AuthPassword
$KriboConfig->userAuthID = null; // Username of the account with the AuthService
$KriboConfig->userAuthPassword = null;


/**
 * Command info
 * -------------------------------------------------
 */
/**
 * The default listener for commands
 * Individual commands may override this
 * $1: userChatName, $2: AuthID, $3: current nickname
 * Example values:
 * - "!" (!foo)
 * - "@" (@foo)
 * - "." (.foo)
 * - "" (foo)
 * - "$1: " (KriboNick: foo)
 * - "$2: " (Kribo: foo)
 * - "$3 " (KriboNick__ foo)
 */
$KriboConfig->commandPrefixDefault = '$3: ';

// Check HOOKS for more info
$KriboConfig->commandResponseDefault = KRIBO_CMD_RE_CHANNEL_AT;


/**
 * Command registry
 * -------------------------------------------------
 * This keeps track of all commands.
 * Keys are the command names.
 * Values are arrays with options.
 * Check HOOKS for more info.
 */
$KriboConfig->commandRegistry = array();

// Example commands
$KriboConfig->commandRegistry['date'] = array(
	'callback' => array( 'KriboCoreCommands', 'cmdDate' ),
);
$KriboConfig->commandRegistry['quit'] = array(
	'callback' => array( 'KriboCoreCommands', 'cmdQuit' ),
);


/**
 * Hooks
 * -------------------------------------------------
 */
// Usage: $KriboConfig->hookRegistry['hookname'][] = 'myclass::func';
// Remove: $KriboMain->unregisterHookFunc( 'myclass::func', 'hookname' );
$KriboConfig->hookRegistry = array();

$KriboConfig->hookRegistry['onReceive'][] = 'KriboCoreHooks::onReceive';
