<?php

/**
 * Plugin Name: WP Auto Linker
 * Plugin URI:  http://wordpress.org/plugins/wp-auto-linker/
 * Description: Automatically link keywords to categories, tags, pages, users, and more
 * Author:      John James Jacoby
 * Author URI:  http://jjj.me
 * Version:     0.1.1
 * Text Domain: autolinker
 * Domain Path: /languages/
 * License:     GPLv2 or later (license.txt)
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Include the autolinker library
function wp_auto_linker() {
	
	// Get the plugin path
	$plugin_path = plugin_dir_path( __FILE__ );

	// Classes
	include $plugin_path . 'includes/class-auto-link.php';
	include $plugin_path . 'includes/class-auto-linker.php';
	include $plugin_path . 'includes/class-wp-auto-linker.php';
}
add_action( 'plugins_loaded', 'wp_auto_linker' );

/**
 * Setup the WordPress Autolinker
 *
 * @since Autolinker (0.1.1)
 *
 * @author johnjamesjacoby
 */
function wp_auto_linker_setup_default_links() {

	// Instantiate the linker
	$linker = new WP_Auto_Linker();

	// Author Archives
	$linker->add_linker( array(
		'name'   => esc_html__( 'Author Archives', 'wp-auto-linker' ),
		'char'   => '@',
		'output' => array(
			'filter_single' => array( $linker, 'single_user_link' )
		),
		'input'  => false
	) );

	// Post Tags
	$linker->add_linker( array(
		'name'   => esc_html__( 'Post Tags', 'wp-auto-linker' ),
		'char'   => '#',
		'output' => array(
			'filter_single' => array( $linker, 'single_post_tag_link' )
		),
		'input' => array(
			'filter_all'      => array( $linker, 'save_all_post_tags'      ),
			'filter_no_match' => array( $linker, 'save_no_match_post_tags' )
		)
	) );

	// Categories
	$linker->add_linker( array(
		'name'   => esc_html__( 'Categories', 'wp-auto-linker' ),
		'char'   => '$',
		'output' => array(
			'filter_single' => array( $linker, 'single_category_link' )
		),
		'input' => array(
			'filter_all'      => array( $linker, 'save_all_categories'      ),
			'filter_no_match' => array( $linker, 'save_no_match_categories' )
		)
	) );

	// Pages
	$linker->add_linker( array(
		'name'   => esc_html__( 'Pages', 'wp-auto-linker' ),
		'char'   => '^',
		'output' => array(
			'filter_single' => array( $linker, 'single_page' )
		),
		'input' => false
	) );
}
add_action( 'plugins_loaded', 'wp_auto_linker_setup_default_links', 11 );
