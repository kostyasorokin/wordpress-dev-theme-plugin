<?php
/**
 * Plugin Name: Wordpress Dev Theme Plugin
 * Plugin URI: https://konstantinsorokin.com/
 * Author: Konstantin Sorokin
 * Author URI: https://konstantinsorokin.com/
 * Version: 1.0.0
 * License: GPL2+
 * Text Domain: wordpress-dev-theme-plugin
 * Domain Path: /languages/
 *
 * @package wordpress-dev-theme
 * @author Konstantin Sorokin
 * @link https://konstantinsorokin.com
 */

/**
 * Disable Post Revisions
 */
define( 'WP_POST_REVISIONS', false, 0 );

/**
 * Autosave Interval
 */
define( 'AUTOSAVE_INTERVAL', 99999 );

/**
 * Disable Autosave
 */
function disable_autosave() {
	wp_deregister_script('autosave');
}
add_action( 'wp_print_scripts', 'disable_autosave' );