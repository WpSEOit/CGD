<?php
namespace CGD\Calc;

/**
 * Handles Formidable Forms integration and calculations.
 */
final class Calculator {
    /**
     * Map of field keys to human-readable labels.
     *
     * @var array<string,string>
     */
    public const FIELD_MAP = array(
        'gas_mc'            => 'Consumo Gas Metano annuo (mc)',
        'gas_cost'          => 'Spesa Gas Metano annua (€)',
        'gpl_kg'            => 'Consumo GPL annuo (kg)',
        'gpl_cost'          => 'Spesa GPL annua (€)',
        'biomass_ql'        => 'Consumo biomassa annuo (q.li)',
        'biomass_cost'      => 'Spesa biomassa annua (€)',
        'elec_kwh'          => 'Consumo elettricità annuo (kWh)',
        'elec_cost'         => 'Spesa elettricità annua (€)',
        'roof_area_m2'      => 'Superficie tetto disponibile (m²)',
        'geo_zone'          => 'Zona geografica',
        'roof_type'         => 'Tipo di tetto',
        'heating_system'    => 'Impianto di riscaldamento presente',
        'heating_terminals' => 'Terminali di riscaldamento presenti',
        'remove_biomass'    => 'Rimozione biomassa prevista',
        'unit_type'         => 'Tipologia unità abitativa',
        'roof_dispersion'   => 'Dispersione dal tetto',
        'roof_insulated'    => 'Tetto isolato',
        'walls_insulated'   => 'Muri isolati',
        'area_m2'           => 'Superficie calpestabile (m²)',
        'floors_n'          => 'Numero piani',
        'windows_n'         => 'Numero finestre',
        'first_home'        => 'Prima casa',
        'privacy_ok'        => 'Consenso privacy',
    );
    /**
     * Attach hooks.
     */
    public function init(): void {
        add_filter( 'frm_validate_entry', array( $this, 'validate_entry' ), 20, 2 );
        add_filter( 'frm_validate_field_entry', array( $this, 'validate_field' ), 10, 3 );
        add_filter( 'frm_force_calculation_on_validate', '__return_true' );
        add_action( 'frm_after_create_entry', array( $this, 'after_create_entry' ), 20, 2 );
    }

    /**
     * Global entry validation.
     *
     * @param array $errors Current errors.
     * @param array $values Submitted values.
     * @return array
     */
    public function validate_entry( array $errors, array $values ): array {
        if ( isset( $values['form_id'] ) && (int) $values['form_id'] !== Options::get_form_id() ) {
            return $errors;
        }

        $consumption_fields = array( 'gas_mc', 'gpl_kg', 'biomass_ql', 'elec_kwh' );
        $has_consumption   = false;
        foreach ( $consumption_fields as $key ) {
            $field_id = \FrmField::get_id_by_key( $key );
            if ( $field_id && ! empty( $values['item_meta'][ $field_id ] ) && (float) $values['item_meta'][ $field_id ] > 0 ) {
                $has_consumption = true;
                break;
            }
        }

        if ( ! $has_consumption ) {
            $first_id = \FrmField::get_id_by_key( 'gas_mc' );
            if ( $first_id ) {
                $errors[ 'field' . $first_id ] = esc_html__( 'Inserire almeno un consumo.', 'calccgd' );
            }
        }

        $privacy_id = \FrmField::get_id_by_key( 'privacy_ok' );
        if ( $privacy_id && empty( $values['item_meta'][ $privacy_id ] ) ) {
            $errors[ 'field' . $privacy_id ] = esc_html__( 'Consenso privacy obbligatorio.', 'calccgd' );
        }

        return $errors;
    }

    /**
     * Field-specific validation.
     *
     * @param array $errors       Field errors.
     * @param object $posted_field Field object.
     * @param mixed  $posted_value Submitted value.
     * @return array
     */
    public function validate_field( array $errors, $posted_field, $posted_value ): array {
        $key = $posted_field->field_key ?? '';

        $numeric = array( 'gas_mc', 'gas_cost', 'gpl_kg', 'gpl_cost', 'biomass_ql', 'biomass_cost', 'elec_kwh', 'elec_cost', 'roof_area_m2', 'area_m2' );
        $integer = array( 'floors_n', 'windows_n' );

        if ( in_array( $key, $numeric, true ) ) {
            if ( $posted_value !== '' && ! is_numeric( $posted_value ) ) {
                $errors[ $posted_field->id ] = esc_html__( 'Inserire un numero valido.', 'calccgd' );
            } elseif ( (float) $posted_value < 0 ) {
                $errors[ $posted_field->id ] = esc_html__( 'Il valore deve essere maggiore o uguale a zero.', 'calccgd' );
            }
            return $errors;
        }

        if ( in_array( $key, $integer, true ) ) {
            if ( $posted_value !== '' && filter_var( $posted_value, FILTER_VALIDATE_INT ) === false ) {
                $errors[ $posted_field->id ] = esc_html__( 'Inserire un numero intero.', 'calccgd' );
            } elseif ( (int) $posted_value < 0 ) {
                $errors[ $posted_field->id ] = esc_html__( 'Il valore deve essere maggiore o uguale a zero.', 'calccgd' );
            }
            return $errors;
        }

        switch ( $key ) {
            case 'geo_zone':
                $allowed = array( 'nord', 'centro', 'sud' );
                if ( $posted_value !== '' && ! in_array( strtolower( (string) $posted_value ), $allowed, true ) ) {
                    $errors[ $posted_field->id ] = esc_html__( 'Zona geografica non valida.', 'calccgd' );
                }
                break;
            case 'roof_type':
                $allowed = array( 'piano', 'falda' );
                if ( $posted_value !== '' && ! in_array( strtolower( (string) $posted_value ), $allowed, true ) ) {
                    $errors[ $posted_field->id ] = esc_html__( 'Tipo di tetto non valido.', 'calccgd' );
                }
                break;
            case 'unit_type':
                $allowed = array( 'appartamento_autonomo', 'villetta_schiera', 'casa_indipendente_4lati', 'condominio_centralizzato' );
                if ( $posted_value !== '' && ! in_array( strtolower( (string) $posted_value ), $allowed, true ) ) {
                    $errors[ $posted_field->id ] = esc_html__( 'Tipologia unità non valida.', 'calccgd' );
                }
                break;
            case 'heating_system':
            case 'heating_terminals':
                $allowed = array( 'si', 'no' );
                if ( $posted_value !== '' && ! in_array( strtolower( (string) $posted_value ), $allowed, true ) ) {
                    $errors[ $posted_field->id ] = esc_html__( 'Valore non valido.', 'calccgd' );
                }
                break;
            case 'remove_biomass':
            case 'roof_dispersion':
            case 'roof_insulated':
            case 'walls_insulated':
            case 'first_home':
            case 'privacy_ok':
                $allowed = array( '0', '1' );
                if ( $posted_value !== '' && ! in_array( (string) $posted_value, $allowed, true ) ) {
                    $errors[ $posted_field->id ] = esc_html__( 'Valore non valido.', 'calccgd' );
                }
                break;
        }

        return $errors;
    }

    /**
     * After entry creation, perform calculations and store results.
     *
     * @param int $entry_id Entry identifier.
     * @param int $form_id  Form identifier.
     */
    public function after_create_entry( int $entry_id, int $form_id ): void {
        if ( (int) $form_id !== Options::get_form_id() ) {
            return;
        }

        $sanitized = array();

        foreach ( array_keys( self::FIELD_MAP ) as $key ) {
            $field_id = \FrmField::get_id_by_key( $key );
            if ( ! $field_id ) {
                continue;
            }

            $raw = \FrmEntryMeta::get_entry_meta_by_field( $entry_id, $field_id );
            if ( is_array( $raw ) ) {
                $value = array_map( 'sanitize_text_field', $raw );
                $value = implode( ', ', $value );
            } else {
                $value = sanitize_text_field( (string) $raw );
            }

            $sanitized[ $key ] = $value;
            \FrmEntryMeta::update_entry_meta( $entry_id, 0, 'cgd_' . $key, $value );
        }

        // Placeholder calculation: sum of annual costs.
        $total = 0.0;
        foreach ( array( 'gas_cost', 'gpl_cost', 'biomass_cost', 'elec_cost' ) as $cost_key ) {
            $total += isset( $sanitized[ $cost_key ] ) ? (float) $sanitized[ $cost_key ] : 0.0;
        }

        \FrmEntryMeta::update_entry_meta( $entry_id, 0, 'cgd_tot_spesa_annua', $total );

        $redirect = wp_get_referer();
        if ( $redirect ) {
            wp_safe_redirect( add_query_arg( 'cgd_entry', $entry_id, $redirect ) );
            exit;
        }
    }
}

