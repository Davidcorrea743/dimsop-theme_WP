<?php
/**
 * Página de opciones del tema (Apariencia › Opciones Dimsop)
 *
 * Edita los valores que lee dimsop_get_option() y ofrece el importador
 * del contenido inicial (unidades, aliados y proyectos).
 *
 * @package Dimsop
 * @subpackage Options
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Definición de los campos agrupados por sección.
 *
 * @return array<string, array{title: string, fields: array<string, array{label: string, type: string, help?: string}>}>
 */
function dimsop_get_option_fields() {
    return array(
        'contacto' => array(
            'title'  => __( 'Contacto', 'dimsop' ),
            'fields' => array(
                'whatsapp_number'  => array( 'label' => __( 'Número de WhatsApp', 'dimsop' ), 'type' => 'digits', 'help' => __( 'Solo dígitos, con código de país. Ej.: 582128195400', 'dimsop' ) ),
                'contact_phone_ve' => array( 'label' => __( 'Teléfono Venezuela', 'dimsop' ), 'type' => 'phone' ),
                'contact_phone_us' => array( 'label' => __( 'Teléfono Estados Unidos', 'dimsop' ), 'type' => 'phone' ),
                'contact_email'    => array( 'label' => __( 'Correo del formulario de contacto', 'dimsop' ), 'type' => 'email' ),
                'careers_email'    => array( 'label' => __( 'Correo de postulaciones (CV)', 'dimsop' ), 'type' => 'email' ),
                'sales_email'      => array( 'label' => __( 'Correo de ventas', 'dimsop' ), 'type' => 'email' ),
            ),
        ),
        'sedes'    => array(
            'title'  => __( 'Sedes', 'dimsop' ),
            'fields' => array(
                'address_ve' => array( 'label' => __( 'Dirección Venezuela', 'dimsop' ), 'type' => 'textarea' ),
                'address_us' => array( 'label' => __( 'Dirección Estados Unidos', 'dimsop' ), 'type' => 'textarea' ),
                'rif_ve'     => array( 'label' => __( 'RIF', 'dimsop' ), 'type' => 'text' ),
                'maps_url_ve'   => array( 'label' => __( 'Enlace "Cómo llegar" Venezuela', 'dimsop' ), 'type' => 'url', 'help' => __( 'Enlace de Google Maps que se abre en una pestaña nueva.', 'dimsop' ) ),
                'maps_embed_ve' => array( 'label' => __( 'Mapa incrustado Venezuela', 'dimsop' ), 'type' => 'url', 'help' => __( 'En Google Maps: Compartir › Insertar un mapa, y copiar solo la dirección del atributo src. Debe empezar por https://www.google.com/maps/embed', 'dimsop' ) ),
                'maps_url_us'   => array( 'label' => __( 'Enlace "Cómo llegar" Estados Unidos', 'dimsop' ), 'type' => 'url' ),
                'maps_embed_us' => array( 'label' => __( 'Mapa incrustado Estados Unidos', 'dimsop' ), 'type' => 'url', 'help' => __( 'Debe empezar por https://www.google.com/maps/embed', 'dimsop' ) ),
            ),
        ),
        'redes'    => array(
            'title'  => __( 'Redes sociales', 'dimsop' ),
            'fields' => array(
                'linkedin_url'  => array( 'label' => __( 'LinkedIn', 'dimsop' ), 'type' => 'url' ),
                'instagram_url' => array( 'label' => __( 'Instagram', 'dimsop' ), 'type' => 'url' ),
            ),
        ),
    );
}

/**
 * Registra la página en el menú Apariencia.
 */
function dimsop_add_options_page() {
    add_theme_page(
        __( 'Opciones Dimsop', 'dimsop' ),
        __( 'Opciones Dimsop', 'dimsop' ),
        'manage_options',
        'dimsop-options',
        'dimsop_render_options_page'
    );
}
add_action( 'admin_menu', 'dimsop_add_options_page' );

/**
 * Registra la opción, las secciones y los campos con la Settings API.
 */
function dimsop_register_options() {
    register_setting( 'dimsop_options_group', 'dimsop_theme_options', array(
        'type'              => 'array',
        'sanitize_callback' => 'dimsop_sanitize_options',
        'default'           => array(),
    ) );

    $defaults = dimsop_get_option_defaults();

    foreach ( dimsop_get_option_fields() as $section_id => $section ) {
        add_settings_section( 'dimsop_' . $section_id, $section['title'], '__return_false', 'dimsop-options' );

        foreach ( $section['fields'] as $key => $field ) {
            add_settings_field(
                $key,
                $field['label'],
                'dimsop_render_option_field',
                'dimsop-options',
                'dimsop_' . $section_id,
                array(
                    'key'         => $key,
                    'field'       => $field,
                    'placeholder' => $defaults[ $key ] ?? '',
                    'label_for'   => 'dimsop-opt-' . $key,
                )
            );
        }
    }
}
add_action( 'admin_init', 'dimsop_register_options' );

/**
 * Imprime un campo. Vacío = se usa el valor por defecto (mostrado como placeholder).
 *
 * @param array $args Argumentos de add_settings_field.
 */
function dimsop_render_option_field( $args ) {
    $options = get_option( 'dimsop_theme_options', array() );
    $key     = $args['key'];
    $value   = $options[ $key ] ?? '';
    $name    = 'dimsop_theme_options[' . $key . ']';
    $id      = 'dimsop-opt-' . $key;

    if ( 'textarea' === $args['field']['type'] ) {
        printf(
            '<textarea class="large-text" rows="2" id="%1$s" name="%2$s" placeholder="%3$s">%4$s</textarea>',
            esc_attr( $id ),
            esc_attr( $name ),
            esc_attr( $args['placeholder'] ),
            esc_textarea( $value )
        );
    } else {
        $input_type = in_array( $args['field']['type'], array( 'email', 'url' ), true ) ? $args['field']['type'] : 'text';
        printf(
            '<input type="%1$s" class="regular-text" id="%2$s" name="%3$s" value="%4$s" placeholder="%5$s">',
            esc_attr( $input_type ),
            esc_attr( $id ),
            esc_attr( $name ),
            esc_attr( $value ),
            esc_attr( $args['placeholder'] )
        );
    }

    if ( ! empty( $args['field']['help'] ) ) {
        printf( '<p class="description">%s</p>', esc_html( $args['field']['help'] ) );
    }
}

/**
 * Sanea los valores según el tipo de campo. Solo se guardan claves conocidas.
 *
 * @param mixed $input Valores enviados.
 * @return array<string, string>
 */
function dimsop_sanitize_options( $input ) {
    $clean = array();
    if ( ! is_array( $input ) ) {
        return $clean;
    }

    foreach ( dimsop_get_option_fields() as $section ) {
        foreach ( $section['fields'] as $key => $field ) {
            if ( ! isset( $input[ $key ] ) ) {
                continue;
            }
            $raw = wp_unslash( $input[ $key ] );

            switch ( $field['type'] ) {
                case 'digits':
                    $clean[ $key ] = preg_replace( '/\D+/', '', $raw );
                    break;
                case 'phone':
                    // Conserva el formato visible (espacios, guiones, puntos).
                    $clean[ $key ] = substr( trim( preg_replace( '/[^0-9+\-\.\s()]/', '', $raw ) ), 0, 30 );
                    break;
                case 'email':
                    $clean[ $key ] = sanitize_email( $raw );
                    break;
                case 'url':
                    $clean[ $key ] = esc_url_raw( $raw );
                    break;
                case 'textarea':
                    $clean[ $key ] = sanitize_textarea_field( $raw );
                    break;
                default:
                    $clean[ $key ] = sanitize_text_field( $raw );
            }
        }
    }

    return $clean;
}

/**
 * Pinta la página: formulario de opciones e importador de contenido.
 */
function dimsop_render_options_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $import_result = isset( $_GET['dimsop_imported'] ) ? sanitize_text_field( wp_unslash( $_GET['dimsop_imported'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- solo lectura de un aviso.
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Opciones Dimsop', 'dimsop' ); ?></h1>
        <p><?php esc_html_e( 'Datos corporativos usados en el header, el footer, los formularios y el botón de WhatsApp. Si dejas un campo vacío se usa el valor mostrado en gris.', 'dimsop' ); ?></p>

        <?php if ( $import_result ) : ?>
            <div class="notice notice-success is-dismissible"><p><?php echo esc_html( $import_result ); ?></p></div>
        <?php endif; ?>

        <form method="post" action="options.php">
            <?php
            settings_fields( 'dimsop_options_group' );
            do_settings_sections( 'dimsop-options' );
            submit_button();
            ?>
        </form>

        <hr>
        <h2><?php esc_html_e( 'Contenido inicial', 'dimsop' ); ?></h2>
        <p><?php esc_html_e( 'Crea las unidades de negocio, los aliados tecnológicos y los proyectos referenciales del sitio Odoo, con sus logos, y las páginas Política de privacidad y Catálogo. Se puede ejecutar varias veces: lo que ya existe no se duplica.', 'dimsop' ); ?></p>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="dimsop_import_content">
            <?php wp_nonce_field( 'dimsop_import_content' ); ?>
            <?php submit_button( __( 'Importar contenido inicial', 'dimsop' ), 'secondary', 'submit', false ); ?>
        </form>
    </div>
    <?php
}
