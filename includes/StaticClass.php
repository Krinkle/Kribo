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
 * This file contains the base class to create static classes
 * aka "simple function group".
 *
 * Mostly to be able to detect whether a class
 * is meant to be a static class (by checking parent class)
 * and saves a few bytes by not having to set
 * the private keyword for the constructor function.
 */


class staticClass {
	private function __construct(){
		// Prevent object creation
	}
}