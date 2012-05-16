# Kribo Hooks

## init
* Usage: Directly after $KriboMain is constructed.

```php
public static function ( KriboMain $KriboMain, KriboConfig $KriboConfig );
```

## onReceive
* Usage: Top (before other actions)

```php
public static function onReceive( Array $parsed, KriboIrc $irc );
```

## writeRaw
* Usage: Top (before other actions)

```php
public static function writeRaw( $rawline, KriboIrc $irc );
```

## sendPong
* Usage: Top (before other actions)
* Return: If false, will abort the action

```php
public static function sendPong( $pingData, KriboIrc $irc );
```

## sendPrivmsg
* Usage: Top (before other actions)
* Return: If false, will abort the action

```php
public static function sendPrivmsg( $msg, $target, KriboIrc $irc );
```

## sendNotice
* Usage: Top (before other actions)
* Return: If false, will abort the action

```php
public static function sendNotice( $msg, $target, KriboIrc $irc );
```

## beforeSendNick

```php
public static function beforeSendNick( $newNick, KriboIrc $irc );
```

## afterSendJoin

```php
public static function afterSendJoin( $channel, KriboIrc $irc );
```

## beforeSendPart

```php
public static function beforeSendPart( $channel, KriboIrc $irc );
```

## beforeSendQuit

```php
public static function beforeSendQuit( $msg, KriboIrc $irc );
```

# Premade registries for common hooks

Since adding commands that the bot responds too are likely the most common kind, there is a dedicated registry for that. You can hook into `onRecieve` directly. To see how that works, look at [CoreHooks.php](https://github.com/Krinkle/ts-krinkle-Kribo/blob/master/includes/hooks/CoreHooks.php). As you can see, we use that hook internally as well.

## commandsRegistry
```php
$KriboConfig->commandsRegistry['commandname'] = array( .. );

// Example
$KriboConfig->commandRegistry['date'] = array(

	/**
	 * Prefix
	 *
	 * Prefix overrides $KriboConfig->commandPrefixDefault if it's a string.
	 * If set to true (or other non-string values) the default (commandPrefix) will be used
	 * if prefix is "!", this means the callback will be called
	 * when "!date" is said in the channel.
	 *
	 * $1: $KriboConfig->userChatName
	 * $2: $KriboConfig->userAuthID
	 * $3: $irc->[private]currentNick
	 */
	'prefix' => true,


	/**
	 * Callback function
	 *
	 * This value should be compatible with call_user_func_array()
	 * Called with first argument $data and the second argument
	 * the instance of KriboIrc.
	 *
	 * Example: "!date Ymd" is said by Foobar in #test
	 * $data = array(
	 *   'parsedCommand' => array(
	 *     'value' => "Ymd",
	 *     'parts' => array( "Ymd" ),
	 *   ),
	 *   'parsedLine' => array( .. ),
	 * );
	 *
	 * Example: "!date Ymd 2001" is said by Foobar as notice in private chat
	 * $data = array(
	 *   'parsedCommand' => array(
	 *     'value' => "Ymd 2001",
	 *     'parts' => array( "Ymd", "2001" ),
	 *   ),`
	 *   'parsedLine' => array( .. ),
	 * );
	 */
	'callback' => array( 'KriboCmdCore', 'cmdDate' ),

	/**
	 * Send response
	 *
	 * What to do with the return value of the callback function:
	 * KRIBO_CMD_RE_DEFAULT: Do whatever $KriboConfig->commandResponseDefault is set to.
	 * KRIBO_CMD_RE_CHANNEL: Send the message to the channel.
	 * KRIBO_CMD_RE_CHANNEL_AT: Send the message to the channel, prefixed with "user: ".
	 * KRIBO_CMD_RE_PRIVATE: Send the message in private chat to user.
	 * KRIBO_CMD_RE_PRIVATE_AT: Send the message in private chat to user, prefixed with "user: ".
	 * KRIBO_CMD_RE_IGNORE: Ignore the message, the callback takes care of everything.
	 */
	'sendResponse' => KRIBO_CMD_DEFAULT,
);
```
