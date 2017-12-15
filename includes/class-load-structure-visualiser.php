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
		 * Previous filter
		 * 
		 * @var string
		 * 
		 * @since 0.0.1
		 */
		private $previous_filter;

		public function __construct() {
			$this->previous_filter = '';
		}

		/**
		 * Initialise class
		 * 
		 * @since 0.0.1
		 */
		public function init() {

			add_action( 'all', array( $this, 'get_raw_data' ) );

			add_action( 'shutdown', array( $this, 'print_raw_data' ) );
		}

		/**
		 * Get raw data
		 * 
		 * Gets the data for included files and defined constants at each hook.
		 * 
		 * @return void
		 * 
		 * @since 0.0.1
		 */
		public function get_raw_data() {

			// Get current filter
			$current_filter = current_filter();

			// If the $raw_data array already has an entry for that filter, return
			if ( !empty( $this->raw_data[ $current_filter ] ) ) {
				return;
			}

			// Get the files that have been included up to this point
			$included_files = get_included_files();
			
			// Get the constants that have been defined up to this point
			$defined_constants = get_defined_constants();
			
			// Declare an array for filtered included files
			$filtered_included_files	 = array();
			
			// Declare an array for filtered defined constants
			$filtered_defined_constants	 = array();

			if ( empty( $this->previous_filter ) ) {
				$this->raw_data[ $current_filter ][ 'includes' ]	 = $included_files;
				$this->raw_data[ $current_filter ][ 'constants' ] = $defined_constants;
				$this->all_included_files += $included_files;
				$this->all_defined_constants += $defined_constants;
			}
			else {
				$filtered_included_files += array_diff( $included_files, $this->all_included_files );
				$filtered_defined_constants += array_diff_assoc( $defined_constants, $this->all_defined_constants );
				$this->raw_data[ $current_filter ][ 'includes' ]	 = $filtered_included_files;
				$this->raw_data[ $current_filter ][ 'constants' ] = $filtered_defined_constants;
				$this->all_included_files += $filtered_included_files;
				$this->all_defined_constants += $filtered_defined_constants;
			}

			$this->previous_filter = $current_filter;
		}

	}

	// class
}// if !class_exists()

