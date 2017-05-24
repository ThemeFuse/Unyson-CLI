<?php

namespace Unyson\Extension;

use Unyson\Exceptions\CommandNotFound;
use Unyson\Extension\Exceptions\NotFound;
use Unyson\Utils\DocReflection;

class Command extends \WP_CLI_Command {

	private $name;

	public function __construct( $name ) {
		$this->name = $name;
	}

	/**
	 * @param $command
	 *
	 * @return bool
	 */
	public function has_command( $command ) {
		return DocReflection::has_command( get_class( $this ), $command );
	}

	public function run_command( $command, $args, $options = array() ) {
		if ( ! $this->has_command( $command ) ) {
			throw new CommandNotFound(
				sprintf( "The class %s doesn't have the %s", get_class( $this ), $command )
			);
		}

		call_user_func(
			array( $this, DocReflection::get_method( get_class( $this ), $command ) ),
			$args,
			$options
		);
	}

	protected function get_ext() {
		$ext = fw_ext( $this->get_name() );

		if ( $ext ) {
			return $ext;
		}

		throw new NotFound( "Extension {$this->get_name()} cannot be found" );
	}

	protected function get_name() {
		return $this->name;
	}

	/**
	 * @return \_FW_Extensions_Manager
	 */
	protected function ext_manager() {
		return fw()->extensions->manager;
	}

	protected function error( $message ) {
		\WP_CLI::error( $this->prepare_message( $message ) );
	}

	protected function message( $message ) {
		\WP_CLI::line( $this->prepare_message( $message ) );
	}

	protected function initialize_fs() {

		global $wp_filesystem;

		if ( ! $wp_filesystem ) {
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				fw_include_file_isolated( ABSPATH . 'wp-admin/includes/file.php', true );
			}

			WP_Filesystem();
		}
	}

	protected function is_active() {
		return (bool) fw_akg( $this->get_name(), fw()->extensions->get_all() );
	}

	private function prepare_message( $message ) {
		return is_array( $message )
			? implode( "\n", array_map( array( $this, 'colorize' ), $message ) )
			: $this->colorize( $message );
	}

	private function colorize( $message ) {
		return \WP_CLI::colorize( $message );
	}

	private function manager_response( $response, $success ) {
		if ( $response === true ) {
			$this->message( $success );

			return;
		}

		$this->error( is_array( $response )
			? array_shift( $response )->get_error_message()
			: $response->get_error_message() );
	}
}