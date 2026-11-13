<?php
/**
 * Buscador del sitio (lo usan get_search_form(), el 404 y los resultados)
 *
 * @package Dimsop
 */

defined( 'ABSPATH' ) || exit;

$dimsop_search_id = wp_unique_id( 'dimsop-search-' );
?>
<form role="search" method="get" class="dimsop-search-form" action="<?php echo esc_url( dimsop_home_url( '/' ) ); ?>">
    <label class="visually-hidden" for="<?php echo esc_attr( $dimsop_search_id ); ?>"><?php esc_html_e( 'Buscar en el sitio', 'dimsop' ); ?></label>
    <i class="fa fa-search dimsop-search-form-icon" aria-hidden="true"></i>
    <input class="dimsop-input dimsop-search-form-input" id="<?php echo esc_attr( $dimsop_search_id ); ?>" type="search" name="s"
           value="<?php echo esc_attr( get_search_query() ); ?>" maxlength="100"
           placeholder="<?php esc_attr_e( 'Buscar en el sitio…', 'dimsop' ); ?>">
    <button class="dimsop-button dimsop-search-form-button" type="submit"><?php esc_html_e( 'Buscar', 'dimsop' ); ?></button>
</form>
