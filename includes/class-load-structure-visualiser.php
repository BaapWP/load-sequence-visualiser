<?php

/**
 * Load structure visualiser
 * 
 * @author Shantanu Desai <shantanu2846@gmail.com>
 * @since 0.0.1
 */
// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) )
	exit();

if ( !class_exists( 'Load_Structure_Visualiser' ) ) {

	/**
	 * Load structure visualiser
	 *
	 * @since 0.0.1
	 */
	class Load_Structure_Visualiser {

		/**
		 * All the included files 
		 * 
		 * @var array
		 */
		private $included_files = array();

		/**
		 * All the hooks that have been fired
		 * 
		 * @var array 
		 */
		private $hooks_fired = array();

		/**
		 * All the constants that have been declared
		 * 
		 * @var array 
		 */
		private $declared_constants = array();

		/**
		 * All the globals that have been declared
		 * 
		 * @var array 
		 */
		private $declared_globals = array();

		/**
		 * Initialise class
		 * 
		 * @since 0.0.1
		 */
		public function init() {

			add_action( 'all', array( $this, 'get_raw_data' ) );
		}

	}// class

}// if !class_exists()

