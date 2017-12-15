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
		 * Raw data
		 * 
		 * An array containing a a list of included files and defined constants with 
		 * current filters as keys.
		 *
		 * @var array 
		 * 
		 * @since 0.0.1
		 */
		private $raw_data = array(
			'includes'	 => array(),
			'constants'	 => array()
		);

		/**
		 * All included files
		 * 
		 * A list of all included files.
		 * 
		 * @var array 
		 * 
		 * @since 0.0.1
		 */
		private $all_included_files = array();

		/**
		 * All defined constants
		 * 
		 * A list of all defined constants.
		 * 
		 * @var array
		 * 
		 * @since 0.0.1
		 */
		private $all_defined_constants = array();

		/**
		 * Initialise class
		 * 
		 * @since 0.0.1
		 */
		public function init() {

			add_action( 'all', array( $this, 'get_raw_data' ) );

			add_action( 'shutdown', array( $this, 'print_raw_data' ) );
		}

	}// class
	
}// if !class_exists()

