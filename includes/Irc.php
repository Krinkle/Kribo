<?php
/**
 * This file contains the irc class.
 * KriboIrc is the heart of the bot that deals with
 * all interaction from and to the irc server..
 *
 * @author Timo Tijhof, 2011
 * @since 0.1
 * @package Kribo
 */
class KriboIrc {

	/* References to important instances */
	public $conf; // instance of KriboConfig
	private $main; // instance of KriboMain

	/* Status */
	private $isIdentified = false;
	private $hasAutojoined = false;
	private $triesLeft = 3;
	private $currentNick = '';


	/* Connection */
	private $retryFailures = array( 'Connection reset by peer' );
	private $sock;


	/* Init */
	public function __construct( KriboMain $main, KriboConfig $config ) {
		$this->main = $main;
		$this->conf = $config;
	}

	public function start(){
		$this->connect();
	}


	/* Connectivity */
	private function connect(){

		// Create cocket
		$this->sock = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );
		if ( $this->sock === false ) {
			$errorMsg = socket_strerror( socket_last_error() );
			$this->log( "Fatal error: Could not create socket. Error: $errorMsg" );
			kfDie();
		}

		// Bind socket
		if ( !socket_bind( $this->sock, $this->conf->socketHostname ) ) {
			$errorMsg = socket_strerror( socket_last_error() );
			$this->log( "Fatal error: Could not bind socket. Error: $errorMsg" );
			kfDie();
		}

		// Connection socket
		if ( !socket_connect( $this->sock, $this->conf->serverHost, $this->conf->serverPort ) ) {
			$errorMsg = socket_strerror( socket_last_error() );
			$this->log( "Fatal error: Could not bind socket. Error: $errorMsg" );
			kfDie();
		}

		// Start read loop
		while ( $rawline = @socket_read( $this->sock, 65000, PHP_NORMAL_READ ) ) {

			// Ignore empty lines
			if ( in_array( $rawline, array( "\n", "\r", "\r\n" ) ) ) {
				continue;
			}

			// Ping ? Pong!
			$ponged = $this->ifPingThenPong( $rawline );

			// Not identified yet ?
			if ( !$this->isIdentified ) {
				$this->identify();
			}

			// Autojoin only once
			// Not untill MOTD is done
			if ( !$this->hasAutojoined && strpos( $rawline, 'MOTD' ) ) {
				$this->autojoin();
			}

			// Pass it on
			if ( !$ponged ) {
				$this->onReceive( $rawline );
			}

		}

		// If loop was not started, or aborted, figure out what happened and die or reconnect.
		if ( socket_last_error() ) {
			$errorMsg = socket_strerror( socket_last_error() );
			$this->log( "End of socket read (server closed connection?). Response: $errorMsg" );

			// This is a valid response to reconnect.
			if ( $this->triesLeft > 0 && in_array( $errorMsg, $this->retryFailures ) ) {
				$this->log( "The error response is in the list of retryFailures. {$this->triesLeft} tries left. Reconnecting..." );
				$this->triesLeft = $this->triesLeft-1;
				$this->connect();

			// No more tries and/or not a reason to reconnect.
			} else {
				kfDie();
			}
		}

	}

	private function disconnect(){
		@socket_shutdown( $this->sock, 2 );
		@socket_close( $this->sock );
		$this->sock = null;
	}

	/**
	 * @todo FIXME: Sanatize these $conf values (may not contain spaces for example)
	 */
	private function identify(){
		$conf = $this->conf;


		// "USER identifier hostname server :realname"
		$realname = $this->getRealName();
		$this->writeRaw( "USER {$conf->userChatName} {$conf->socketHostname} {$conf->serverHost} :$realname" );
		$this->sendNick( $conf->userChatName );

		// Identify with services.
		if ( strlen($this->conf->userAuthService) && strlen($this->conf->userAuthPattern)
			&& strlen($this->conf->userAuthID) && strlen($this->conf->userAuthPassword) )
		{
			$msg = $this->conf->userAuthPattern;
			$msg = str_replace( '$1', $this->conf->userAuthID, $msg );
			$msg = str_replace( '$2', $this->conf->userAuthPassword, $msg );
			$this->sendPrivmsg( $msg, $this->conf->userAuthService );

			}

		$this->isIdentified = true;
	}

	private function autojoin(){
		foreach( $this->conf->autoJoinChannels as $channel ) {
			$this->sendJoin( $channel );
		}
		$this->hasAutojoined = true;
	}


	/* Events */

	private function onReceive( $rawline ) {
		$this->log( $rawline, 'receive' );

		$parsed = $this->parseIncomingRaw( $rawline );
		$this->main->runHooksFor( 'onReceive', array( $parsed, $this ) );
	}


	/* Base functions */

	private function writeRaw( $rawline ) {
		// Multi-line writing is supported in some server commands,
		// but once here in writeRaw() there should not be multiple lines.
		// Following is a forced last guard against possible mistakes.
		// "PRIVMSG Foo\nBar" is seen as a PRIVMSG command following by
		// the (invalid) "Bar" command.
		$parts = explode( "\n", $rawline, 2 );
		$rawline = $parts[0];

		$this->main->runHooksFor( 'writeRaw', array( $rawline, $this ) );

		$this->log( $rawline, 'write' );
		if ( !socket_write( $this->sock, "$rawline\r\n" ) ) {
			$this->error( 'Previous could not be delivered by socket_write.', __FUNCTION__ );
		}

		// wait for a second
		sleep(1);
	}


	/* Server commands */

	// PONG
	public function sendPong( $pingData ) {
		$continue = $this->main->runHooksFor( 'sendPong', array( $pingData, $this ) );
		if ( !$continue ) {
			return false;
		}
		$this->writeRaw( "PONG $pingData" );
	}

	// PRIVMSG
	// Prefix is useful for dynamic multi-line messages,
	// in which case prefix will be prefixed to each message
	public function sendPrivmsg( $msg, $target, $prefix = '' ) {

		// Multi-line messages as array
		if ( is_array( $msg ) ) {
			foreach( $msg as $line ) {
				$this->sendPrivmsg( $line, $target, $prefix );
			}
			return;
		}

		// Split too-long lines
		$parts = explode( "\n", wordwrap( $msg, 500 - strlen($prefix), "\n", true ) );
		if ( count($parts) > 1 ) {
			$this->sendPrivmsg( $parts /* array */, $target, $prefix );
			return;
		}

		// Normal
		$continue = $this->main->runHooksFor( 'sendPrivmsg', array( $msg, $target, $this ) );
		if ( !$continue ) {
			return false;
		}
		$this->writeRaw( "PRIVMSG $target :{$prefix}$msg" );
	}

	// NOTICE
	public function sendNotice( $msg, $target ) {
		$continue = $this->main->runHooksFor( 'sendNotice', array( $msg, $target, $this ) );
		if ( !$continue ) {
			return false;
		}
		$this->writeRaw( "NOTICE $target :$msg" );
	}

	// NICK
	public function sendNick( $newNick ) {
		$this->main->runHooksFor( 'beforeSendNick', array( $newNick, $this ) );

		$this->writeRaw( "NICK $newNick" );

		$this->currentNick = $newNick;
	}

	// JOIN
	public function sendJoin( $channel ) {
		$this->writeRaw( "JOIN $channel" );

		$this->main->runHooksFor( 'afterSendJoin', array( $channel, $this ) );
	}

	// PART
	public function sendPart( $channel ) {
		$this->main->runHooksFor( 'beforeSendPart', array( $channel, $this ) );

		$this->writeRaw( "PART $channel" );

	}

	// QUIT
	public function sendQuit( $msg = null ) {
		$msg = is_null( $msg ) ? 'I\'ll be back!' : $msg;

		$this->main->runHooksFor( 'beforeSendQuit', array( $msg, $this ) );

		$this->writeRaw( "QUIT :$msg" );

		$this->disconnect();
	}


	/* Utilities (@public) */

	public function getAltNick(){

		$current = $this->currentNick;
		$alt = str_replace( '$1', $current, $this->conf->userAltNamePattern );
		return $alt;

	}

	public function getRealName(){

		$raw = $this->conf->userRealName;
		$parsed = str_replace( '$1', $this->conf->version, $raw );
		return $parsed;

	}

	// Read-only variable
	public function getCurrentNick(){
		return $this->currentNick;
	}


	/* Utilities (@private) */

	private function ifPingThenPong( $rawline ) {
		$parts = explode( ' ', $rawline );
		if ( $parts[0] == 'PING' ) {
			$this->log( $rawline, 'pingpong' );
			$this->sendPong( $parts[1] );
			return true;
		}
		return false;
	}

	private function parseIncomingRaw( $rawline ) {
		$parsed = array(
			'rawline' => $rawline,

			// Basic parts
			'prefix' => null,
			'command' => null,
			'params' => null,

			// More advanced parts
			'senderType' => null, // 'server' or 'client'
			'senderNick' => null,
			'senderId' => null,
			'senderHost' => null,
			'paramsMiddle' => null,
			'paramsTrailing' => null,
		);

		/* Basic parts */

		// Validate (RFC 1459: Internet Relay Chat Protocol)
		// [':' <prefix> <SPACE> ] <command> <params>

		$parts = explode( ' ', $rawline, 3 );

		// ":<prefix> <command> <params>" ?
		if ( isset( $parts[0] ) && substr( $parts[0], 0, 1 ) == ':'
			&& isset( $parts[1] ) && isset( $parts[2] ) ) {

			// Prefix
			$parsed['prefix'] = $parts[0];

			// Command
			$parsed['command'] = $parts[1];

			// Params
			$parsed['params'] = $parts[2];


		// "<command> <params>" ?
		} elseif ( isset( $parts[0] ) && substr( $parts[0], 0, 1 ) !== ':'
			&& isset( $parts[1] ) && !isset( $parts[2] ) ) {

			// Prefix
			$parsed['prefix'] = null;

			// Command
			$parsed['command'] = $parts[0];

			// Params
			$parsed['params'] = $parts[1];

		// Invalid
		} else {
			return false;
		}

		/* More advanced parts */

		// Prefix (sender)
		// - ":Krinkle!~Krinkle@wikimedia/Krinkle" (hostname cloak)
		// - ":Foobar!~Foobar@cm12.34.56.78.dynamic.ziggo.nl" (unregistered, normal ip or hostname)
		// - ":Foobar!123ab01a@gateway/web/freenode/ip.80.100.192.168" (proxy, hostname cloak ish)
		// - ":foobar.domain.tld" (The Server)
		// nick!user@host
		preg_match( "/^:(.*)!(.*)@(.*)/", $parsed['prefix'], $prefMatch );

		// Is this the server or a client ?
		if ( isset( $prefMatch[1] ) && isset( $prefMatch[2] ) && isset( $prefMatch[3] ) ) {
			$parsed['senderType'] = 'client';
			$parsed['senderNick'] = $prefMatch[1];
			$parsed['senderId'] = $prefMatch[2];
			$parsed['senderHost'] = $prefMatch[3];
		} else {
			$parsed['senderType'] = 'server';
			$parsed['senderNick'] = substr( $parsed['prefix'], 1);
		}

		// Params
		$partsParams = explode( ':', $parsed['params'], 2 );

		$parsed['paramsMiddle'] = trim( $partsParams[0] ); // May end in a space
		$parsed['paramsTrailing'] = isset($partsParams[1]) ? $partsParams[1] : '';

		return $parsed;
	}

	private function log( $msg = '', $action = null ) {
		$action = is_null( $action ) ? '' : " [$action]";
		return kfLog( '[' . __CLASS__ . "]$action $msg" );
	}

	private function error( $msg = '', $context = '?' ) {
		return kfLog( '[' . __CLASS__ . "] [ERROR] $context: $msg" );
	}
}
