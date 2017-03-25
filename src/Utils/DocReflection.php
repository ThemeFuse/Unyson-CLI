<?php
namespace Unyson\Utils;

use Unyson\Exceptions\CommandNotFound;
use Unyson\Exceptions\Exception;
use Unyson\Exceptions\MethodNotFound;
use WP_CLI\DocParser;

/**
 * Class DocReflection
 * @package Unyson\Utils
 */
class DocReflection {

	/**
	 * @param $class
	 *
	 * @return array
	 */
	public static function get_methods( $class ) {
		return array_map( function ( \ReflectionMethod $method ) {
			return array(
				'method'  => $method->getName(),
				'command' => DocReflection::get_method_command( $method )
			);
		},
			self::reflection( $class )
			    ->getMethods( \ReflectionMethod::IS_PUBLIC ) );
	}

	/**
	 * @param $class
	 * @param $command
	 *
	 * @return bool
	 */
	public static function has_command( $class, $command ) {
		return (bool) array_filter(
			self::get_methods( $class ),
			function ( $method ) use ( $command ) {
				return $method['command'] == $command;
			}
		);
	}

	/**
	 * @param $class
	 * @param $method
	 *
	 * @return string
	 * @throws Exception
	 */
	public static function get_command( $class, $method ) {
		$ref = self::reflection( $class );

		try {
			$m = $ref->getMethod( $method );
			if ( ! $m->isPublic() ) {
				throw new Exception( "Class $class doesn't have public method $method" );
			}

			return self::get_method_command( $m );
		} catch ( \ReflectionException $e ) {
			throw new Exception( "Class $class doesn't have public method $method" );
		}
	}

	public static function get_method( $class, $command ) {
		$methods = array_filter(
			self::get_methods( $class ),
			function ( $method ) use ( $command ) {
				return $command == $method['command'];
			}
		);

		if ( empty( $methods ) ) {
			throw new MethodNotFound( "The class $class doesn't support the $command command" );
		}

		$method = array_shift( $methods );

		return $method['method'];
	}

	/**
	 * @param $class
	 *
	 * @return \ReflectionClass
	 */
	protected static function reflection( $class ) {
		return new \ReflectionClass( $class );
	}

	/**
	 * @param $docs
	 *
	 * @return DocParser
	 */
	protected static function get_doc_parser( $docs ) {
		return new DocParser( $docs );
	}

	/**
	 * @param \ReflectionMethod $method
	 *
	 * @return string
	 */
	protected static function get_method_command( \ReflectionMethod $method ) {
		$command = self::get_doc_parser( $method->getDocComment() )->get_tag( 'subcommand' );

		return $command == '' ? $method->getName() : $command;
	}
}