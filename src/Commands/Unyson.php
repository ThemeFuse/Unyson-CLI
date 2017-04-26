<?php

namespace Unyson\Commands;

use Unyson\Exceptions\Exception;
use Unyson\Exceptions\InvalidRequest;
use Unyson\Exceptions\NotInstalled;
use Unyson\Utils\Plugin;

/**
 * Manage Unyson plugin.
 *
 * @package wp-cli
 */
class Unyson extends \WP_CLI_Command {
	protected function plugin_command() {
		return new \Plugin_Command();
	}

	/**
	 * Prints Unyson current version.
	 *
	 * ## EXAMPLES
	 *
	 *  # Print current version
	 *  $wp unyson version
	 *  2.6.15
	 *
	 * @when after_wp_load
	 */
	public function version() {
		$this->get( null, array( 'field' => 'version' ) );
	}

	/**
	 * Activate unyson.
	 *
	 * ## OPTIONS
	 *
	 * [--network]
	 * : If set, unyson will be activated for the entire multisite network.
	 *
	 * ## EXAMPLES
	 *
	 *     # Activate unyson
	 *     $ wp unyson activate
	 *     Plugin 'unyson' activated.
	 *     Success: Activated 1 of 1 plugins.
	 *
	 *     # Activate plugin in entire multisite network
	 *     $ wp unyson activate --network
	 *     Plugin 'unyson' network activated.
	 *     Success: Network activated 1 of 1 plugins.
	 */
	public function activate( $_ = array(), $assoc_args = array() ) {
		if ( isset( $assoc_args['all'] ) ) {
			unset( $assoc_args['all'] );
		}
		$this->plugin_command()->activate( array( 'unyson' ), $assoc_args );
	}

	/**
	 * Deactivate unyson.
	 *
	 * ## OPTIONS
	 *
	 * [--uninstall]
	 * : Uninstall unyson after deactivation.
	 *
	 * [--network]
	 * : If set, unyson will be deactivated for the entire multisite network.
	 *
	 * ## EXAMPLES
	 *
	 *     # Deactivate unyson
	 *     $ wp unyson deactivate
	 *     Plugin 'unyson' deactivated.
	 *     Success: Deactivated 1 of 1 plugins.
	 */
	public function deactivate( $_ = array(), $assoc_args = array() ) {
		if ( isset( $assoc_args['all'] ) ) {
			unset( $assoc_args['all'] );
		}
		$this->plugin_command()->deactivate( array( 'unyson' ), $assoc_args );
	}

	/**
	 * Install unyson.
	 *
	 * ## OPTIONS
	 *
	 * [--version=<version>]
	 * : If set, get that particular version from wordpress.org, instead of the
	 * stable version.
	 *
	 * [--force]
	 * : If set, the command will overwrite any installed version of unyson, without prompting
	 * for confirmation.
	 *
	 * [--activate]
	 * : If set, unyson will be activated immediately after install.
	 *
	 * [--activate-network]
	 * : If set, unyson will be network activated immediately after install
	 *
	 * ## EXAMPLES
	 *
	 *     # Install the latest version from wordpress.org and activate
	 *     $ wp unyson install --activate
	 *     Installing unyson (2.6.16)
	 *     Downloading install package from https://downloads.wordpress.org/plugin/unyson.2.6.16.zip...
	 *     Using cached file '/home/vagrant/.wp-cli/cache/plugin/unyson-2.6.16.zip'...
	 *     Unpacking the package...
	 *     Installing the plugin...
	 *     Plugin installed successfully.
	 *     Activating 'unyson'...
	 *     Plugin 'unyson' activated.
	 *     Success: Installed 1 of 1 plugins.
	 */
	public function install( $_ = array(), $assoc_args = array() ) {
		$plugin_command = new \Plugin_Command();

		$plugin_command->install( array( 'unyson' ), $assoc_args );
	}

	/**
	 * Uninstall Unyson.
	 *
	 * [--deactivate]
	 * : Deactivate unyson before uninstalling. Default behavior is to warn and skip if unyson is active.
	 *
	 * [--skip-delete]
	 * : If set, the unyson files will not be deleted. Only the uninstall procedure
	 * will be run.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp unyson uninstall
	 *     Uninstalled and deleted 'unyson' plugin.
	 *     Success: Uninstalled 1 of 1 plugins.
	 */
	public function uninstall( $_ = array(), $assoc_args = array() ) {
		$this->plugin_command()->uninstall( array( 'unyson' ), $assoc_args );
	}

	/**
	 * Get details about current unyson installation.
	 *
	 * ## OPTIONS
	 *
	 * [--field=<field>]
	 * : Instead of returning the whole unyson data, returns the value of a single field.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific fields. Defaults to all fields.
	 *
	 * [--format=<format>]
	 * : Render output in a particular format.
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - csv
	 *   - json
	 *   - yaml
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp unyson get --format=json
	 *     {"name":"unyson","title":"Unyson","author":"ThemeFuse","version":"2.6.15","description":"A free drag & drop framework that comes with a bunch of built in extensions that will help you develop premium themes fast & easy.","status":"active"}
	 */
	public function get( $_ = array(), $assoc_args = array() ) {
		$this->plugin_command()->get( array( 'unyson' ), $assoc_args );
	}

	/**
	 * Check if unyson is installed.
	 *
	 * Returns exit code 0 when installed, 1 when uninstalled.
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 *     # Check whether unyson is installed; exit status 0 if installed, otherwise 1
	 *     $ wp unyson is-installed
	 *     $ echo $?
	 *     1
	 *
	 * @subcommand is-installed
	 */
	public function is_installed( $_ = array(), $assoc_args = array() ) {
		$this->plugin_command()->is_installed( array( 'unyson' ), $assoc_args );
	}

	/**
	 * Get the path to unyson or to the unyson directory.
	 *
	 * ## OPTIONS
	 *
	 * [--dir]
	 * : If set, get the path to the closest parent directory, instead of the
	 * unyson file.
	 *
	 * ## EXAMPLES
	 *
	 *     # Get Unyson plugin path
	 *     $ wp unyson path
	 *     /var/www/wordpress/wp-content/plugins/unyson/unyson.php
	 *
	 *     # Get Unyson plugin directory
	 *     $ wp unyson path --dir
	 *     /var/www/wordpress/wp-content/plugins/unyson
	 */
	public function path( $_ = array(), $assoc_args = array() ) {
		$this->plugin_command()->path( array( 'unyson' ), $assoc_args );
	}

	/**
	 * See unyson status.
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 *     # Displays unyson status
	 *     $ wp unyson status
	 *     Plugin unyson details:
	 *         Name: Unyson
	 *         Status: Active
	 *         Version: 2.6.16
	 *         Author: ThemeFuse
	 *         Description: A free drag & drop framework that comes with a bunch of built in extensions that will help you develop premium themes fast & easy.
	 */
	public function status() {
		$this->plugin_command()->status( array( 'unyson' ) );
	}

	/**
	 * Toggle a unyson's activation state.
	 *
	 * If unyson is active, then it will be deactivated. If unyson is
	 * inactive, then it will be activated.
	 *
	 * ## OPTIONS
	 *
	 * [--network]
	 * : If set, unyson will be toggled for the entire multisite network.
	 *
	 * ## EXAMPLES
	 *
	 *     # Unyson is currently activated
	 *     $ wp unyson toggle
	 *     Plugin 'unyson' deactivated.
	 *     Success: Toggled 1 of 1 plugins.
	 *
	 *     # Unyson is currently deactivated
	 *     $ wp plugin toggle unyson
	 *     Plugin 'unyson' activated.
	 *     Success: Toggled 1 of 1 plugins.
	 */
	public function toggle( $_ = array(), $assoc_args = array() ) {
		$this->plugin_command()->toggle( array( 'unyson' ), $assoc_args );
	}

	/**
	 * Update unyson.
	 *
	 * ## OPTIONS
	 *
	 * [--format=<format>]
	 * : Output summary as table or summary. Defaults to table.
	 *
	 * [--version=<version>]
	 * : If set, unyson will be updated to the specified version.
	 *
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp unyson update --version=2.5.4
	 *     Installing unyson
	 *     Downloading install package from https://downloads.wordpress.org/plugin/unyson-2.5.4.zip...
	 *     Unpacking the package...
	 *     Installing the plugin...
	 *     Removing the old version of the plugin...
	 *     Plugin updated successfully.
	 *     Success: Updated 1 of 1 plugins.
	 */
	public function update( $_ = array(), $assoc_args = array() ) {
		if ( isset( $assoc_args['all'] ) ) {
			unset( $assoc_args['all'] );
		}
		if ( isset( $assoc_args['dry-run'] ) ) {
			unset( $assoc_args['dry-run'] );
		}
		$this->plugin_command()->update( array( 'unyson' ), $assoc_args );
	}

	/**
	 * Upgrades unyson to the next available version.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp unyson upgrade
	 *     Installing unyson
	 *     Downloading install package from https://downloads.wordpress.org/plugin/unyson-2.5.5.zip...
	 *     Unpacking the package...
	 *     Installing the plugin...
	 *     Removing the old version of the plugin...
	 *     Plugin updated successfully.
	 *     Success: Updated 1 of 1 plugins.
	 */
	public function upgrade() {
		try {
			$versions = Plugin::get_versions();
			$current  = Plugin::get_version();
		} catch ( InvalidRequest $e ) {
			\WP_CLI::error( "Unable to get 'Plugin' versions." );
			exit;
		} catch ( NotInstalled $e ) {
			\WP_CLI::error( "The 'Plugin' plugin could not be found." );
		}

		$key = array_search( $current, $versions );

		if ( $key == - 1 ) {
			\WP_CLI::error( "Unable to locate 'Plugin' version" );
		}

		if ( $key == count( $versions ) - 1 ) {
			\WP_CLI::line( 'You already have the latest version' );
			\WP_CLI::halt( 1 );
		}

		$this->update( null, array( 'version' => $versions[ $key + 1 ] ) );
	}

	/**
	 * Downgrades unyson to the previous version.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp unyson downgrade
	 *     Installing unyson
	 *     Downloading install package from https://downloads.wordpress.org/plugin/unyson-2.5.3.zip...
	 *     Unpacking the package...
	 *     Installing the plugin...
	 *     Removing the old version of the plugin...
	 *     Plugin updated successfully.
	 *     Success: Updated 1 of 1 plugins.
	 */
	public function downgrade() {
		try {
			$versions = Plugin::get_versions();
			$current  = Plugin::get_version();
		} catch ( InvalidRequest $e ) {
			\WP_CLI::error( "Unable to get 'Plugin' versions." );
		} catch ( NotInstalled $e ) {
			\WP_CLI::error( "The 'Plugin' plugin could not be found." );
		}

		$key = array_search( $current, $versions );

		if ( $key == - 1 ) {
			\WP_CLI::error( "Unable to locate 'Plugin' version" );
			exit;
		}

		if ( $key == 0 ) {
			\WP_CLI::line( 'You already have the first version' );
			\WP_CLI::halt( 1 );
		}

		$this->update( null, array( 'version' => $versions[ $key - 1 ] ) );
	}

	/**
	 * List all unyson available versions.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp unyson versions
	 *          2.0.0
	 *          2.0.1
	 *          2.0.2
	 *          2.1.0
	 *          2.1.1
	 *          2.1.2
	 *          2.1.3
	 *          2.1.4
	 *          2.1.5
	 *          2.1.6
	 *          2.1.7
	 *        * 2.1.8
	 *          2.1.9
	 *          2.1.10
	 *          2.1.11
	 */
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

		\WP_CLI::halt( 1 );
	}
}