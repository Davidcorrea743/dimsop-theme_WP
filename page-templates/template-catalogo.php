<?php
/**
 * Template Name: Catálogo (próximamente)
 *
 * Página provisional mientras se define el catálogo: aviso "Próximamente",
 * botón para pedir información y una vista previa con tarjetas skeleton.
 * No se indexa en buscadores hasta que tenga contenido real.
 *
 * @package Dimsop
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'wp_robots', 'wp_robots_no_robots' );

get_header();

$dimsop_actions = sprintf(
    '<div class="d-flex flex-wrap justify-content-center gap-3"><a class="dimsop-button dimsop-button--lg" href="%1$s">%2$s <i class="fa fa-arrow-right dimsop-button-arrow" aria-hidden="true"></i></a><a class="dimsop-button dimsop-button--lg dimsop-button--outline dimsop-page-hero-outline" href="%3$s">%4$s</a></div>',
    esc_url( home_url( '/#contacto' ) ),
    esc_html__( 'Solicitar información', 'dimsop' ),
    esc_url( home_url( '/#unidades' ) ),
    esc_html__( 'Ver unidades de negocio', 'dimsop' )
);
?>

<main id="primary" class="site-main dimsop-catalogo">
    <?php
    get_template_part( 'template-parts/page-hero', null, array(
        'badge'   => __( 'Próximamente', 'dimsop' ),
        'title'   => __( 'Catálogo de soluciones', 'dimsop' ),
        'lead'    => __( 'Estamos preparando nuestro catálogo de productos y servicios. Mientras tanto, escríbenos y te enviaremos la información que necesitas.', 'dimsop' ),
        'actions' => $dimsop_actions,
    ) );
    ?>

    <section class="dimsop-section dimsop-section-alt pt-5" aria-label="<?php esc_attr_e( 'Vista previa del catálogo, en preparación', 'dimsop' ); ?>">
        <div class="container">
            <p class="dimsop-skeleton-note text-center mb-4">
                <i class="fa fa-clock-o" aria-hidden="true"></i> <?php esc_html_e( 'Contenido en preparación', 'dimsop' ); ?>
            </p>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4" aria-hidden="true">
                <?php for ( $dimsop_i = 0; $dimsop_i < 6; $dimsop_i++ ) : ?>
                    <div class="col">
                        <div class="dimsop-skeleton-card">
                            <span class="dimsop-skeleton dimsop-skeleton--media"></span>
                            <span class="dimsop-skeleton dimsop-skeleton--chip"></span>
                            <span class="dimsop-skeleton dimsop-skeleton--title"></span>
                            <span class="dimsop-skeleton dimsop-skeleton--line"></span>
                            <span class="dimsop-skeleton dimsop-skeleton--line dimsop-skeleton--short"></span>
                            <span class="dimsop-skeleton dimsop-skeleton--button"></span>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
