<?php
/**
 * Navegación del tema Dimsop: clases de Bootstrap para los menús de WordPress
 * y menú de respaldo cuando no hay un menú asignado.
 *
 * @package Dimsop
 * @subpackage Navigation
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Añade la clase nav-item a los <li> del menú principal.
 */
function dimsop_nav_menu_item_class( $classes, $item, $args ) {
    if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
        $classes[] = 'nav-item';
    }
    return $classes;
}
add_filter( 'nav_menu_css_class', 'dimsop_nav_menu_item_class', 10, 3 );

/**
 * Añade la clase nav-link a los <a> del menú principal para que tomen los estilos del header.
 */
function dimsop_nav_menu_link_attributes( $atts, $item, $args ) {
    if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
        $atts['class'] = trim( ( $atts['class'] ?? '' ) . ' nav-link' );
    }
    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'dimsop_nav_menu_link_attributes', 10, 3 );

/**
 * Enlaces del menú de respaldo.
 *
 * @return array<int, array{url: string, label: string}>
 */
function dimsop_get_fallback_menu_items() {
    $items = array(
        array( 'url' => dimsop_home_url( '/' ),           'label' => __( 'Inicio', 'dimsop' ) ),
        array( 'url' => dimsop_home_url( '/#nosotros' ),  'label' => __( 'Nosotros', 'dimsop' ) ),
        array( 'url' => dimsop_home_url( '/#unidades' ),  'label' => __( 'Unidades de Negocio', 'dimsop' ) ),
        array( 'url' => dimsop_home_url( '/#aliados' ),   'label' => __( 'Aliados', 'dimsop' ) ),
        array( 'url' => dimsop_home_url( '/#blog' ),       'label' => __( 'Blog', 'dimsop' ) ),
        array( 'url' => dimsop_page_url( 'catalogo' ),   'label' => __( 'Catálogo', 'dimsop' ) ),
        array( 'url' => dimsop_home_url( '/#contacto' ),  'label' => __( 'Contacto', 'dimsop' ) ),
    );

    // Sin entradas en este idioma la sección Blog no se imprime: el enlace no tendría destino.
    if ( ! dimsop_has_blog_posts() ) {
        $items = array_values( array_filter( $items, function ( $item ) {
            return false === strpos( $item['url'], '#blog' );
        } ) );
    }
    return $items;
}

/**
 * Idiomas para el selector del header, con los datos de Polylang.
 *
 * Muestra siempre los dos idiomas: si la página actual no tiene traducción,
 * el enlace lleva a la portada de ese idioma (Polylang lo resuelve así con
 * hide_if_no_translation = 0). Sin Polylang devuelve un array vacío y el
 * selector no se imprime.
 *
 * @return array<int, array{slug: string, name: string, w3c: string, url: string, current: bool, no_translation: bool}>
 */
function dimsop_get_language_switcher() {
    if ( ! function_exists( 'pll_the_languages' ) ) {
        return array();
    }

    $raw = pll_the_languages( array(
        'raw'                    => 1,
        'hide_if_empty'          => 0, // el inglés aún no tiene entradas propias
        'hide_if_no_translation' => 0,
        'hide_current'           => 0,
    ) );
    if ( ! is_array( $raw ) ) {
        return array();
    }

    $languages = array();
    foreach ( $raw as $lang ) {
        $languages[] = array(
            'slug'           => $lang['slug'],
            'name'           => $lang['name'],
            'w3c'            => str_replace( '_', '-', $lang['locale'] ),
            'url'            => $lang['url'],
            'current'        => ! empty( $lang['current_lang'] ),
            'no_translation' => ! empty( $lang['no_translation'] ),
        );
    }
    return $languages;
}

/**
 * Imprime el menú principal (desktop o móvil).
 *
 * @param string $menu_class Clases del <ul>.
 * @param string $extra_html <li> adicionales al final del menú (ya escapados).
 */
function dimsop_primary_menu( $menu_class = 'nav', $extra_html = '' ) {
    if ( has_nav_menu( 'primary' ) ) {
        wp_nav_menu( array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => $menu_class,
            'fallback_cb'    => false,
            'depth'          => 2,
            'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s' . $extra_html . '</ul>',
        ) );
        return;
    }

    echo '<ul class="' . esc_attr( $menu_class ) . '">';
    foreach ( dimsop_get_fallback_menu_items() as $item ) {
        printf(
            '<li class="nav-item"><a class="nav-link" href="%1$s">%2$s</a></li>',
            esc_url( $item['url'] ),
            esc_html( $item['label'] )
        );
    }
    echo $extra_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML construido y escapado por el llamador.
    echo '</ul>';
}
