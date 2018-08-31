<?php
/**
 * Plugin Name: Canonical URL for Posts
 * Plugin URI: https://ben.lobaugh.net
 * Description: Creates a metabox with a field that will set the canonical url for the post. Works with all post types.
 * Version: 1.0
 * Author: Ben Lobaugh
 * Author URI: https://ben.lobaugh.net
 */

class Canonical_URL {

	public function hooks() {
		if ( is_admin() ) {
			// Load admin hooks
			add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
			add_action( 'save_post', [ $this, 'save_meta_box' ] );
		}

		if ( ! is_admin() ) {
			// Load only on the front
			add_filter( 'get_canonical_url', [ $this, 'set_head_canonical_url' ], 10, 2 );
			add_filter( 'the_content', [ $this, 'maybe_add_disclaimer' ] );
		}

		// Hooks that load everywhere
	}

	/**
	 * May add the disclaimer to the front of the post
	 *
	 * @since August 31, 2018
	 * @param String $content
	 * @return String
	 */
	public function maybe_add_disclaimer( $content ) {
		if ( ! apply_filters( 'enable_canonical_disclaimer', is_single() ) ) {
			// Disclaimer not enabled on site or is archive page
			return $content;
		}
		global $post;

		$disclaimer = get_post_meta( $post->ID, 'insert_canonical_disclaimer', true );

		if ( empty( $disclaimer ) ) {
			// Disclaimer not desired
			return $content;
		}

		if ( 'true' != $disclaimer ) {
			// Invalid value to show disclaimer
			return $content;
		}

		$url = esc_url_raw( get_post_meta( $post->ID, 'canonical_url', true ) ); 

		$msg = '<p><i>Contents of this article reposted from <a href="' . $url . '">' . $url . '</a></p>';

		$msg = apply_filters( 'canonical_disclaimer', $msg, $url, $content, $post->ID );

		return $msg . $content;
	}

	/**
	 * Set the url to use in the head canonical tag. This should only run
	 * on a single page. Archives and front seem ok so far. If there are
	 * issues we will need to add a conditional to check for single only.
	 *
	 * @since August 31, 2018
	 * @param String $link Original canonical url
	 * @param WP_Post $post
	 * @return string
	*/
	public function set_head_canonical_url( $link, $post ) {
		$url = get_post_meta( $post->ID, 'canonical_url', true );

		if ( empty( $url ) ) {
			// No url to change
			return $link;
		}
		return $url;
	}

	/**
	 * Add the metabox to WordPress metabox callback stack.
	 *
	 * @since August 31, 2018
	 * @param WP_Post $post
	 */
	public function add_meta_box( $post ) {
		add_meta_box(
			'canonical-url-metabox',
			'Canonical URL',
			[ $this, 'render_meta_box' ]
		);	
	}

	/**
	 * Renders the metabox html with the canonical url form.
	 *
	 * @since August 31, 2018
	 * @param WP_Post $post
	 */
	public function render_meta_box( $post ) {
		$url = get_post_meta( $post->ID, 'canonical_url', true );
		$disclaimer = get_post_meta( $post->ID, 'insert_canonical_disclaimer', true );

		echo '<input type="text" name="canonical_url" value="' . esc_attr( $url ) . '" style="width:100%">';
		echo '<p style="font-size:small;">Optional URL that will output a custom canonical url. Useful when reposting content from a different site.</p>';
		echo '<label><input type="checkbox" name="insert_canonical_disclaimer" value="true"' . checked( $disclaimer, 'true', false ) . '> Insert disclamer at beginning of post?</label>';
	}

	/**
	 * Saves the canonical url to the post's meta
	 *
	 * @since August 31, 2018
	 * @param Integer $post_id
	 */
	public function save_meta_box( $post_id ) {
		if ( empty( $post_id ) ) {
			return; // Do not play with fake posts
		}

		if ( empty( $_POST['canonical_url'] ) ) {
			return; // no url to work with
		}

		$url = esc_url_raw( $_POST['canonical_url'] );
		
		update_post_meta( $post_id, 'canonical_url', $url );

		if ( ! empty( $_POST['insert_canonical_disclaimer'] ) ) {
			update_post_meta( $post_id, 'insert_canonical_disclaimer', 'true' );
		} else {
			// Clear disclaimer flag
			delete_post_meta( $post_id, 'insert_canonical_disclaimer' );
		}
	}
} // end class

$canonical_url = new Canonical_URL();
$canonical_url->hooks();
