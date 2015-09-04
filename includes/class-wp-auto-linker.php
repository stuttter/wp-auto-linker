<?php

/**
 * Link WordPress object to post content
 *
 * @package Autolinker/Includes/Classes/WordPress
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * The main WordPress Autolinker
 */
class WP_Auto_Linker extends Autolinker {

	/**
	 * The main WordPress Autolinker class
	 *
	 * @since 0.1.0
	 */
	public function __construct( $linkers = array() ) {

		// Hook into WordPress's actions
		add_filter( 'the_content', array( $this, 'the_content' ) );
		add_filter( 'save_post',   array( $this, 'save_post'   ) );

		// Call the parent
		parent::__contruct( $linkers );
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
		parent::input( $post->post_content, $post );
	}

	/** Input Functions *******************************************************/

	/**
	 * Save all tags in a post
	 *
	 * @param  array   $tags     Array of tags
	 * @param  string  $content  Not used
	 * @param  object  $post     The post being saved
	 */
	protected function save_all_post_tags( $tags = '', $content = '', $post = false ) {
		$this->set_object_terms( get_post( $post ), $tags, 'post_tag' );
	}

	/**
	 * Remove all tags from a post
	 *
	 * @param  string  $content  Not used
	 * @param  object  $post     The post being saved
	 */
	protected function save_no_match_post_tags( $content = '', $post = false ) {
		$this->set_object_terms( get_post( $post ), '', 'post_tag' );
	}

	/**
	 * Save all categories in a post
	 *
	 * @param  array   $categories  Array of categories
	 * @param  string  $content     Not used
	 * @param  object  $post        The post being saved
	 */
	protected function save_all_categories( $categories = '', $content = '', $post = false ) {
		$this->set_object_terms( get_post( $post ), $categories, 'category' );
	}

	/**
	 * No categories in this post
	 *
	 * @param  string  $content Not used
	 * @param  object  $post    The post being saved
	 */
	protected function save_no_match_categories( $content = '', $post = false ) {

		// Get the post
		$post = get_post( $post );

		// Bail if category is already set
		if ( get_the_category( $post ) ) {
			return;
		}

		// Get the default category
		$default_category = get_term( get_option( 'default_category', 'Uncategorized' ), 'category' )->slug;

		// Set object terms
		$this->set_object_terms( $post, $default_category, 'category' );
	}

	/**
	 * Set terms for an object
	 *
	 * @param  object  $object
	 * @param  array   $terms
	 * @param  string  $taxonomy
	 */
	private function set_object_terms( $object, $terms = '', $taxonomy = '' ) {
		if ( is_object_in_taxonomy( $object, $taxonomy ) ) {
			wp_set_object_terms( $object->ID, $terms, $taxonomy, false );
		}
	}

	/** Output Functions ******************************************************/

	/**
	 * Get a single `post_tag` taxonomy link
	 *
	 * @param   string  $match
	 *
	 * @return  mixed
	 */
	protected function single_post_tag_link( $match = '' ) {
		return $this->get_term_link( $match, 'post_tag' );
	}

	/**
	 * Get a single `category` taxonomy link
	 *
	 * @param   string  $match
	 *
	 * @return  mixed
	 */
	protected function single_category_link( $match = '' ) {
		return $this->get_term_link( $match, 'category' );
	}

	/**
	 * Get a single term by it's slug & taxonomy
	 *
	 * @param   string  $match
	 * @param   string  $taxonomy
	 *
	 * @return  mixed
	 */
	protected function get_term_link( $match = '', $taxonomy = '' ) {
		$object = get_term_by( 'slug', $match, $taxonomy );

		return ( ! empty( $object ) )
			? get_term_link( $object, $taxonomy )
			: false;
	}

	/**
	 * Get a single user by it's slug
	 *
	 * @param   string $match
	 *
	 * @return  mixed
	 */
	protected function single_user_link( $match = '' ) {
		$object = get_user_by( 'slug', $match );

		return ( ! empty( $object->ID ) )
			? get_author_posts_url( $object->ID )
			: false;
	}

	/**
	 * Get a single page by it's path
	 *
	 * @param   string $match
	 *
	 * @return  mixed
	 */
	protected function single_page( $match = '' ) {
		$object = get_page_by_path( $match );

		return ( ! empty( $object->ID ) )
			? get_the_permalink( $object->ID )
			: false;
	}
}
