<?php
/**
 * Plugin Name: CalcCGD
 * Description: Configuratore/calcolatore multi-step CasaGreenDavvero.
 * Version: 1
 * Text Domain: calccgd
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Plugin constants.
define( 'CGD_SLUG', 'calccgd' );
define( 'CGD_VERSION', '1' );
define( 'CGD_PATH', plugin_dir_path( __FILE__ ) );
define( 'CGD_URL', plugin_dir_url( __FILE__ ) );
// Formidable Forms form ID handled by this plugin.
define( 'CGD_FORM_ID', 0 );

// Simple PSR-4 style autoloader for plugin classes.
spl_autoload_register(
    function ( $class ) {
        $prefix = 'CGD\\Calc\\';
        if ( strpos( $class, $prefix ) !== 0 ) {
            return;
        }
        $relative = strtolower( str_replace( array( $prefix, '\\' ), array( 'cgd-', '-' ), $class ) );
        $file     = CGD_PATH . 'includes/class-' . $relative . '.php';
        if ( is_readable( $file ) ) {
            require_once $file;
        }
    }
);

register_activation_hook( __FILE__, 'cgd_activate' );
register_deactivation_hook( __FILE__, 'cgd_deactivate' );

/**
 * Run on plugin activation.
 */
function cgd_activate() {
    // Placeholder for future activation logic.
}

/**
 * Run on plugin deactivation.
 */
function cgd_deactivate() {
    // Placeholder for future deactivation logic.
}

add_action(
    'plugins_loaded',
    function () {
        $plugin = new CGD\Calc\Plugin();
        $plugin->init();
    }
);

