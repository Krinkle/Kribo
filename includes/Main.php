<?php
/**
 * This file contains the main application class.
 * The entry point will only interact via this class.
 *
 * @author Timo Tijhof, 2011
 * @since 0.1
 * @package Kribo
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