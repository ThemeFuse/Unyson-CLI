<?php

namespace Unyson\Commands;


use Unyson\Exceptions\Exception;
use Unyson\Exceptions\InvalidRequest;
use Unyson\Exceptions\NotInstalled;
use Unyson\Utils\Plugin;

class Core extends \WP_CLI_Command {

	protected function plugin_command() {
		return new \Plugin_Command();
	}

	/**
	 * Prints Plugin current version.
	 *
	 * ## EXAMPLES
	 *
	 *  wp unyson version
	 *
	 * @when after_wp_load
	 */
	public function version() {
		$this->get( null, array( 'field' => 'version' ) );
	}

	public function activate( $_, $assoc_args = array() ) {
		if ( isset( $assoc_args['all'] ) ) {
			unset( $assoc_args['all'] );
		}
		$this->plugin_command()->activate( array( 'unyson' ), $assoc_args );
	}

	public function deactivate( $_, $assoc_args = array() ) {
		if ( isset( $assoc_args['all'] ) ) {
			unset( $assoc_args['all'] );
		}
		$this->plugin_command()->deactivate( array( 'unyson' ), $assoc_args );
	}

	public function install( $_, $assoc_args = array() ) {
		$plugin_command = new \Plugin_Command();

		$plugin_command->install( array( 'unyson' ), $assoc_args );
	}

	public function uninstall( $_, $assoc_args = array() ) {
		$this->plugin_command()->uninstall( array( 'unyson' ), $assoc_args );
	}

	public function get( $_, $assoc_args = array() ) {
		$this->plugin_command()->get( array( 'unyson' ), $assoc_args );
	}

	public function is_installed( $_, $assoc_args = array() ) {
		$this->plugin_command()->is_installed( array( 'unyson' ), $assoc_args );
	}

	public function path( $_, $assoc_args = array() ) {
		$this->plugin_command()->path( array( 'unyson' ), $assoc_args );
	}

	public function status() {
		$this->plugin_command()->status( array( 'unyson' ) );
	}

	public function toggle( $_, $assoc_args = array() ) {
		$this->plugin_command()->toggle( array( 'unyson' ), $assoc_args );
	}

	public function update( $_, $assoc_args = array() ) {
		if ( isset( $assoc_args['all'] ) ) {
			unset( $assoc_args['all'] );
		}
		$this->plugin_command()->update( array( 'unyson' ), $assoc_args );
	}

	public function upgrade() {
		try {
			$versions = Plugin::get_versions();
			$current  = Plugin::get_version();
		} catch ( InvalidRequest $e ) {
			\WP_CLI::error( "Unable to get 'Plugin' versions." );
			exit;
		} catch ( NotInstalled $e ) {
			\WP_CLI::error( "The 'Plugin' plugin could not be found." );
			exit;
		}

		$key = array_search( $current, $versions );

		if ( $key == - 1 ) {
			\WP_CLI::error( "Unable to locate 'Plugin' version" );
			exit;
		}

		if ( $key == count( $versions ) - 1 ) {
			\WP_CLI::line( 'You already have the latest version' );
			exit;
		}

		$this->update( null, array( 'version' => $versions[ $key + 1 ] ) );
	}

	public function downgrade() {
		try {
			$versions = Plugin::get_versions();
			$current  = Plugin::get_version();
		} catch ( InvalidRequest $e ) {
			\WP_CLI::error( "Unable to get 'Plugin' versions." );
			exit;
		} catch ( NotInstalled $e ) {
			\WP_CLI::error( "The 'Plugin' plugin could not be found." );
			exit;
		}

		$key = array_search( $current, $versions );

		if ( $key == - 1 ) {
			\WP_CLI::error( "Unable to locate 'Plugin' version" );
			exit;
		}

		if ( $key == 0 ) {
			\WP_CLI::line( 'You already have the first version' );
			exit;
		}

		$this->update( null, array( 'version' => $versions[ $key - 1 ] ) );
	}

	public function versions() {
		try {
			$current_version = Plugin::get_version();
		} catch ( Exception $e ) {
			$current_version = null;
		}

		array_map( function ( $v ) use ( $current_version ) {
			$prefix = version_compare( $v, $current_version ) == 0
				? ' * '
				: '   ';

			\WP_CLI::line( "$prefix$v" );
		},
			Plugin::get_versions() );
	}
}