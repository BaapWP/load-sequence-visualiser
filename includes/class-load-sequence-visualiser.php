<?php

/**
 * Load sequence visualiser
 * 
 * @author Shantanu Desai <shantanu2846@gmail.com>
 * @since 0.0.1
 */
// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Load_Sequence_Visualiser' ) ) {

	/**
	 * Load sequence visualiser
	 *
	 * @since 0.0.1
	 */
	class Load_Sequence_Visualiser {

		/**
		 * Raw data
		 * 
		 * An array containing a a list of included files, defined constants and 
		 * globals.
		 *
		 * @var array 
		 * 
		 * @since 0.0.1
		 */
		private $raw_data = array(
			'includes'	 => array(),
			'constants'	 => array(),
			'globals'	 => array(),
		);

		/**
		 * Timeline
		 * 
		 * An array containing a list of included files, defined constants and globals
		 * with the name of the filter as the key.
		 * 
		 * @var array 
		 * 
		 * @since 0.0.1
		 */
		private $timeline = array();

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
		
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
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
			
			// Get all the global variables
			$all_globals = $GLOBALS;


			if ( empty( $this->previous_filter ) ) {
				
				// Get the raw data at the very first filter
				$this->get_data_at_first_filter($current_filter, $included_files, $defined_constants, $all_globals);
			}else {
				
				// Get the raw data at all other filters
				$this->get_data_at_remaining_filters($current_filter, $included_files, $defined_constants, $all_globals);
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
		 * @param array $globals List of golbal variables
		 * 
		 * @since 0.0.1
		 */
		public function get_data_at_first_filter( $current_filter, $files, $constants, $globals ) {
			
			$this->timeline[ $current_filter ] =  $this->get_temp_data( $files, $constants, array_keys( $globals ));
			
			// Add the lists to the array that holds historical data			
			$this->add_to_historical_data($files, $constants, $globals);
		}
		
		/**
		 * Get data at remaining filters
		 * 
		 * Get the data after the first filter.
		 * 
		 * @param string $current_filter Name of the current filter
		 * @param array $files List of included files
		 * @param array $constants List of defined constants
		 * @param array $globals List of globals
		 * 
		 * @since 0.0.1
		 */
		public function get_data_at_remaining_filters( $current_filter, $files, $constants, $globals ) {

			// Store all the file names that are present in $files but not in raw data
			$filtered_included_files = array_diff( $files, $this->raw_data[ 'includes' ] );

			// Store all the constanrs that are present in $constants but not in raw data
			$filtered_defined_constants = array_diff_assoc( $constants, $this->raw_data[ 'constants' ] );
			
			// Store all the globals that are present in $globals but not in raw data
			$filtered_globals = array_diff_key( $globals, $this->raw_data[ 'globals' ] );
			
			$temp_array = $this->get_temp_data( $filtered_included_files, $filtered_defined_constants, array_keys( $filtered_globals ));
			
			$this->timeline[ $current_filter ] = $temp_array;
	
			// Add the filtered content to the arrays that hold historical data
			$this->add_to_historical_data($filtered_included_files, $filtered_defined_constants, $filtered_globals);
		}
		
		/**
		 * Get temporary data
		 * 
		 * Get the list of files that have been included and the constants and global 
		 * variables that have been defined and thup to the first filter.
		 * 
		 * @param array $files List of included files
		 * @param array $constants List of defined constants
		 * @param array $globals List of golbal variables
		 * 
		 * @return array Unified list of files, constants and globals
		 * @since 0.0.1
		 */
		public function get_temp_data( $files, $constants, $globals ) {
			
			$temp_array = array();
			array_merge($temp_array, $files);
			array_merge($temp_array, $constants);
			array_merge($temp_array, $globals);
			
			return $temp_array;
		}
		
		/**
		 * Add to historical data
		 * 
		 * Add current data to historical data
		 * 
		 * @param array $files List of included files
		 * @param array $constants List of defined constants
		 * @param array $globals List of golbal variables
		 * 
		 * @since 0.0.1
		 */
		public function add_to_historical_data( $files, $constants, $globals ) {
			
			$this->raw_data[ 'includes' ]	 = array_merge($this->raw_data[ 'includes' ], $files) ;
			$this->raw_data[ 'constants' ] = array_merge($this->raw_data[ 'includes' ], $constants);
			$this->raw_data[ 'globals' ] = array_merge($this->raw_data[ 'globals' ], $globals);
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
			$timeline_data = json_encode( $this->timeline, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_PRETTY_PRINT );
			if ( $timeline_data ) {
?>
								<script type="text/javascript">
								var load_sequence = <?php echo $timeline_data; ?>;
								</script>
				<?php
			}
			else {
				echo "JSON ENCODE FAILED!!!!!!!!!";
				print_r( json_last_error() );
			}
			echo "</pre>";
		}

		/**
		 * Enqueue
		 * 
		 * @since 0.0.1
	 */
		public function enqueue() {
			
			wp_enqueue_script( 'display-json-object', LSV_URL . 'assets/js/display-json.js', array( 'jquery' ) );
		}

	}// class
	
}// if !class_exists()