<?php

/**
 * Single object used for storing the attributes for an automatic linker.
 *
 * @since Autolinker (0.1.0)
 */
class Autolink {

	/**
	 * Human readible name, used by any UI objects
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Prefix character used to link to another object
	 *
	 * @var string
	 */
	public $character = '';

	/**
	 * Array of attributes used to intelligently parse input
	 *
	 * @var array
	 */
	public $input = array(
		'filter_all'      => '',
		'filter_single'   => '',
		'filter_no_match' => ''
	);

	/**
	 * Array of attributes used to intelligently parse output
	 *
	 * @var array
	 */
	public $output = array(
		'filter_all'      => '',
		'filter_single'   => '',
		'filter_no_match' => ''
	);

	/**
	 * Assign properties on new
	 *
	 * @param string $name   Human readible name, used by any UI objects
	 * @param string $char   Prefix character used to link to another object
	 * @param array  $input  Array of attributes used to intelligently parse input
	 * @param array  $output Array of attributes used to intelligently parse output
	 */
	public function __construct( $name = '', $char = '', $input = array(), $output = array() ) {
		$this->name      = $name;
		$this->character = $char;
		$this->input     = $input;
		$this->output    = $output;
	}
}
