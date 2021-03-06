<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the
 * plugin admin area. This file also includes all of the dependencies used by
 * the plugin, registers the activation and deactivation functions, and defines
 * a function that starts the plugin.
 *
 * @since             0.0.1
 * @package           load_sequence_visualiser
 *
 * @wordpress-plugin
 * 
 * Plugin Name: Load Sequence Visualiser
 * Description: Helps visualise the load sequence of WordPress hooks, filters, global variables and constants.
 * Version: 0.0.1
 * Author: BaapWP
 * Author URI:  https://github.com/BaapWP
 * Text Domain: load-sequence-visualiser
 * Domain Path: /languages
 * License: GPL2
 */

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) exit();

if ( !defined( 'LSV_PATH' ) ) {
	/**
	 * Path to the plugin directory.
	 *
	 * @since 0.0.1
	 */
	define( 'LSV_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

if ( !defined( 'LSV_URL' ) ) {
	/**
	 * URL to the plugin directory.
	 *
	 * @since 0.0.1
	 */
	define( 'LSV_URL', trailingslashit( plugin_dir_url(  __FILE__ ) ) );
}


/**
 * The core plugin class
 */
require_once LSV_PATH . 'includes/class-load-sequence-visualiser.php';

$lsv = new Load_Sequence_Visualiser();
$lsv->init();
