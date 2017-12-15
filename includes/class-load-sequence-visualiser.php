<?php

/**
 * Load structure visualiser
 * 
 * @author Shantanu Desai <shantanu2846@gmail.com>
 * @since 0.0.1
 */
// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) exit();

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
		 * An array containing a a list of included files and defined constants.
		 *
		 * @var array 
		 * 
		 * @since 0.0.1
		 */
		private $raw_data = array(
			'includes'	 => array(),
			'constants'	 => array(),
		);

		/**
		 * Timeline
		 * 
		 * An array containing a list of included files and defined constants with 
		 * the name of the filter as the key.
		 * 
		 * @var array 
		 * 
		 * @since 0.0.1
		 */
		private $timeline = array(
			'hook_name' => array(
				'includes'	 => array(),
				'constants'	 => array(),
			)
		);

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

			// If the 'timeline' array already has an entry for that filter, return
			if ( !empty( $this->timeline[ $current_filter ] ) ) {
				return;
			}

			// Get the files that have been included up to this point
			$included_files = get_included_files();
			
			// Get the constants that have been defined up to this point
			$defined_constants = get_defined_constants();

			if ( empty( $this->previous_filter ) ) {
				
				// Get the raw data at the very first filter
				$this->get_data_at_first_filter($current_filter, $included_files, $defined_constants);
			}else {
				
				// Get the raw data at all other filters
				$this->get_data_at_remaining_filters($current_filter, $included_files, $defined_constants);
			}

			// Set the value of previous filter
			$this->previous_filter = $current_filter;
		}
		
		/**
		 * Get data at first filter
		 * 
		 * Get the list of files that have been included and the constants that have been 
		 * defined up to the first filter.
		 * 
		 * @param array $current_filter Name of the current filter
		 * @param array $files List of included files
		 * @param array $constants List of defined constants
		 * 
		 * @since 0.0.1
		 */
		public function get_data_at_first_filter( $current_filter, $files, $constants ) {
			
			// Add the lists to the main array
			$this->timeline[ $current_filter ][ 'includes' ]	 = $files;
			$this->timeline[ $current_filter ][ 'constants' ] = $constants;
			
			// Add the lists to the array that holds historical data
			$this->raw_data[ 'includes' ]	 += $files;
			$this->raw_data[ 'constants' ] += $constants;
		}
		
		/**
		 * Get data at remaining filters
		 * 
		 * Get the data after the first filter.
		 * 
		 * @param string $current_filter Name of the current filter
		 * @param array $files List of included files
		 * @param array $constants List of defined constants
		 * 
		 * @since 0.0.1
		 */
		public function get_data_at_remaining_filters( $current_filter, $files, $constants ) {

			// Declare an array for filtered included files
			$filtered_included_files = array();

			// Declare an array for filtered defined constants
			$filtered_defined_constants = array();

			// Store all the file names that are present in $files but not in all_included_files
			$filtered_included_files += array_diff( $files, $this->raw_data[ 'includes' ] );

			// Store all the constanrs that are present in $constants but not in all_defined_constants
			$filtered_defined_constants += array_diff_assoc( $constants, $this->raw_data[ 'constants' ] );

			//Add the lists to the main array
			$this->timeline[ $current_filter ][ 'includes' ]	 = $filtered_included_files;
			$this->timeline[ $current_filter ][ 'constants' ] = $filtered_defined_constants;

			// Add the filtered content to the arrays that hold historical data
			$this->raw_data[ 'includes' ]	 += $filtered_included_files;
			$this->raw_data[ 'constants' ] += $filtered_defined_constants;
		}
		
		/**
		 * Print raw data
		 * 
		 * Print raw data is hooked into the 'shutdown' action. 
		 * 
		 * @since 0.0.1
		 */
		public function print_raw_data() {

			echo "<pre>";
			print_r( $this->timeline );
			echo "</pre>";
		}

	}// class
	
}// if !class_exists()

