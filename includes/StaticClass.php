<?php
/**
 * This file contains the base class to create static classes
 * aka "simple function group".
 *
 * @author Timo Tijhof, 2011
 * @since 0.1
 * @package Kribo
 */

/**
 * staticClass
 *
 * Mostly to be able to detect whether a class
 * is meant to be a static class (by checking parent class)
 * and saves a few bytes by not having to set
 * the private keyword for the constructor function.
 */
class staticClass {

	// Disallow instantiation
	private function __construct() {
	}
}
