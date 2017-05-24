<?php

namespace Unyson\Commands;

use Unyson\Exceptions\CommandNotFound;
use Unyson\Extension\Command;
use Unyson\Extension\Exceptions\NotFound;
use Unyson\Utils\Plugin;
use Unyson\Utils\Extensions as Exts;
use \WP_CLI;

class Extensions extends \WP_CLI_Command {
	public function __construct() {
		parent::__construct();

		if ( ! Plugin::is_installed() ) {
			WP_CLI::error( 'Unyson is not installed. Run wp unyson install --activate' );
		}

		if ( ! Plugin::is_active() ) {
			WP_CLI::error( 'Unyson is not active. Run wp unyson activate' );
		}
	}

	/**
	 * List Unyson extensions.
	 *
	 * ## OPTIONS
	 *
	 * [--only=<filter>]
	 * : Filter extensions to be listed only.
	 * ---
	 * default: all
	 * options:
	 *   - all
	 *   - active
	 *   - inactive
	 *   - supported
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp unyson exts list
	 *     wp unyson exts list --active
	 *
	 * @alias list
	 * @when after_wp_load
	 */
	public function _list( $args, $options ) {
		switch ( fw_akg( 'only', $options, 'all' ) ) {
			case 'active' :
				$extensions = Exts::get_active();
				break;
			case 'inactive' :
				$extensions = array_diff( Exts::get_all(), Exts::get_active() );
				break;
			default:
				$extensions = Exts::get_all();
		}

		\WP_CLI\Utils\format_items(
			'table',
			array_map( function ( $i ) {
				return array(
					'Name'   => $i,
					'Status' => \Unyson\Utils\Extension::active( $i ) ? 'Active' : 'Inactive',
				);
			},
				$extensions ),
			array( 'Name', 'Status' )
		);
	}

	/**
	 * Install Unyson extension.
	 *
	 * ## OPTIONS
	 *
	 * [<name>]
	 * : Extension name.
	 *
	 * [--activate]
	 * : Activate extension after installation
	 *
	 * [--supported]
	 * : Install supported extensions by theme
	 *
	 * [--force]
	 * : Removes the current installation of th extension and installs the new one
	 *
	 * ## EXAMPLES
	 *
	 *     wp unyson exts backups install
	 *     wp unyson exts backups install --activate
	 *
	 * @when after_wp_load
	 */
	public function install( $args, $options = array() ) {
		$activate = isset( $options['activate'] );
		$this->initialize_fs();
		$supported  = isset( $options['supported'] );
		$extensions = $supported ? fw()->extensions->manager->get_supported_extensions() : (array) $args;
		$names      = implode( ', ', array_keys( $extensions ) );

		if ( empty( $names ) ) {
			$this->error( "The extension name cannot be empty" );
		}

		if ( isset( $options['force'] ) ) {
			$this->uninstall( $extensions, array( 'force' => 'force' ) );
		}

		$this->manager_response(
			$this
				->ext_manager()
				->install_extensions( $extensions, array( 'activate' => $activate ) ),
			"Extensions %c{$names}%n successfully installed"
			. ( $activate ? " and activated" : "" )
			. "."
		);

		WP_CLI::halt( 1 );
	}

	/**
	 * Uninstall Unyson extension.
	 *
	 * ## OPTIONS
	 *
	 * [<name>]
	 * : Extension name.
	 *
	 * [--force]
	 * : Force uninstalling even if the extension is active
	 *
	 * ## EXAMPLES
	 *
	 *     wp unyson exts backups uninstall
	 *     wp unyson exts backups uninstall --force
	 *
	 * @when after_wp_load
	 */
	public function uninstall( $args, $options = array() ) {

		$name = array_shift( $args );

		if ( empty( $name ) ) {
			$this->error( "The extension name cannot be empty" );
		}

		if ( $this->is_active( $name ) && ! isset( $options['force'] ) ) {
			$this->error( array(
				"Cannot uninstall %c{$name}%n, as it is active",
				"%gTips: %n You can run %cwp unyson ext {$name} uninstall --force %n "
			) );
		}

		$this->initialize_fs();
		$this->manager_response(
			$this
				->ext_manager()
				->uninstall_extensions( array( $name => array() ) ),
			"Extension %c{$name}%n successfully uninstalled."
		);

		WP_CLI::halt( 1 );
	}

	/**
	 * Activates Unyson extension.
	 *
	 * ## Options
	 *
	 * [<name>]
	 * : Extension name.
	 *
	 * ## EXAMPLES
	 *
	 *     wp unyson exts backups activate
	 *
	 * @when after_wp_load
	 */
	public function activate( $args, $options = array() ) {
		$name = array_shift( $args );

		if ( empty( $name ) ) {
			$this->error( "The extension name cannot be empty" );
		}

		$this->manager_response(
			$this
				->ext_manager()
				->activate_extensions( array( $name => array() ) ),
			"Extension %c{$name}%n successfully activated."
		);

		WP_CLI::halt( 1 );
	}

	/**
	 * Deactivates Unyson extension.
	 *
	 * ## Options
	 *
	 * [<name>]
	 * : Extension name.
	 *
	 * ## EXAMPLES
	 *
	 *     wp unyson exts backups deactivate
	 *
	 * @when after_wp_load
	 */
	public function deactivate( $args, $options = array() ) {

		$name = array_shift( $args );

		if ( empty( $name ) ) {
			$this->error( "The extension name cannot be empty" );
		}

		$this->manager_response(
			$this
				->ext_manager()
				->deactivate_extensions( array( $name => array() ) ),
			"Extension %c{$name}%n successfully deactivated."
		);

		WP_CLI::halt( 1 );
	}

	/**
	 * Provides Unyson extension current version.
	 *
	 * ## Options
	 *
	 * <name>
	 * : Extension name.
	 *
	 * ## EXAMPLES
	 *
	 *     wp unyson ext backups version
	 *
	 * @when after_wp_load
	 */
	public function version( $args, $options = array() ) {
		$name = array_shift( $args );

		if ( empty( $name ) ) {
			$this->error( "The extension name cannot be empty" );
		}

		try {
			$this->message( $this->get_ext( $name )->manifest->get_version() );
		} catch ( NotFound $e ) {
			$this->error( array(
				"It seems the extension %c{$name}%n is not installed or active",
				"%yTips:%n You can run the %cwp unyson ext {$name} install --activate%n"
			) );
		}

		WP_CLI::halt( 1 );
	}

	/**
	 * Provides Unyson extension current status, active or inactive.
	 * The inactive status is shown when extension is installed but not active or not even installed
	 *
	 * ## Options
	 *
	 * <name>
	 * : Extension name.
	 *
	 * ## EXAMPLES
	 *
	 *     wp unyson ext backups status
	 *
	 * @when after_wp_load
	 */
	public function status( $args, $options = array() ) {

		$name = array_shift( $args );

		if ( empty( $name ) ) {
			$this->error( "The extension name cannot be empty" );
		}

		if ( $this->is_active( $name ) ) {
			$this->message( '%gActive%n' );
			WP_CLI::halt( 1 );
		} else {
			$this->error( '%rInactive%n' );
		}
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

	protected function get_ext( $name ) {
		$ext = fw_ext( $name );

		if ( $ext ) {
			return $ext;
		}

		throw new NotFound( "Extension {$name} cannot be found" );
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

	protected function is_active( $name ) {
		return \Unyson\Utils\Extension::active( $name );
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
			? array_map( function ( $response ) {
				return $response->get_error_message();
			},
				$response )
			: $response->get_error_message() );
	}
}