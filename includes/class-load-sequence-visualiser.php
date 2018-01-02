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

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->previous_filter = '';
		}

		/**
		 * Initialise class
		 * 
		 * @since 0.0.1
		 */
		public function init() {

			/*
			 * The action 'all' is fired before every other action in the Wordpress core.
			 * This allows us to hook into actions and get the name of the filter
			 */
			add_action( 'all', array( $this, 'get_raw_data' ) );

			/*
			 * The action 'shutdown' is the action that is fired in the Wordpress core.
			 * This allows us to print our data right at the end
			 */
			add_action( 'shutdown', array( $this, 'print_raw_data' ) );

			/*
			 * Hook into wp_enqueue_scripts to enqueue the javascript file.
			 */
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		}

		/**
		 * Get raw data
		 * 
		 * Gets the data for included files and defined constants and globals 
		 * at each hook.
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
			$included_files =  array_fill_keys( get_included_files(), 'file' );

			// Get the constants that have been defined up to this point
			$defined_constants = array_fill_keys( array_keys( get_defined_constants() ), 'constant' );

			// Get all the global variables
			$all_globals = array_fill_keys( array_keys( $GLOBALS ), 'global' );

			/*
			 *  Check if 'previous_filter' is empty. If it is empty, then that is the 
			 * first filter to be fired.
			 */
			if ( empty( $this->previous_filter ) ) {

				// Get the raw data at the very first filter
				$this->get_data_at_first_filter( $current_filter, $included_files, $defined_constants, $all_globals );
			}
			else {

				// Get the raw data at all the other filters
				$this->get_data_at_remaining_filters( $current_filter, $included_files, $defined_constants, $all_globals );
			}

			// Set the value of previous filter
			$this->previous_filter = $current_filter;
		}

		/**
		 * Get data at first filter
		 * 
		 * Get the list of files that have been included and the constants and globals 
		 * that have been defined up to the first filter.
		 * 
		 * @param array $current_filter Name of the current filter
		 * @param array $files List of included files
		 * @param array $constants List of defined constants
		 * @param array $globals List of global variables
		 * 
		 * @since 0.0.1
		 */
		public function get_data_at_first_filter( $current_filter, $files, $constants, $globals ) {

			// Filter the constants and globals and save all the values in the main array
			$merged_data = $this->get_temp_data( $files, $constants, $globals );
			$this->timeline = array_merge( $this->timeline, $merged_data);
			$this->timeline[ $current_filter ] = 'filter';

			// Add the lists to the array that holds historical data			
			$this->add_to_historical_data( $files, $constants, $globals );
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
		public function get_data_at_remaining_filters( $current_filter,
												 $files, $constants, $globals ) {

			// Store all the file names that are present in $files but not in raw data
			$filtered_included_files = array_diff( $files, $this->raw_data[ 'includes' ] );

			// Store all the constanrs that are present in $constants but not in raw data
			$filtered_defined_constants = array_diff_assoc( $constants, $this->raw_data[ 'constants' ] );

			// Store all the globals that are present in $globals but not in raw data
			$filtered_globals = array_diff( $globals, $this->raw_data[ 'globals' ] );

			$merged_data = $this->get_temp_data( $filtered_included_files, $filtered_defined_constants, $filtered_globals );

			// Add the filtered content to the main array
			$this->timeline = array_merge( $this->timeline, $merged_data );
			$this->timeline[ $current_filter ] = 'filter';
			
			// Add the filtered content to the arrays that hold historical data
			$this->add_to_historical_data( $filtered_included_files, $filtered_defined_constants, $filtered_globals );
		}

		/**
		 * Get temporary data
		 * 
		 * Merge included files, constants and global variables into one array
		 * 
		 * @param array $files List of included files
		 * @param array $constants List of defined constants
		 * @param array $globals List of global variables
		 * 
		 * @return array Unified list of files, constants and globals
		 * @since 0.0.1
		 */
		public function get_temp_data( $files, $constants,
								 $globals ) {

			// Return a merged array of files, constants and globals
			return array_merge( $files, $constants, $globals );
		}

		/**
		 * Add to historical data
		 * 
		 * Add current data to historical data
		 * 
		 * @param array $files List of included files
		 * @param array $constants List of defined constants
		 * @param array $globals List of global variables
		 * 
		 * @since 0.0.1
		 */
		public function add_to_historical_data( $files,
										  $constants, $globals ) {

			// Filter out the existing 'included_files' and merge the rest with 'included_files' in 'raw_data'
			$this->raw_data[ 'includes' ]	 = array_merge( $this->raw_data[ 'includes' ], $files );
			// Filter out the existing 'constants' and merge the rest with 'included_files' in 'constants'
			$this->raw_data[ 'constants' ]	 = array_merge( $this->raw_data[ 'constants' ], $constants );
			// Filter out the existing 'globals' and merge the rest with 'included_files' in 'globals'
			$this->raw_data[ 'globals' ]	 = array_merge( $this->raw_data[ 'globals' ], $globals );
		}

		/**
		 * Print raw data
		 * 
		 * Print raw data is hooked into the 'shutdown' action. 
		 * 
		 * @since 0.0.1
		 */
		public function print_raw_data() {

			// Check if it is an AJAX request
			if ( defined( 'DOING_AJAX') && DOING_AJAX ) {
				return;
			}
			
			// Print a 'pre' tag
			echo "<pre>";

			// Encode the timline data into JSON format
			$timeline_data = json_encode( $this->timeline, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_PRETTY_PRINT );
			// Check if the encoding was successful, echo the data
			if ( $timeline_data ) {
							?>
								<script type="text/javascript">
								var load_sequence = <?php echo $timeline_data; ?>;
								</script>
							<?php
			}
			// Else, echo the last error
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

			// Enqueue the jQuery script
			wp_enqueue_script( 'display-json-object', LSV_URL . 'assets/js/display-json.js', array( 'jquery' ) );
		}

	}

	// class
}// if !class_exists()