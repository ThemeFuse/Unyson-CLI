<?php

namespace Unyson\Utils;

class Extensions {
	public static function get_all() {

	}

	public static function get_required() {

	}

	public static function get_active() {

	}

	public static function get_temp_dir() {
		return fw_fix_path(WP_CONTENT_DIR) .'/extensions-tmp';
	}
}