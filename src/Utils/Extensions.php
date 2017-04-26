<?php

namespace Unyson\Utils;

class Extensions {
	public static function get_all() {
		return array_keys( fw()->extensions->manager->get_installed_extensions() );
	}

	public static function get_active() {
		return array_keys( fw()->extensions->get_all() );
	}

	public static function get_temp_dir() {
		return fw_fix_path( WP_CONTENT_DIR ) . '/extensions-tmp';
	}
}