<?php
/**
 * Plugin Name: Lucky Wheel Exit Intent Pop Up, Upsell Pop Up – Rafflys
 * Description: Embed your Wheel of Fortune from Rafflys directly on your WordPress site as an exit-intent popup or slider.
 * Version: 1.0
 * Author: Rafflys.com
 * Text Domain: rafflys
 * Domain Path: /languages
 */

// Make sure we don't expose any info if called directly
if ( ! defined( 'ABSPATH' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define( 'RAFFLYS_VERSION', '0.8' );
define( 'RAFFLYS__MINIMUM_WP_VERSION', '3.1' );
define( 'RAFFLYS__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'RAFFLYS__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'RAFFLYS__PLUGIN_BASENAME', plugin_basename(__FILE__) );
define( 'RAFFLYS_APP_HOST', 'app-sorteos.com' );
define( 'RAFFLYS_APP_URL', 'https://'. RAFFLYS_APP_HOST );
define( 'RAFFLYS_API_URL', 'https://'. RAFFLYS_APP_HOST . '/api/v2' );

$user_lang = strtolower(substr(get_user_locale(), 0, 2));

if ($user_lang !== 'es' && $user_lang !== 'en') {
	$user_lang = 'en';
}

define( 'RAFFLYS_USER_LANG', $user_lang);

require_once( RAFFLYS__PLUGIN_DIR . 'class.rafflys.php' );

add_action( 'init', array( 'Rafflys', 'init' ) );
add_action( 'init', array( 'Rafflys', 'load_translations'));
add_action( 'plugin_locale', array( 'Rafflys', 'locale_filter'), 10, 2);

if ( is_admin() ) {
    require_once( RAFFLYS__PLUGIN_DIR . 'class.rafflys-admin.php' );
    require_once( RAFFLYS__PLUGIN_DIR . 'class.rafflys-api.php' );
    add_action( 'init', array( 'Rafflys_Admin', 'init' ) );
}