<?php

namespace Unyson\Extension;

use Unyson\Extension\Exceptions\NotFound;

class Command extends \WP_CLI_Command {

	private $name;

	public function __construct( $name ) {
		$this->name = $name;
	}

	/**
	 * Install Unyson extension.
	 *
	 * ## OPTIONS
	 *
	 * [--activate]
	 * : Activate extension after installation
	 *
	 * [--force]
	 * : Removes the current installation of th extension and installs the new one
	 *
	 * ## EXAMPLES
	 *
	 *     wp unyson ext backups install
	 *     wp unyson ext backups install --activate
	 *
	 * @when after_wp_load
	 */
	public function install( $_, $options = array() ) {
		$activate = isset( $options['activate'] );
		$this->initialize_fs();

		if ( isset( $options['force'] ) ) {
			$this->uninstall( $_, array( 'force' => 'force' ) );
		}

		$this->manager_response(
			$this
				->ext_manager()
				->install_extensions( array( $this->get_name() => array() ), array( 'activate' => $activate ) ),
			"Extension %c{$this->get_name()}%n successfully installed"
			. ( $activate ? " and activated" : "" )
			. "."
		);
	}

	/**
	 * Uninstall Unyson extension.
	 *
	 * ## OPTIONS
	 *
	 * [--force]
	 * : Force uninstalling even if the extension is active
	 *
	 * ## EXAMPLES
	 *
	 *     wp unyson ext backups uninstall
	 *     wp unyson ext backups uninstall --force
	 *
	 * @when after_wp_load
	 */
	public function uninstall( $_, $options = array() ) {

		if ( $this->is_active() && ! isset( $options['force'] ) ) {
			$this->error( array(
				"Cannot uninstall %c{$this->get_name()}%n, as it is active",
				"%gTips: %n You can run %cwp unyson ext {$this->get_name()} uninstall --force %n "
			) );
		}

		$this->initialize_fs();
		$this->manager_response(
			$this
				->ext_manager()
				->uninstall_extensions( array( $this->get_name() => array() ) ),
			"Extension %c{$this->get_name()}%n successfully uninstalled."
		);
	}

	/**
	 * Activates Unyson extension.
	 *
	 * ## EXAMPLES
	 *
	 *     wp unyson ext backups activate
	 *
	 * @when after_wp_load
	 */
	public function activate( $_, $options = array() ) {
		$this->manager_response(
			$this
				->ext_manager()
				->activate_extensions( array( $this->get_name() => array() ) ),
			"Extension %c{$this->get_name()}%n successfully activated."
		);
	}

	/**
	 * Deactivates Unyson extension.
	 *
	 * ## EXAMPLES
	 *
	 *     wp unyson ext backups deactivate
	 *
	 * @when after_wp_load
	 */
	public function deactivate( $_, $options = array() ) {
		$this->manager_response(
			$this
				->ext_manager()
				->deactivate_extensions( array( $this->get_name() => array() ) ),
			"Extension %c{$this->get_name()}%n successfully deactivated."
		);
	}

	/**
	 * Provides Unyson extension current version.
	 *
	 * ## EXAMPLES
	 *
	 *     wp unyson ext backups version
	 *
	 * @when after_wp_load
	 */
	public function version( $_, $options = array() ) {
		try {
			$this->message( $this->get_ext()->manifest->get_version() );
		} catch ( NotFound $e ) {
			$this->error( array(
				"It seems the extension %c{$this->get_name()}%n is not installed or active",
				"%yTips:%n You can run the %cwp unyson ext {$this->get_name()} install --activate%n"
			) );
		}
	}

	/**
	 * Provides Unyson extension current status, active or inactive.
	 * The inactive status is shown when extension is installed but not active or not even installed
	 *
	 * ## EXAMPLES
	 *
	 *     wp unyson ext backups status
	 *
	 * @when after_wp_load
	 */
	public function status( $_, $options = array() ) {
		if ( $this->is_active() ) {
			$this->message( '%gActive%n' );
		} else {
			$this->message( '%rInactive%n' );
		}
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