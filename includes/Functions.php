<?php
/**
 * This file declares all global functions in Kribo.
 *
 * @author Timo Tijhof, 2011
 * @since 0.1
 * @package Kribo
 */
function kfLog( $msg ) {
	echo '[' . date('r') . "] $msg\n";
}

function kfDie(){
	kfLog( __FUNCTION__ );
	die(1);
}

/**
 * Check wether this is a non-empty string or not.
 * A simple !empty(), isset(), is_string() or strlen() can't be used.
 *
 * - "strlen($v) > 0" also returns true for other things (like int: strlen(0)=1, strlen(10)=2)
 * - "!empty($v)" returns false for '0'
 */
function kfStrHasLen( $var = null ) {
	return is_string( $var ) && ( strlen( $var ) > 0 );
}

/**
 * @param $data array: Data array with an irc response, as given by Kribo to the
 * command hook handlers.  
 */
function kfIsIrcDataSenderTrusted( $data ) {
	global $KriboConfig;

	// Check is user is whitelisted
	if ( isset( $KriboConfig->userWhitelist )
		&& is_array( $KriboConfig->userWhitelist )
		&& in_array( $data['parsedLine']['senderHost'], $KriboConfig->userWhitelist ) ) {
		return true;
	}

	return false;
}
