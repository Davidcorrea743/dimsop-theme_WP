<?php
/**
 * Sección: Proyectos Referenciales (carrusel de tarjetas en bucle)
 *
 * Lee el CPT dimsop_project y muestra solo el logo (imagen destacada) y el nombre
 * de la empresa. Mismo bucle doble y controles que Aliados (initMarquees).
 *
 * @package Dimsop
 */

defined( 'ABSPATH' ) || exit;

$dimsop_projects = dimsop_get_projects()->posts;

if ( ! $dimsop_projects ) {
    return;
}
?>

<section id="proyectos" class="dimsop-section dimsop-section-alt dimsop-proyectos">
    <div class="container">
        <div class="dimsop-section-head text-center mb-4">
            <span class="dimsop-badge mb-3"><span class="dimsop-badge-dot"></span> <?php esc_html_e( 'Casos de éxito', 'dimsop' ); ?></span>
            <h2 class="dimsop-h2 mb-2"><?php esc_html_e( 'Proyectos Referenciales', 'dimsop' ); ?></h2>
            <p class="dimsop-section-lead mx-auto">
                <?php esc_html_e( 'Empresas de banca, salud, energía, telecomunicaciones y retail en la región confían en nosotros.', 'dimsop' ); ?>
            </p>
        </div>
    </div>

    <div class="dimsop-marquee dimsop-marquee--slow" data-marquee data-speed="45" role="region" aria-label="<?php esc_attr_e( 'Proyectos referenciales de Dimsop', 'dimsop' ); ?>">
        <button class="dimsop-marquee-btn dimsop-marquee-btn--prev" type="button" aria-label="<?php esc_attr_e( 'Mover el carrusel hacia atrás', 'dimsop' ); ?>" hidden><i class="fa fa-chevron-left" aria-hidden="true"></i></button>
        <div class="dimsop-marquee-container">
        <div class="dimsop-marquee-track">
            <?php foreach ( array( false, true ) as $dimsop_is_copy ) : ?>
                <?php foreach ( $dimsop_projects as $dimsop_project ) : ?>
                    <article class="dimsop-ui-card dimsop-project-card"<?php echo $dimsop_is_copy ? ' aria-hidden="true"' : ''; ?>>
                        <div class="dimsop-project-logo">
                            <?php
                            echo get_the_post_thumbnail(
                                $dimsop_project,
                                'dimsop-partner-logo',
                                array(
                                    'alt'     => '', // El nombre de la empresa va justo debajo.
                                    'loading' => 'lazy',
                                )
                            );
                            ?>
                        </div>
                        <h3 class="dimsop-project-name"><?php echo esc_html( get_the_title( $dimsop_project ) ); ?></h3>
                    </article>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
        </div>
        <button class="dimsop-marquee-btn dimsop-marquee-btn--next" type="button" aria-label="<?php esc_attr_e( 'Mover el carrusel hacia adelante', 'dimsop' ); ?>" hidden><i class="fa fa-chevron-right" aria-hidden="true"></i></button>
    </div>
</section>
