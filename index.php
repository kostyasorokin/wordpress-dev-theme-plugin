<?php
/**
 * Plugin Name: Wordpress Dev Theme Plugin
 * Plugin URI: https://konstantinsorokin.com/
 * Author: Konstantin Sorokin
 * Author URI: https://konstantinsorokin.com/
 * Version: 1.1.0
 * License: GPL2+
 * Text Domain: wordpress-dev-theme-plugin
 * Domain Path: /languages/
 *
 * @package wordpress-dev-theme
 * @author Konstantin Sorokin
 * @link https://konstantinsorokin.com
 */

/**
 * Disable File Editor
 */
define( 'DISALLOW_FILE_EDIT', true );

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

/**
 * Disable wptexturize
 */
add_filter( 'run_wptexturize', '__return_false' );

/**
 * Remove jQuery Migrate
 *
 * @param $scripts
 */
function removeJqueryMigrate( $scripts )
{
	if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
		$script = $scripts->registered['jquery'];
		if ( $script->deps ) {
			$script->deps = array_diff( $script->deps, [ 'jquery-migrate' ] );
		}
	}
}

add_action( 'wp_default_scripts', 'removeJqueryMigrate' );

/**
 * Disable emoji's
 */
function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
	add_filter( 'wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2 );
}
add_action( 'init', 'disable_emojis' );

/**
 * Filter function used to remove the tinymce emoji plugin.
 *
 * @param array $plugins
 * @return array Difference betwen the two arrays
 */
function disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 *
 * @param array $urls URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array Difference betwen the two arrays.
 */
function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
	if ( 'dns-prefetch' == $relation_type ) {
		/** This filter is documented in wp-includes/formatting.php */
		$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );

		$urls = array_diff( $urls, array( $emoji_svg_url ) );
	}

	return $urls;
}

/**
 * Disable meta tags in the <head>
 */
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link'); // function of editing content with Windows Live Writer
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0 );

/**
 * Remove Powered by Slider Revolution
 */
function remove_revslider_meta_tag() {
	return '';
}
add_filter( 'revslider_meta_generator', 'remove_revslider_meta_tag' );

/**
 * Contact Form 7 - deregister style.css
 * https://contactform7.com/loading-javascript-and-stylesheet-only-when-it-is-necessary/
 */
add_filter( 'wpcf7_load_css', '__return_false' );

/**
 * Contact Form 7 - disable auto <p>
 */
add_filter( 'wpcf7_autop_or_not', '__return_false' );

/**
 * TranslatePress - disable default CSS
 */
function trp_dequeue_style() {
	wp_dequeue_style( 'trp-language-switcher-style' );
}
add_action( 'wp_enqueue_scripts', 'trp_dequeue_style', 9000 );

/**
 * Remove .recentcomments on wp_head
 */
function remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action('wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
}
add_action('widgets_init', 'remove_recent_comments_style');

/**
 * Hide A Particular Admin Account From Wordpress User List
 * https://perishablepress.com/stop-user-enumeration-wordpress/
 */
if (!is_admin()) {
	// default URL format
	if (preg_match('/author=([0-9]*)/i', $_SERVER['QUERY_STRING'])) die();
	add_filter('redirect_canonical', 'shapeSpace_check_enum', 10, 2);
}

function shapeSpace_check_enum($redirect, $request) {
	// permalink URL format
	if (preg_match('/\?author=([0-9]*)(\/*)/i', $request)) die();
	else return $redirect;
}

/**
 * Enable SVG support
 *
 * @param $type
 *
 * @return mixed
 */
function mimeTypes( $type )
{
	$type['svg'] = 'image/svg+xml';

	return $type;
}
add_filter('upload_mimes', 'mimeTypes');