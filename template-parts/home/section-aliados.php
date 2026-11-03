<?php
/**
 * Sección: Aliados Tecnológicos (carrusel de logos en bucle)
 *
 * Lee el CPT dimsop_partner. La pista se imprime dos veces para el bucle infinito
 * (la copia va con aria-hidden). initMarquees() añade botones, arrastre e inercia;
 * sin JS queda la animación CSS y los botones siguen ocultos.
 *
 * @package Dimsop
 */

defined( 'ABSPATH' ) || exit;

$dimsop_partners = dimsop_get_partners()->posts;

if ( ! $dimsop_partners ) {
    return;
}
?>

<section id="aliados" class="dimsop-section dimsop-aliados">
    <div class="container">
        <div class="dimsop-section-head text-center mb-4">
            <span class="dimsop-badge mb-3"><span class="dimsop-badge-dot"></span> <?php esc_html_e( 'Ecosistema de marcas', 'dimsop' ); ?></span>
            <h2 class="dimsop-h2 mb-2"><?php esc_html_e( 'Aliados Tecnológicos', 'dimsop' ); ?></h2>
            <p class="dimsop-section-lead mx-auto">
                <?php esc_html_e( 'Trabajamos con los fabricantes líderes del mercado para ofrecer soluciones certificadas y con respaldo.', 'dimsop' ); ?>
            </p>
        </div>
    </div>

    <div class="dimsop-marquee" data-marquee data-speed="60" role="region" aria-label="<?php esc_attr_e( 'Logos de aliados tecnológicos de Dimsop', 'dimsop' ); ?>">
        <button class="dimsop-marquee-btn dimsop-marquee-btn--prev" type="button" aria-label="<?php esc_attr_e( 'Mover el carrusel hacia atrás', 'dimsop' ); ?>" hidden><i class="fa fa-chevron-left" aria-hidden="true"></i></button>
        <div class="dimsop-marquee-container">
        <div class="dimsop-marquee-track">
            <?php foreach ( array( false, true ) as $dimsop_is_copy ) : ?>
                <?php foreach ( $dimsop_partners as $dimsop_partner ) : ?>
                    <div class="dimsop-partner-item"<?php echo $dimsop_is_copy ? ' aria-hidden="true"' : ''; ?>>
                        <?php
                        echo get_the_post_thumbnail(
                            $dimsop_partner,
                            'dimsop-partner-logo',
                            array(
                                'class'   => 'dimsop-partner-logo',
                                'alt'     => $dimsop_is_copy ? '' : get_the_title( $dimsop_partner ),
                                'loading' => 'lazy',
                            )
                        );
                        ?>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
        </div>
        <button class="dimsop-marquee-btn dimsop-marquee-btn--next" type="button" aria-label="<?php esc_attr_e( 'Mover el carrusel hacia adelante', 'dimsop' ); ?>" hidden><i class="fa fa-chevron-right" aria-hidden="true"></i></button>
    </div>
</section>
