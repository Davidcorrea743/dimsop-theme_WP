<?php
/**
 * Sección: Aliados Tecnológicos (carrusel de logos en bucle)
 *
 * Lee el CPT dimsop_partner. La pista se imprime dos veces para el bucle infinito
 * (la copia va con aria-hidden). Botones, arrastre e inercia llegan el Día 7.
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

    <div class="dimsop-marquee-container" role="region" aria-label="<?php esc_attr_e( 'Logos de aliados tecnológicos de Dimsop', 'dimsop' ); ?>">
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
</section>
