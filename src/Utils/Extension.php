<?php

namespace Unyson\Utils;

class Extension {
	/**
	 * @param $name
	 *
	 * @return bool
	 */
	public static function installed( $name ) {
		return (bool) in_array( $name, Extensions::get_all() );
	}

	public static function active( $name ) {
		return (bool) in_array( $name, Extensions::get_active() );
	}
}