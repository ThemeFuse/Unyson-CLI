<?php
if ( defined( 'WP_CLI' ) && class_exists( 'WP_CLI', false ) ) {
	WP_CLI::add_command( 'unyson core', 'Unyson\Commands\Core' );
}