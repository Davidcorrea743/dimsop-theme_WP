<?php
/**
 * Sección: Unidades de Negocio (cuadrícula bento)
 *
 * Lee el CPT dimsop_unit. La última unidad por orden (Desarrollo e IA) se destaca
 * en la celda grande; las dos últimas del resto ocupan celdas anchas.
 *
 * @package Dimsop
 */

defined( 'ABSPATH' ) || exit;

$dimsop_units = dimsop_get_units( 8 )->posts;

if ( ! $dimsop_units ) {
    return;
}

// La unidad destacada pasa al inicio para ocupar la celda grande del bento.
$dimsop_featured = array_pop( $dimsop_units );
$dimsop_wide_from = max( 0, count( $dimsop_units ) - 2 );
?>

<section id="unidades" class="dimsop-section dimsop-unidades">
    <div class="container">
        <div class="dimsop-section-head text-center mb-5">
            <span class="dimsop-badge mb-3"><span class="dimsop-badge-dot"></span> <?php esc_html_e( 'Soluciones TIC', 'dimsop' ); ?></span>
            <h2 class="dimsop-h2 mb-2"><?php esc_html_e( 'Unidades de Negocio', 'dimsop' ); ?></h2>
            <p class="dimsop-section-lead mx-auto">
                <?php esc_html_e( 'Siete áreas especializadas que cubren de punta a punta la infraestructura tecnológica de tu empresa.', 'dimsop' ); ?>
            </p>
        </div>

        <div class="dimsop-bento">
            <?php $dimsop_icon = get_post_meta( $dimsop_featured->ID, '_dimsop_unit_icon', true ) ?: 'fa-cube'; ?>
            <article class="dimsop-ui-card dimsop-ui-card--featured dimsop-bento-featured">
                <span class="dimsop-badge dimsop-badge--primary mb-4"><?php esc_html_e( 'Innovación', 'dimsop' ); ?></span>
                <span class="dimsop-ui-card-icon"><i class="fa <?php echo esc_attr( $dimsop_icon ); ?>" aria-hidden="true"></i></span>
                <h3 class="dimsop-ui-card-title"><?php echo esc_html( get_the_title( $dimsop_featured ) ); ?></h3>
                <p class="dimsop-ui-card-text"><?php echo esc_html( get_the_excerpt( $dimsop_featured ) ); ?></p>
                <a href="#contacto" class="dimsop-button mt-4">
                    <?php esc_html_e( 'Conversemos', 'dimsop' ); ?> <i class="fa fa-arrow-right dimsop-button-arrow" aria-hidden="true"></i>
                </a>
            </article>

            <?php foreach ( $dimsop_units as $dimsop_i => $dimsop_unit ) : ?>
                <?php $dimsop_icon = get_post_meta( $dimsop_unit->ID, '_dimsop_unit_icon', true ) ?: 'fa-cube'; ?>
                <article class="dimsop-ui-card<?php echo $dimsop_i >= $dimsop_wide_from ? ' dimsop-bento-wide' : ''; ?>">
                    <span class="dimsop-ui-card-icon"><i class="fa <?php echo esc_attr( $dimsop_icon ); ?>" aria-hidden="true"></i></span>
                    <h3 class="dimsop-ui-card-title"><?php echo esc_html( get_the_title( $dimsop_unit ) ); ?></h3>
                    <p class="dimsop-ui-card-text"><?php echo esc_html( get_the_excerpt( $dimsop_unit ) ); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
