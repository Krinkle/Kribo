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
 * This file initializes the Kribo application.
 * This is the only valid point of entry.
 */

/**
 * Always strict
 */
error_reporting( -1 ); ini_set( 'display_errors', 1 );

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
