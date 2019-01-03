<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://eventchain.io
 * @since             1.0.0
 * @package           Eventchain
 *
 * @wordpress-plugin
 * Plugin Name:       EventChain SmartTickets
 * Plugin URI:        http://eventchain.io/EventChain_SmartTickets/
 * Description:       Sell your event tickets on your WordPress website.
 * Version:           1.0.0
 * Author:            EventChain SmartTicket Services Ltd.
 * Author URI:        http://eventchain.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       eventchain
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('EVENTCHAIN_VERSION', '1.0.0' );

/**
 * Plugin base name
 */
define('EVENTCHAIN_BASE_NAME', plugin_basename(__FILE__) );

/**
 * Plugin base directory
 */
define('EVENTCHAIN_BASE_DIR', plugin_dir_path( __FILE__ ));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-eventchain-activator.php
 */
function activate_eventchain() {
	require_once EVENTCHAIN_BASE_DIR . 'includes/class-eventchain-activator.php';
	Eventchain_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-eventchain-deactivator.php
 */
function deactivate_eventchain() {
	require_once EVENTCHAIN_BASE_DIR . 'includes/class-eventchain-deactivator.php';
	Eventchain_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_eventchain' );
register_deactivation_hook( __FILE__, 'deactivate_eventchain' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require EVENTCHAIN_BASE_DIR . 'includes/class-eventchain.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_eventchain() {

	$plugin = new Eventchain();
	$plugin->run();

}
run_eventchain();
