<?php
namespace CGD\Calc;

/**
 * Core plugin class.
 */
final class Plugin {
    /**
     * Bootstrap plugin components.
     */
    public function init(): void {
        ( new Admin() )->init();
        ( new Calculator() )->init();
        add_shortcode( 'cgd-calculator', array( $this, 'render_shortcode' ) );
    }

    /**
     * Render shortcode output.
     *
     * @param array       $atts    Shortcode attributes.
     * @param string|null $content Enclosed content.
     * @param string       $tag     Shortcode tag.
     * @return string
     */
    public function render_shortcode( array $atts = array(), ?string $content = null, string $tag = '' ): string {
        if ( isset( $_GET['cgd_entry'] ) ) {
            $entry_id = absint( $_GET['cgd_entry'] );
            if ( ! $entry_id ) {
                return '';
            }

            $output = '<div class="cgd-results"><h2>' . esc_html__( 'Dati inseriti', 'calccgd' ) . '</h2><dl>';
            foreach ( Calculator::FIELD_MAP as $key => $label ) {
                $value = \FrmEntryMeta::get_entry_meta( $entry_id, 0, 'cgd_' . $key, true );
                $output .= '<dt>' . esc_html( $label ) . '</dt><dd>' . esc_html( (string) $value ) . '</dd>';
            }

            $totale = \FrmEntryMeta::get_entry_meta( $entry_id, 0, 'cgd_tot_spesa_annua', true );
            $output .= '<dt>' . esc_html__( 'Totale spesa annua', 'calccgd' ) . '</dt><dd>' . esc_html( (string) $totale ) . '</dd>';
            $output .= '</dl></div>';

            return $output;
        }

        $form_id = Options::get_form_id();
        return do_shortcode( '[formidable id="' . esc_attr( $form_id ) . '"]' );
    }
}

