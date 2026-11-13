<?php
/**
 * Idiomas: integración del tema con Polylang.
 *
 * - Las unidades de negocio se traducen (tienen título y descripción).
 * - Aliados y proyectos NO: son logos y nombres de clientes, iguales en los dos idiomas.
 * - Prospectos y postulaciones tampoco: son registros internos.
 *
 * @package Dimsop
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hace traducibles las unidades de negocio. Al no ser un tipo de contenido
 * público, Polylang no lo ofrece en sus ajustes: se registra aquí para que
 * staging y producción queden igual sin tocar la configuración a mano.
 *
 * @param array<string, string> $post_types Tipos traducibles.
 * @param bool                  $is_settings True en la pantalla de ajustes de Polylang.
 * @return array<string, string>
 */
function dimsop_pll_post_types( $post_types, $is_settings ) {
    if ( ! $is_settings ) {
        $post_types['dimsop_unit'] = 'dimsop_unit';
    }
    return $post_types;
}
add_filter( 'pll_get_post_types', 'dimsop_pll_post_types', 10, 2 );

/**
 * ¿Hay entradas publicadas en el idioma actual?
 *
 * La sección Blog de la portada no aparece sin entradas; sus enlaces (menú,
 * footer, 404, búsqueda) se ocultan igual para no llevar a un ancla vacía.
 *
 * @return bool
 */
function dimsop_has_blog_posts() {
    static $has = null;
    if ( null === $has ) {
        $has = (bool) get_posts( array(
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'numberposts'         => 1,
            'fields'              => 'ids',
            'ignore_sticky_posts' => true,
        ) );
    }
    return $has;
}

/**
 * Añade hreflang="x-default" (versión para idiomas que no son ES ni EN):
 * apunta al español, el idioma por defecto del sitio.
 *
 * @param array<string, string> $hreflangs Código hreflang => URL.
 * @return array<string, string>
 */
function dimsop_hreflang_x_default( $hreflangs ) {
    if ( function_exists( 'pll_default_language' ) && ! isset( $hreflangs['x-default'] ) ) {
        $default = pll_default_language( 'slug' );
        if ( isset( $hreflangs[ $default ] ) ) {
            $hreflangs['x-default'] = $hreflangs[ $default ];
        }
    }
    return $hreflangs;
}
add_filter( 'pll_rel_hreflang_attributes', 'dimsop_hreflang_x_default' );

/**
 * Añade la portada de cada idioma no predeterminado (/en/) al sitemap de páginas
 * de Yoast, que solo incluye la portada principal.
 *
 * @param string $content XML de URLs adicionales.
 * @return string
 */
function dimsop_sitemap_language_homes( $content ) {
    if ( ! function_exists( 'pll_languages_list' ) || ! function_exists( 'pll_home_url' ) ) {
        return $content;
    }
    $default = pll_default_language( 'slug' );
    foreach ( pll_languages_list( array( 'fields' => 'slug' ) ) as $slug ) {
        if ( $slug !== $default ) {
            $content .= "\t<url>\n\t\t<loc>" . esc_url( pll_home_url( $slug ) ) . "</loc>\n\t</url>\n";
        }
    }
    return $content;
}
add_filter( 'wpseo_sitemap_page_content', 'dimsop_sitemap_language_homes' );
