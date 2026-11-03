<?php
/**
 * Configuración y soportes esenciales del tema Dimsop
 *
 * @package Dimsop
 * @subpackage Setup
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'dimsop_theme_setup' ) ) {
    function dimsop_theme_setup() {
        // Cargar traducciones
        load_theme_textdomain( 'dimsop', DIMSOP_THEME_DIR . '/languages' );

        // Añadir soporte de etiquetas de título automáticas
        add_theme_support( 'title-tag' );

        // Soporte de imágenes destacadas
        add_theme_support( 'post-thumbnails' );

        // Tamaños de imagen personalizados optimizados para la UI corporativa
        add_image_size( 'dimsop-blog-card', 768, 432, true );     // Proporción 16:9
        add_image_size( 'dimsop-project-card', 800, 500, true );  // Tarjeta de proyectos
        add_image_size( 'dimsop-partner-logo', 320, 160, false ); // Logos de aliados

        // Soporte de logo personalizado administrable
        add_theme_support( 'custom-logo', array(
            'height'      => 80,
            'width'       => 280,
            'flex-height' => true,
            'flex-width'  => true,
            'header-text' => array( 'site-title', 'site-description' ),
        ) );

        // Soporte HTML5 para formularios y elementos de estructura
        add_theme_support( 'html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        ) );

        // Soporte para incrustaciones responsivas (video iframes, mapas)
        add_theme_support( 'responsive-embeds' );

        // Registro de menús de navegación
        register_nav_menus( array(
            'primary' => esc_html__( 'Menú Principal (Cabecera)', 'dimsop' ),
            'footer'  => esc_html__( 'Menú Secundario (Pie de Página)', 'dimsop' ),
        ) );
    }
}
add_action( 'after_setup_theme', 'dimsop_theme_setup' );

/**
 * Genera en WebP los tamaños intermedios de las imágenes JPG/PNG subidas
 * (el original se conserva). Solo si el servidor soporta WebP.
 */
function dimsop_image_output_format( $formats ) {
    if ( wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) ) ) {
        $formats['image/jpeg'] = 'image/webp';
        $formats['image/png']  = 'image/webp';
    }
    return $formats;
}
add_filter( 'image_editor_output_format', 'dimsop_image_output_format' );

/**
 * Favicon de respaldo mientras no se defina un Icono del sitio en el Personalizador.
 */
function dimsop_fallback_favicon() {
    if ( has_site_icon() ) {
        return;
    }
    printf(
        '<link rel="icon" type="image/webp" href="%s">' . "\n",
        esc_url( dimsop_asset_url( 'images/logos/logo_Dimsop_D_Degradado.webp' ) )
    );
}
add_action( 'wp_head', 'dimsop_fallback_favicon', 2 );
add_action( 'admin_head', 'dimsop_fallback_favicon' );

/**
 * Ancho máximo de contenido global
 */
function dimsop_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'dimsop_content_width', 1200 );
}
add_action( 'after_setup_theme', 'dimsop_content_width', 0 );
