<?php
/**
 * This file initializes the Kribo application.
 * This is the only valid point of entry.
 *
 * @author Timo Tijhof, 2011
 * @since 0.1
 * @package Kribo
 */

/**
 * Always strict
 */
error_reporting( -1 );
ini_set( 'display_errors', 1 );
date_default_timezone_set( 'UTC' );

// Minimum PHP version
if ( !function_exists( 'version_compare' ) || version_compare( phpversion(), '5.3.2' ) < 0 ) {
	echo "<b>Kribo Fatal:</b> Kribo requires at least PHP 5.3.2\n";
	exit;
}

/**
 * Load
 */
$KriboDir = __DIR__;

// Global
require_once( "$KriboDir/includes/Defines.php" );
require_once( "$KriboDir/includes/Functions.php" );
require_once( "$KriboDir/includes/StaticClass.php" );

// Config
require_once( "$KriboDir/includes/Config.php" );
require_once( "$KriboDir/includes/DefaultConfig.php" );
require_once( "$KriboDir/LocalConfig.php" );

// Commands
require_once( "$KriboDir/includes/commands/CoreCommands.php" );
require_once( "$KriboDir/includes/hooks/CoreHooks.php" );

// Main
require_once( "$KriboDir/includes/Irc.php" );
require_once( "$KriboDir/includes/Main.php" );

/**
 * Initialize main application
 */
$KriboMain = new KriboMain( $KriboConfig );

/**
 * Run hooks
 */
$KriboMain->runHooksFor( 'init', array( $KriboMain, $KriboConfig ) );

/**
 * Start the bot
 */
$KriboMain->start();

// End of the track has been reached.
$KriboMain->endOfTrack();
