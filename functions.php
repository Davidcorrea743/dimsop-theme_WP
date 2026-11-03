<?php
/**
 * Dimsop Theme functions and definitions
 *
 * @package Dimsop
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// Definición de constantes del tema
define( 'DIMSOP_THEME_VERSION', '1.0.0' );
define( 'DIMSOP_THEME_DIR', get_template_directory() );
define( 'DIMSOP_THEME_URI', get_template_directory_uri() );

/**
 * Carga modular de subsistemas del tema
 */
require_once DIMSOP_THEME_DIR . '/inc/security.php';
require_once DIMSOP_THEME_DIR . '/inc/setup.php';
require_once DIMSOP_THEME_DIR . '/inc/helpers.php';
require_once DIMSOP_THEME_DIR . '/inc/enqueues.php';
require_once DIMSOP_THEME_DIR . '/inc/navigation.php';

// Los módulos de CPTs y Formularios se cargarán en las siguientes fases
if ( file_exists( DIMSOP_THEME_DIR . '/inc/post-types.php' ) ) {
    require_once DIMSOP_THEME_DIR . '/inc/post-types.php';
}

if ( file_exists( DIMSOP_THEME_DIR . '/inc/forms/contact-handler.php' ) ) {
    require_once DIMSOP_THEME_DIR . '/inc/forms/contact-handler.php';
}

if ( file_exists( DIMSOP_THEME_DIR . '/inc/forms/careers-handler.php' ) ) {
    require_once DIMSOP_THEME_DIR . '/inc/forms/careers-handler.php';
}
