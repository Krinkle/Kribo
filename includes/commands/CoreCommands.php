<?php
/**
 * Core command functions. Registered in DefaultConfig.
 *
 * @author Timo Tijhof, 2011
 * @since 0.1
 * @package Kribo
 */
class KriboCoreCommands extends staticClass {

	static function cmdDate( $data, $irc ) {

		// Time string may me multple words
		// Use our own limited explode instead of
		// default parts.
		$args = explode( ' ', $data['parsedCommand']['value'], 2);

		$format = isset( $args[0] ) && kfStrHasLen( $args[0] ) ? $args[0] : 'r';
		$time = isset( $args[1] ) && kfStrHasLen( $args[1] ) ? strtotime( $args[1] ) : time();

		return date( $format, $time );
	}

	static function cmdQuit( $data, $irc ) {
		global $KriboConfig;

		// Check is user is whitelisted
		if ( !kfIsIrcDataSenderTrusted( $data ) ) {
			$irc->sendNotice( "The quit command is restricted to users on the whitelist.", $data["parsedLine"]['senderNick'] );
			return false;
		}

		$msg = kfStrHasLen( $data['parsedCommand']['value'] ) ? $data['parsedCommand']['value'] : null;
		$irc->sendQuit( $msg );
		return true;
	}

}
