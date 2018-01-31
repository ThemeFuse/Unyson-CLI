<?php

namespace Unyson\Utils;

use \WP_CLI;

class UpgraderSkin {

	public function __construct() {}

	/**
	 *
	 * @param string|WP_Error $errors
	 */
	public function error( $errors ) {
		\WP_CLI::error( $this->prepare_message( $errors ) );
	}

	/**
	 *
	 * @param string $string
	 */
	public function feedback( $string ) {
		\WP_CLI::line( $this->prepare_message( $string ) );
	}

	private function prepare_message( $message ) {
		return is_array( $message ) ? implode( "\n", array_map( array(
			$this,
			'colorize'
		), $message ) ) : $this->colorize( $message );
	}

	private function colorize( $message ) {
		return \WP_CLI::colorize( $message );
	}

	public function set_result( $result ) {}

	public function header() {}

	public function footer() {}

	public function before() {}

	public function after() {}

	public function decrement_extension_update_count( $type ) {}
}
