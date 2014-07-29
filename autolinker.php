<?php

/**
 * Plugin Name: Autolinker
 * Plugin URI:  http://wordpress.org/plugins/autolinker/
 * Description: Automatically link keywords to tags, categories, pages, and users
 * Author:      John James Jacoby
 * Author URI:  http://jjj.me
 * Version:     0.1.0
 * Text Domain: autolinker
 * Domain Path: /languages/
 * License:     GPLv2 or later (license.txt)
 */

// Include the autolinker library
include( __DIR__ . '/class-autolink.php'   );
include( __DIR__ . '/class-autolinker.php' );

/**
 * Setup the WordPress Autolinker
 *
 * @since Autolinker (0.1.0)
 *
 * @author johnjamesjacoby
 */
function wp_setup_autolinkers() {
	new WP_Autolinker;
}
add_action( 'plugins_loaded', 'wp_setup_autolinkers' );

/**
 * The main WordPress Autolinker
 */
final class WP_Autolinker extends Autolinker {

	/**
	 * The main WordPress Autolinker class
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		// Hook into WordPress's actions
		add_filter( 'the_content', array( $this, 'the_content' ) );
		add_filter( 'save_post',   array( $this, 'save_post'   ) );

		// Get the default Autolinks
		parent::__contruct( $this->default_autolinks() );
	}

	/**
	 * Get the default array of automatic link relationships
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	private function default_autolinks() {
		return get_site_option( 'autolinkers', array(

			// Author Archives
			array(
				'name'   => __( 'Author Archives', 'autolinker' ),
				'char'   => '@',
				'output' => array(
					'filter_single' => array( $this, 'single_author_link' )
				),
				'input'  => false
			),

			// Post Tags
			array(
				'name'   => __( 'Post Tags', 'autolinker' ),
				'char'   => '#',
				'output' => array(
					'filter_single' => array( $this, 'single_post_tag_link' )
				),
				'input' => array(
					'filter_all'      => array( $this, 'save_all_post_tags'      ),
					'filter_no_match' => array( $this, 'save_no_match_post_tags' )
				)
			),

			// Categories
			array(
				'name'   => __( 'Categories', 'autolinker' ),
				'char'   => '$',
				'output' => array(
					'filter_single' => array( $this, 'single_category_link' )
				),
				'input' => array(
					'filter_all'      => array( $this, 'save_all_categories'      ),
					'filter_no_match' => array( $this, 'save_no_match_categories' )
				)
			),

			// Pages
			array(
				'name'   => __( 'Pages', 'autolinker' ),
				'char'   => '^',
				'output' => array(
					'filter_single' => array( $this, 'single_page' )
				),
				'input' => false
			)
		) );
	}

	/**
	 * Filter post output
	 *
	 * @param string $content The post content
	 *
	 * @return string Post content with automatic links applied
	 */
	public function the_content( $content = '' ) {
		return parent::output( $content );
	}

	/**
	 * 
	 * @param type $post_id
	 */
	public function save_post( $post_id = false ) {

		// Get the post object
		$post = get_post( $post_id );

		// Bail if post is an autosave, revision, or menu item
		if ( wp_is_post_autosave( $post ) || wp_is_post_revision( $post ) || is_nav_menu_item( $post ) ) {
			return;
		}

		// Run autolink callbacks on post content
		parent::input( get_post_field( 'post_content', $post ), $post );
	}

	/** Input Functions *******************************************************/

	protected function save_all_post_tags( $tags = '', $content = '', $post = false ) {
		$this->set_object_terms( get_post( $post ), $tags, 'post_tag' );
	}

	protected function save_no_match_post_tags( $content = '', $post = false ) {
		$this->set_object_terms( get_post( $post ), '', 'post_tag' );
	}

	protected function save_all_categories( $categories = '', $content = '', $post = false ) {
		$this->set_object_terms( get_post( $post ), $categories, 'category' );
	}

	/**
	 * No categories in this post
	 *
	 * @param string $content Not used
	 * @param object $post The post being saved
	 */
	protected function save_no_match_categories( $content = '', $post = false ) {

		// Bail if category is already set
		if ( get_the_category( $post ) ) {
			return;
		}
		$default_category = get_term( get_option( 'default_category', 'Uncategorized' ), 'category' )->slug;
		$this->set_object_terms( get_post( $post ), $default_category, 'category' );
	}

	private function set_object_terms( $object, $terms = '', $taxonomy = '' ) {
		if ( is_object_in_taxonomy( $object, $taxonomy ) ) {
			wp_set_object_terms( $object->ID, $terms, $taxonomy, false );
		}
	}

	/** Output Functions ******************************************************/

	protected function single_post_tag_link( $match = '' ) {
		return $this->get_term_link( $match, 'post_tag' );
	}

	protected function single_category_link( $match = '' ) {
		return $this->get_term_link( $match, 'category' );
	}

	private function get_term_link( $match, $taxonomy ) {
		$object = get_term_by( 'slug', $match, $taxonomy );

		return ( ! empty( $object ) )
			? get_term_link( $object, $taxonomy )
			: false;
	}

	protected function single_author_link( $match = '' ) {
		$object = get_user_by( 'slug', $match );

		return ( ! empty( $object->ID ) )
			? get_author_posts_url( $object->ID )
			: false;
	}

	/**
	 * 
	 * @param string $match
	 * @return boolean
	 */
	protected function single_page( $match = '' ) {
		$object = get_page_by_path( $match );

		return ( ! empty( $object->ID ) )
			? get_the_permalink( $object->ID )
			: false;
	}
}
