<?php

/**
 * DefaultConfig.php contains custom settings
 *
 * DO *NOT* EDIT ANYTHING HERE !!
 * OVERRIDE IN LocalConfig.php INSTEAD.
 *
 * Documentation can be found at https://github.com/Krinkle/Kribo#configuration

/**
 * Init
 * -------------------------------------------------
 */
$KriboConfig = new KriboConfig();


/**
 * Meta
 * -------------------------------------------------
 */
$KriboConfig->version = '0.1.1-alpha';


/**
 * Server
 * -------------------------------------------------
 */
$KriboConfig->serverHost = 'localhost';
$KriboConfig->serverPort = 6667;
$KriboConfig->socketHostname = 0;
$KriboConfig->autoJoinChannels = array();


/**
 * Logging and debugging (not used yet)
 * -------------------------------------------------
 */

// Send to these channels
#$KriboConfig->debugChannels = array();
#$KriboConfig->logChannels = array();

// Send to STOUT (echo)
#$KriboConfig->debugToConsole = true;
#$KriboConfig->logToConsole = true;

// Append to a file (set to an absolute filepath)
#$KriboConfig->debugToFile = null;
#$KriboConfig->logToFile = null;

// If debugToFile is used, should it be cleared
// when the bot starts ?
#$KriboConfig->debugToFileClearStart = true;


/**
 * User
 * -------------------------------------------------
 */
$KriboConfig->userChatName = 'Kribo';

// $1: $KriboConfig->userChatName
$KriboConfig->userAltNamePattern = '$1_';

// $1: $KriboConfig->version
$KriboConfig->userRealName = 'KrinkleIRCBot Package $1';


/**
 * Authentication
 * -------------------------------------------------
 *
 * If all of the next four are not null, then the bot will
 * PRIVMSG userAuthService directly after the connection is established
 * (before channels are joined), with the userAuthPattern.
 *
 * The default settings are optimized for the "NickServ" service as found
 * on many servers (such as Freenode). If you're using that, then only 
 * userAuthID and userAuthPassword have to be set in LocalConfig.
 */
// Nickname of the authservice on serverHost
$KriboConfig->userAuthService = 'NickServ';

// $1: userAuthID, $2: userAuthPassword
$KriboConfig->userAuthPattern = 'IDENTIFY $1 $2';

// Username of the account with the AuthService, may be equal to userChatName
$KriboConfig->userAuthID = null;
$KriboConfig->userAuthPassword = null;


/**
 * Command info
 * -------------------------------------------------
 */
/**
 * The default listener for commands
 * Individual commands may override this
 * $1: userChatName, $2: userAuthID, $3: current nickname
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
 * Whitelist
 * -------------------------------------------------
 */
$KriboConfig->userWhitelist = array();

/**
 * Hooks
 * -------------------------------------------------
 */
// Usage: $KriboConfig->hookRegistry['hookname'][] = 'myclass::func';
// Remove: $KriboMain->unregisterHookFunc( 'myclass::func', 'hookname' );
$KriboConfig->hookRegistry = array();

$KriboConfig->hookRegistry['onReceive'][] = 'KriboCoreHooks::onReceive';
