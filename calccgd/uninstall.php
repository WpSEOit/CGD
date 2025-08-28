<?php
/**
 * Uninstall routines for CalcCGD.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Remove plugin options.
delete_option( 'cgd_settings' );

// Note: Entry meta created by this plugin should be removed when the corresponding
// Formidable entries are deleted. Additional cleanup logic can be added here.

