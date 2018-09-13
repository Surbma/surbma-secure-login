<?php
/*
Plugin Name: Surbma | Secure Login
Plugin URI: https://surbma.com/wordpress-plugins/
Description: The most simple two factor authentication plugin for WordPress.

Version: 3.0

Author: Surbma
Author URI: https://surbma.com/

License: GPLv2

Text Domain: surbma-secure-login
Domain Path: /languages/
*/

// Prevent direct access to the plugin
if ( !defined( 'ABSPATH' ) ) exit( 'Good try! :)' );

// Include files
define( 'WPMAILAUTH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

include ( WPMAILAUTH_PLUGIN_DIR . 'class.auth.php' );
include ( WPMAILAUTH_PLUGIN_DIR . 'class.admin.php' );