<?php
/**
 *
 * Created on April 13, 2011
 *
 * Copyright 2011 Krinkle <krinklemail@gmail.com>
 *
 * This file is licensed under
 * the Creative Commons Attribution 3.0 Unported License 
 * <http://creativecommons.org/licenses/by/3.0/>
 *
 * @package Kribo
 */

/**
 * This file declares all global functions in Kribo.
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
