<?php

namespace Unyson\Utils;


use Unyson\Exceptions\NotInstalled;

class Plugin {

	public static function get_file() {
		return 'unyson/unyson.php';
	}

	public static function get_version() {
		$plugin_data = static::get();

		return $plugin_data['Version'];
	}

	public static function get_versions() {
		return Repo::get_versions( 'unyson' );
	}

	public static function is_installed() {
		$plugins = get_plugins();

		return isset( $plugins[ static::get_file() ] );
	}

	public static function is_active() {
		return defined( 'FW' );
	}

	public static function get() {
		if ( self::is_installed() ) {
			return get_plugin_data( WP_PLUGIN_DIR . '/' . static::get_file(), false, false );
		}

		throw new NotInstalled( 'Plugin is not installed' );
	}
}