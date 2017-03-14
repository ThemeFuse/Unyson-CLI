<?php

namespace Unyson\Utils;


use Unyson\Exceptions\InvalidRequest;

class Repo {
	public static function get_repository() {
		return 'http://plugins.svn.wordpress.org';
	}

	public static function plugin_repository( $slug ) {
		return static::get_repository() . '/' . $slug;
	}

	public static function get_tags_repository( $slug ) {
		return static::plugin_repository( $slug ) . '/tags';
	}

	public static function get_versions( $slug ) {
		$response = wp_remote_get( static::get_tags_repository( $slug ) );

		if ( ( $code = wp_remote_retrieve_response_code( $response ) ) !== 200 ) {
			throw new InvalidRequest( sprintf(
				"Request: <i>%s</i> ended with code <strong>$code</strong>",
				static::get_tags_repository( $slug )
			) );
		}

		$DOM = new \DOMDocument();
		$DOM->loadHTML( wp_remote_retrieve_body( $response ) );
		$versions = array();

		foreach ( $DOM->getElementsByTagName( 'a' ) as $item ) {
			$versions[] = str_replace( '/', '', $item->getAttribute( 'href' ) );
		}

		array_shift( $versions );
		array_pop( $versions );
		natsort( $versions );

		return $versions;
	}
}