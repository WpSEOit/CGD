<?php
namespace CGD\Calc;

/**
 * Admin utilities and public asset loading.
 */
final class Admin {
    /**
     * Initialize hooks.
     */
    public function init(): void {
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_menu', array( $this, 'add_options_page' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public' ) );
    }

    /**
     * Register plugin settings.
     */
    public function register_settings(): void {
        register_setting(
            'cgd_group',
            'cgd_settings',
            array(
                'type'              => 'array',
                'sanitize_callback' => array( $this, 'sanitize_settings' ),
                'default'           => array( 'form_id' => 0 ),
            )
        );

        add_settings_section(
            'cgd_main',
            __( 'Impostazioni generali', 'calccgd' ),
            '__return_false',
            'calccgd'
        );

        add_settings_field(
            'cgd_form_id',
            __( 'ID Formidable form', 'calccgd' ),
            array( $this, 'render_form_id_field' ),
            'calccgd',
            'cgd_main'
        );
    }

    /**
     * Sanitize settings array.
     *
     * @param mixed $value Raw option value.
     * @return array
     */
    public function sanitize_settings( $value ): array {
        if ( ! is_array( $value ) ) {
            return array();
        }

        $sanitized = array();

        if ( isset( $value['form_id'] ) ) {
            $sanitized['form_id'] = absint( $value['form_id'] );
        }

        return $sanitized;
    }

    /**
     * Add options page.
     */
    public function add_options_page(): void {
        add_options_page(
            __( 'CalcCGD', 'calccgd' ),
            __( 'CalcCGD', 'calccgd' ),
            'manage_options',
            'calccgd',
            array( $this, 'render_options_page' )
        );
    }

    /**
     * Render the main options page.
     */
    public function render_options_page(): void {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Impostazioni CalcCGD', 'calccgd' ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'cgd_group' );
                do_settings_sections( 'calccgd' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render field for Formidable form ID.
     */
    public function render_form_id_field(): void {
        $value = Options::get( 'form_id', 0 );
        echo '<input type="number" class="small-text" name="cgd_settings[form_id]" value="' . esc_attr( $value ) . '" min="0" />';
    }

    /**
     * Enqueue public assets only when shortcode is present.
     */
    public function enqueue_public(): void {
        if ( ! $this->has_shortcode() ) {
            return;
        }

        // Placeholder for enqueueing scripts and styles.
    }

    /**
     * Check if current post contains plugin shortcode.
     *
     * @return bool
     */
    private function has_shortcode(): bool {
        if ( ! is_singular() ) {
            return false;
        }

        $post = get_post();
        if ( ! $post ) {
            return false;
        }

        return has_shortcode( $post->post_content, 'cgd-calculator' );
    }
}

