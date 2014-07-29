<?php

/**
 * The Autolinker
 *
 * This class is platform agnostic, and designed to allow for easy connecting
 * of different types of data objects to different character prefixes. A few
 * common usages are:
 *
 * - @ - User accounts
 * - # - Tags
 * - $ - Categories
 * - ^ - Pages
 * - ! - FAQ pages
 *
 * @since Autolinker (0.1.0)
 */
class Autolinker {

	/**
	 * Array of automatic linkers, eventually iterated on when inputting and
	 * outputting some blog of text.
	 *
	 * @var array
	 */
	public $linkers = array();

	/**
	 * Construct the main Autolinker foundation
	 */
	public function __contruct( $linkers = array() ) {
		$this->setup_linkers( $linkers );
	}

	protected function setup_linkers( $linkers = array() ) {

		// Bail if no linkers passed
		if ( empty( $linkers ) ) {
			return;
		}

		// Loop through and setup linkers
		foreach ( (array) $linkers as $linker ) {
			$this->linkers[ $linker['char'] ] = new Autolink(
				$linker['name'],
				$linker['char'],
				$linker['input'],
				$linker['output']
			);
		}
	}

	protected function input( $content = '', $object = false ) {

		// Get matches and bail if none exist
		$matches = $this->find_matches( $content );
		if ( empty( $matches ) ) {
			if ( ! empty( $this->linkers ) ) {
				foreach ( array_values( $this->linkers ) as $linker ) {
					if ( ! empty( $linker->input['filter_no_match'] ) && is_callable( $linker->input['filter_no_match'] ) ) {
						call_user_func( $linker->input['filter_no_match'], $content, $object );
					}
				}
			}

			return;
		}

		// Get rules and setup the sorted matches array
		$sorted = array();

		// Loop through matches and sort strings into arrays keyed by hash
		foreach ( (array) $matches as $match ) {

			// Get the matched hash
			$char   = substr( $match, 0, 1 );
			$string = substr( $match, 1, strlen( $match ) - 1 );

			// Add the string to the sorted hash array
			if ( ! empty( $this->linkers[ $char ]->input['filter_all'] ) && is_callable( $this->linkers[ $char ]->input['filter_all'] ) ) {
				$sorted[ $char ][] = $string;
			}

			// Check for matching URL
			if ( ! empty( $this->linkers[ $char ]->input['filter_single'] ) && is_callable( $this->linkers[ $char ]->input['filter_single'] ) ) {
				call_user_func( $this->linkers[ $char ]->input['filter_single'], $match, $content, $object );
			}
		}

		// Loop through sorted hashes and call input function
		foreach ( $sorted as $sorted_hash => $sorted_strings ) {
			call_user_func( $this->linkers[ $sorted_hash ]->input['filter_all'], $sorted_strings, $content, $object );
		}
	}

	public function output( $content = '' ) {

		// Look for matches, and maybe filter if none are found
		$matches = $this->find_matches( $content );
		if ( empty( $matches ) ) {
			if ( ! empty( $this->linkers ) ) {
				foreach ( array_values( $this->linkers ) as $linker ) {
					if ( ! empty( $linker->output['filter_no_match'] ) && is_callable( $linker->output['filter_no_match'] ) ) {
						$content = call_user_func( $linker->output['filter_no_match'], $content );
					}
				}
			}

			// Return content, possibly filtered by no_match callbacks
			return $content;
		}

		// Get rules and setup the sorted matches array
		$sorted = array();

		// Loop through usernames and link to profiles
		foreach ( (array) $matches as $match ) {

			// Get the matched hash
			$char   = substr( $match, 0, 1                    );
			$string = substr( $match, 1, strlen( $match ) - 1 );

			// Add the string to the sorted hash array
			if ( ! empty( $this->linkers[ $char ]->output['filter_all'] ) && is_callable( $this->linkers[ $char ]->output['filter_all'] ) ) {
				$sorted[ $char ][] = $string;
			}

			// Check for matching method
			if ( ! empty( $this->linkers[ $char ]->output['filter_single'] ) && is_callable( $this->linkers[ $char ]->output['filter_single'] ) ) {

				// Check for matching URL
				$url = call_user_func( $this->linkers[ $char ]->output['filter_single'], $string );
				if ( ! empty( $url ) ) {
					$content = str_replace( $match, sprintf( '<a href="%1$s" rel="nofollow">%2$s</a>', $url, $match ), $content );
				}
			}
		}

		// Loop through sorted hashes and call input function
		foreach ( $sorted as $sorted_hash => $sorted_strings ) {
			$content = call_user_func( $this->linkers[ $sorted_hash ]->output['filter_all'], $sorted_strings );
		}

		// Return modified content
		return $content;
	}

	protected function find_matches( $content = '' ) {

		// Bail if no linkers to match
		if ( empty( $this->linkers ) ) {
			return false;
		}

		// Get the linkers to match
		$matches = implode( array_keys( $this->linkers ), ',' );

		// Setup the pattern to match against
		$pattern = "/[{$matches}]+([A-Za-z0-9-_\.{$matches}]+)\b/";

		// Attempt to match our linkers to some text
		preg_match_all( $pattern, $content, $matches );

		// Use the matches that include prefix chars
		return array_filter( $matches[0] );
	}
}
