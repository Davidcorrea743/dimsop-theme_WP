<?php
/**
 * Funciones de utilidad y helpers generales del tema Dimsop
 *
 * @package Dimsop
 * @subpackage Helpers
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Valores por defecto de las opciones corporativas
 *
 * Se usan cuando la opción no se ha guardado en Apariencia › Opciones Dimsop.
 *
 * @return array<string, string>
 */
function dimsop_get_option_defaults() {
    return array(
        // Teléfono general (usado también como WhatsApp hasta confirmar el definitivo)
        'whatsapp_number'  => '582128195400',
        'contact_phone_ve' => '+58 212-819.54.00',
        'contact_phone_us' => '+1 954-271-9003',
        // Correos por destino
        'contact_email'    => 'mercadeo@dimsop.com',   // Formulario de contacto
        'careers_email'    => 'talento@dimsop.com',    // Postulaciones / CV
        'sales_email'      => 'ventas@dimsop.com',     // Sección de mapas / contacto ventas
        'address_ve'       => 'Calle Sanatorio del Avila, Centro Empresarial Ciudad Center, Torre C, Piso 4, Ofic. 41-C, Municipio Sucre – Estado Miranda',
        'address_us'       => '4995 NW 72 Nd AVE STE 205, Miami, Florida',
        'rif_ve'           => 'J-41137726-0',
        'linkedin_url'     => 'https://www.linkedin.com/company/dimsop',
        'instagram_url'    => 'https://www.instagram.com/dimsop',
    );
}

/**
 * Obtener opción de configuración corporativa de Dimsop
 *
 * Permite centralizar valores globales como teléfonos, sedes y redes.
 *
 * @param string $key Identificador de la opción.
 * @param mixed  $default Valor por defecto en caso de no estar definida.
 * @return mixed
 */
function dimsop_get_option( $key, $default = '' ) {
    $options  = get_option( 'dimsop_theme_options', array() );
    $defaults = dimsop_get_option_defaults();

    if ( isset( $options[ $key ] ) && ! empty( $options[ $key ] ) ) {
        return $options[ $key ];
    }

    if ( isset( $defaults[ $key ] ) ) {
        return $defaults[ $key ];
    }

    return $default;
}

/**
 * Comprobar si una IP pertenece a alguno de los rangos dados (IP suelta o CIDR, IPv4 o IPv6)
 *
 * @param string   $ip     IP ya validada.
 * @param string[] $ranges Lista de IP o rangos, por ejemplo '173.245.48.0/20'.
 * @return bool
 */
function dimsop_ip_in_ranges( $ip, $ranges ) {
    $ip_bin = inet_pton( $ip );

    foreach ( (array) $ranges as $range ) {
        $parts = explode( '/', trim( $range ), 2 );

        if ( ! filter_var( $parts[0], FILTER_VALIDATE_IP ) ) {
            continue;
        }

        $net_bin = inet_pton( $parts[0] );

        if ( strlen( $net_bin ) !== strlen( $ip_bin ) ) {
            continue; // Una es IPv4 y la otra IPv6.
        }

        $max_bits = strlen( $net_bin ) * 8;
        $bits     = isset( $parts[1] ) ? min( max( (int) $parts[1], 0 ), $max_bits ) : $max_bits;
        $bytes    = intdiv( $bits, 8 );
        $rest     = $bits % 8;

        if ( substr( $ip_bin, 0, $bytes ) !== substr( $net_bin, 0, $bytes ) ) {
            continue;
        }

        if ( 0 === $rest ) {
            return true;
        }

        $mask = ( 0xFF << ( 8 - $rest ) ) & 0xFF;

        if ( ( ord( $ip_bin[ $bytes ] ) & $mask ) === ( ord( $net_bin[ $bytes ] ) & $mask ) ) {
            return true;
        }
    }

    return false;
}

/**
 * Obtener la IP del cliente sin fiarse de cabeceras que el visitante puede falsificar
 *
 * Por defecto usa REMOTE_ADDR. X-Forwarded-For solo se lee si la petición llega desde
 * un proxy declarado en wp-config.php (Cloudflare, balanceador):
 *
 *     define( 'DIMSOP_TRUSTED_PROXIES', array( '173.245.48.0/20', '10.0.0.5' ) );
 *
 * La cabecera se recorre de derecha a izquierda porque cada proxy añade su dato al
 * final: lo que esté a la izquierda de la primera IP no confiable lo pudo escribir
 * el propio visitante.
 *
 * @return string IP validada o '0.0.0.0' si es inválida
 */
function dimsop_get_client_ip() {
    $remote = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

    if ( ! filter_var( $remote, FILTER_VALIDATE_IP ) ) {
        return '0.0.0.0';
    }

    if (
        ! defined( 'DIMSOP_TRUSTED_PROXIES' )
        || empty( $_SERVER['HTTP_X_FORWARDED_FOR'] )
        || ! dimsop_ip_in_ranges( $remote, DIMSOP_TRUSTED_PROXIES )
    ) {
        return $remote;
    }

    $forwarded = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );

    foreach ( array_reverse( $forwarded ) as $candidate ) {
        $candidate = trim( $candidate );

        if ( ! filter_var( $candidate, FILTER_VALIDATE_IP ) ) {
            break;
        }

        if ( ! dimsop_ip_in_ranges( $candidate, DIMSOP_TRUSTED_PROXIES ) ) {
            return $candidate;
        }
    }

    return $remote;
}

/**
 * Rate Limiting basado en WordPress Transients API
 *
 * Previene ataques de fuerza bruta y spam en formularios públicos sin depender de librerías externas.
 *
 * @param string $action_context Nombre del contexto ('contact_form', 'career_apply').
 * @param int    $max_attempts    Número máximo de intentos permitidos en la ventana.
 * @param int    $window_seconds  Duración de la ventana de tiempo en segundos.
 * @return bool True si la IP superó el límite (debe bloquearse), False si aún puede operar.
 */
function dimsop_is_rate_limited( $action_context = 'general', $max_attempts = 5, $window_seconds = 600 ) {
    $ip = dimsop_get_client_ip();
    // Hash criptográfico con salt del sistema para anonimizar la IP y proteger la privacidad
    $ip_hash = hash_hmac( 'sha256', $ip, wp_salt( 'nonce' ) );
    $transient_key = 'dimsop_rl_' . substr( $action_context, 0, 10 ) . '_' . substr( $ip_hash, 0, 16 );

    $current_hits = (int) get_transient( $transient_key );

    if ( $current_hits >= $max_attempts ) {
        return true;
    }

    $new_hits = $current_hits + 1;
    set_transient( $transient_key, $new_hits, $window_seconds );

    return false;
}

/**
 * Sanitizar número de teléfono internacional (solo dígitos y símbolo +)
 *
 * @param string $phone
 * @return string
 */
function dimsop_sanitize_phone( $phone ) {
    $phone = preg_replace( '/[^0-9+]/', '', (string) $phone );
    return substr( $phone, 0, 30 );
}

/**
 * Obtener URL de asset con cache-busting automático por fecha de modificación
 *
 * @param string $relative_path Ruta relativa dentro de /assets/.
 * @return string URL absoluta con parámetro ?v=mtime
 */
function dimsop_asset_url( $relative_path ) {
    $file_path = DIMSOP_THEME_DIR . '/assets/' . ltrim( $relative_path, '/' );
    $file_uri  = DIMSOP_THEME_URI . '/assets/' . ltrim( $relative_path, '/' );

    if ( file_exists( $file_path ) ) {
        return esc_url( add_query_arg( 'v', filemtime( $file_path ), $file_uri ) );
    }

    return esc_url( $file_uri );
}
