<?php

namespace Unyson\Commands;

use Unyson\Exceptions\CommandNotFound;
use Unyson\Extension\Command;
use \WP_CLI;

class Extension extends \WP_CLI_Command {
	public function __invoke( $args, $options ) {
		$name    = array_shift( $args );
		$ext     = $this->get( $name );
		$command = array_shift( $args );

		if ( empty( $name ) ) {
			$this->empty_command( 'ext' );
		}

		if ( empty( $command ) ) {
			$this->empty_command( "ext $name" );
		}

		if ( ! $ext->has_command( $command )
		     &&
		     get_class( $ext ) == 'Unyson\Extension\Base'
		) {
			$this->non_existent_extension( $name );
		}

		try {
			$ext->run_command( $command, $args, $options );
		} catch ( CommandNotFound $e ) {
			$this->non_existent_command( $name, $command );
		}
	}

	/**
	 * @param $name
	 *
	 * @return Command
	 */
	protected function get( $name ) {
		$extension = fw_ext( $name );
		$class     = $this->class_name( $name );

		if (
			$extension
			&&
			fw_include_file_isolated( $extension->get_path( $this->get_commands_path() ), true )
			&&
			class_exists( $class )
			&&
			is_subclass_of( $class, 'Unyson\Extension\Command' )
		) {
			return new $class( $name );
		}

		return new Command( $name );
	}

	protected function class_name( $name ) {
		return '\Unyson\Extension\\'
		       . implode(
			       '',
			       array_map(
				       'ucfirst',
				       array_map(
					       'strtolower',
					       explode( '-', $name )
				       )
			       )
		       )
		       . '\Command';
	}

	protected function get_commands_path() {
		return '/cli/commands.php';
	}

	protected function empty_command( $command ) {
		$this->multi_line_message( array(
			"The %c$command%n command requires the extension name and command",
			'%cCommand:%n wp unyson ext <extension-name> <command> [--<arg-name>]'
		) );
	}

	protected function non_existent_extension( $name ) {
		$this->error_multi_line( array(
			"It seems the extension %c$name%n is not installed or active",
			"%yTips:%n You can run the %cwp unyson ext $name install --activate%n"
		) );
	}

	protected function non_existent_command( $name, $command ) {
		$this->error_multi_line( array( "Invalid %g$command%n command name" ) );
	}

	protected function multi_line_message( array $messages ) {
		return implode( "\n", $messages );
	}

	protected function error_multi_line( array $errors ) {
		WP_CLI::error( WP_CLI::colorize( implode( "\n", $errors ) ) );
	}
}