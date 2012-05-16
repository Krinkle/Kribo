<?php
/**
 * This file contains the class for core hooks.
 * Contains the command parser and an example hook
 * to learn from (eg. KriboCoreHooks::afterSendJoin).
 * These hooks are registered in DefaultConfig.php with $KriboConfig->hookRegistry.
 * Register yours in LocalConfig.php (after including the class file).
 *
 * @author Timo Tijhof, 2011
 * @since 0.1
 * @package Kribo
 */
class KriboCoreHooks extends staticClass {

	/* Direct hooks */

	static function onReceive( $parsedLine, $irc ) {
		// Server code ?
		if ( is_numeric( $parsedLine['command'] ) ) {
			self::onServerCode($parsedLine, $irc);
		}

		// Run commands
		self::commandParser($parsedLine, $irc);

		return true;
	}

	/**
	 * Says hello to a channel after joining it
	 *
	 * @example
	 * 
	 * Registered in DefaultConfig.php:
	 *  $KriboConfig->hookRegistry['afterSendJoin'][] = 'KriboCoreHooks::helloOnJoin';
	 * To unregister it, put the following in LocalConfig.php:
	 *
	 *  $KriboConfig->hookRegistry['init'][] = 'removeDemoHooks';
	 *  function removeDemoHooks( $main, $config ) {
	 *   $main->unregisterHookFunc( 'KriboCoreHooks::helloOnJoin', 'afterSendJoin' );
	 *   return true;
	 *  }
	 *
	 */
	static function helloOnJoin( $channel, $irc ) {
			$irc->sendPrivmsg( "Hello $channel!", $channel );	
			return true;
	}

	/* Helper functions */

	static function onServerCode( $parsedLine, $irc ) {
		$code = $parsedLine['command'];

		switch ($code) {

			// ERR_NICKNAMEINUSE
			// 433 * :Nickname is already in use
			case '433':
				$irc->sendNick( $irc->getAltNick() );
				break;
		}
	}

	static function commandParser( $parsedLine, $irc ) {
		global $KriboConfig;

		$conf = $irc->conf;

		foreach( $conf->commandRegistry as $commandName => $commandInfo ) {
			$prefix = $conf->commandPrefixDefault;
			if ( isset( $commandInfo['prefix'] ) && kfStrHasLen( $commandInfo['prefix'] ) ) {
				$prefix = $commandInfo['prefix'];
			}
			$prefix = self::parseCommandPrefix( $prefix, $irc );


			// Case-insensitive !
			// <JohnDoe> MyBot: !DO SomethingHere
			// <JohnDoe> mybot: !do SomethingHere
			if ( stripos( $parsedLine['paramsTrailing'], $prefix . $commandName ) === 0 ) {
				// Remove prefix
				$effectiveLine = substr($parsedLine['paramsTrailing'], strlen( $prefix ) );
				$cmdArgs = explode( ' ', $effectiveLine );
				$cmdMain = array_shift( $cmdArgs );
				$hookFuncArgs = array(
					'parsedCommand' => array(
						'value' => implode( ' ', $cmdArgs ),
						'parts' => $cmdArgs,
					),
					'parsedLine' => $parsedLine,
				);
				$return = call_user_func_array( $commandInfo['callback'], array( $hookFuncArgs, $irc ) );
				if ( kfStrHasLen( $return ) || is_array( $return ) ) {
					$sendResponse = isset( $commandInfo['sendResponse'] ) ?
						$commandInfo['sendResponse'] : KRIBO_CMD_RE_DEFAULT;
					$sendResponse = $sendResponse === KRIBO_CMD_RE_DEFAULT ?
						$KriboConfig->commandResponseDefault : $sendResponse;
					switch ( $sendResponse ) {
						case KRIBO_CMD_RE_CHANNEL:
							$irc->sendPrivmsg( $return, $parsedLine['paramsMiddle'] );
							break;
						case KRIBO_CMD_RE_CHANNEL_AT:
							$irc->sendPrivmsg( $return, $parsedLine['paramsMiddle'], "{$parsedLine['senderNick']}: " );
							break;
						case KRIBO_CMD_RE_PRIVATE:
							$irc->sendPrivmsg( "PRIVATE: $return", $parsedLine['paramsMiddle'] );
							break;
						case KRIBO_CMD_RE_PRIVATE_AT:
							$irc->sendPrivmsg( "PRIVATE_AT: $return", $parsedLine['paramsMiddle'] );
							break;
						// case KRIBO_CMD_RE_IGNORE:
						default:
					}
				}
			}
		}
	}

	static function parseCommandPrefix( $prefix, $irc ) {
		$prefix = str_replace( '$1', $irc->conf->userChatName, $prefix );
		$prefix = str_replace( '$2', $irc->conf->userAuthID, $prefix );
		$prefix = str_replace( '$3', $irc->getCurrentNick(), $prefix );
		return $prefix;
	}
}
