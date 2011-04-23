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
 * This file contains the main application class.
 * The entry point will only interact via this class.
 */


class KriboMain {

	private $irc;
	private $config;

	public function __construct( KriboConfig $config ){

		$this->config = $config;
		$this->irc = new KriboIrc( $this, $config );
	
	}

	public function runHooksFor( $id, $args ) {
		if ( !isset( $this->config->hookRegistry[$id] ) ) {
			return true;
		}
		foreach( $this->config->hookRegistry[$id] as $hookFunc ) {
			if ( is_callable( $hookFunc ) ) {
				$return = call_user_func_array( $hookFunc, $args );
				if ( $return === false ) {
					return false;
				}
			}		
		}
		return true;
	}

	public function unregisterHookFunc( $funcRemove, $id ) {
		if ( !isset( $this->config->hookRegistry[$id] ) ) {
			return true;
		}
		// Performance: http://lixlpixel.org/php-benchmarks/remove-array-items-by-value/
		$new = array();
		foreach ( $this->config->hookRegistry[$id] as $funcName ) {
			if ( $funcName !== $funcRemove ) {
				$new[] = $funcName;
			}
		}
		$this->config->hookRegistry[$id] = $new;
		return true;
	}

	public function start(){
		$this->irc->start();
	}

	public function endOfTrack(){
		kfLog( __METHOD__ );
		kfDie(1);
	}

}