<?php
namespace CGD\Calc;

/**
 * Helper for retrieving plugin options.
 */
final class Options {
    /**
     * Option name used to store plugin settings.
     */
    public const OPTION = 'cgd_settings';

    /**
     * Get an option value.
     *
     * @param string $key     Option key.
     * @param mixed  $default Default value if option missing.
     * @return mixed
     */
    public static function get( string $key, $default = null ) {
        $options = get_option( self::OPTION, array() );
        return $options[ $key ] ?? $default;
    }

    /**
     * Get configured Formidable form ID.
     *
     * @return int
     */
    public static function get_form_id(): int {
        $form_id = self::get( 'form_id', defined( 'CGD_FORM_ID' ) ? CGD_FORM_ID : 0 );
        return absint( $form_id );
    }
}
