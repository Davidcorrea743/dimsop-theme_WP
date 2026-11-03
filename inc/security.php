<?php
/**
 * Hardening y Políticas de Seguridad del Theme Dimsop
 *
 * @package Dimsop
 * @subpackage Security
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Inyección de Cabeceras HTTP de Seguridad (Defensa en profundidad)
 *
 * Replica y amplía las cabeceras definidas en controllers/main.py de Odoo
 */
function dimsop_security_headers() {
    if ( headers_sent() ) {
        return;
    }

    // Prevención de Clickjacking
    header( 'X-Frame-Options: SAMEORIGIN' );

    // Prevención de MIME-Sniffing
    header( 'X-Content-Type-Options: nosniff' );

    // Control estricto de Referrer
    header( 'Referrer-Policy: strict-origin-when-cross-origin' );

    // Deshabilitar APIs de hardware no utilizadas
    header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );

    // CSP básico: restringir que el sitio no pueda embeberse en sitios externos
    header( "Content-Security-Policy: frame-ancestors 'self';" );

    // HSTS (HTTP Strict Transport Security) cuando la conexión es SSL
    if ( is_ssl() ) {
        header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains; preload' );
    }
}
add_action( 'send_headers', 'dimsop_security_headers' );

/**
 * Deshabilitar XML-RPC totalmente para mitigar ataques DDoS y fuerza bruta
 */
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'xmlrpc_methods', '__return_empty_array' );

/**
 * Limpieza de cabeceras expuestas en wp_head (Footprinting / Information Disclosure)
 */
function dimsop_clean_wp_head() {
    // Elimina la versión de WordPress del HTML
    remove_action( 'wp_head', 'wp_generator' );
    // Elimina el enlace RSD (Really Simple Discovery)
    remove_action( 'wp_head', 'rsd_link' );
    // Elimina el enlace a Windows Live Writer
    remove_action( 'wp_head', 'wlwmanifest_link' );
    // Elimina enlaces de shortlink de posts
    remove_action( 'wp_head', 'wp_shortlink_wp_head' );
    // Elimina enlaces relacionales adyacentes de posts
    remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
    // Elimina la versión de WordPress en feeds RSS
    add_filter( 'the_generator', '__return_empty_string' );
}
add_action( 'init', 'dimsop_clean_wp_head' );

/**
 * Bloquear enumeración de usuarios vía REST API (/wp-json/wp/v2/users)
 *
 * Evita que atacantes listen usuarios y usernames de administradores sin autenticación.
 */
function dimsop_restrict_rest_user_enumeration( $response, $server, $request ) {
    $route = $request->get_route();

    if ( strpos( $route, '/wp/v2/users' ) === 0 && ! current_user_can( 'list_users' ) ) {
        return new WP_Error(
            'rest_forbidden_users',
            esc_html__( 'Acceso no autorizado al listado de usuarios.', 'dimsop' ),
            array( 'status' => rest_authorization_required_code() )
        );
    }

    return $response;
}
add_filter( 'rest_pre_dispatch', 'dimsop_restrict_rest_user_enumeration', 10, 3 );

/**
 * Bloquear escaneo y enumeración de usuarios mediante consultas ?author=N
 */
function dimsop_block_author_scanning() {
    if ( ! is_admin() && isset( $_REQUEST['author'] ) && ! empty( $_REQUEST['author'] ) ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_safe_redirect( home_url(), 301 );
            exit;
        }
    }
}
add_action( 'template_redirect', 'dimsop_block_author_scanning' );

/**
 * Ofuscar mensajes de error en la pantalla de inicio de sesión
 *
 * Evita revelar si el nombre de usuario o correo existe en el sistema.
 */
function dimsop_generic_login_errors() {
    return esc_html__( 'Credenciales no válidas. Por favor intente de nuevo.', 'dimsop' );
}
add_filter( 'login_errors', 'dimsop_generic_login_errors' );

/**
 * Deshabilitar pingbacks y trackbacks
 */
function dimsop_disable_pingbacks( $headers ) {
    unset( $headers['X-Pingback'] );
    return $headers;
}
add_filter( 'wp_headers', 'dimsop_disable_pingbacks' );

/**
 * Eliminar la versión de WordPress de las URLs de scripts y hojas de estilo
 * (para evitar exponer versiones de WP públicamente en producción)
 */
function dimsop_remove_version_query_strings( $src ) {
    if ( is_admin() ) {
        return $src;
    }
    if ( strpos( $src, 'ver=' . get_bloginfo( 'version' ) ) ) {
        $src = remove_query_arg( 'ver', $src );
    }
    return $src;
}
add_filter( 'style_loader_src', 'dimsop_remove_version_query_strings', 9999 );
add_filter( 'script_loader_src', 'dimsop_remove_version_query_strings', 9999 );
