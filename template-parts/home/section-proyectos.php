<?php
/**
 * Sección: Proyectos Referenciales (carrusel de tarjetas en bucle)
 *
 * Lee el CPT dimsop_project: logo (imagen destacada), cliente y descripción
 * (extracto). Mismo bucle doble que Aliados; los controles llegan el Día 7.
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

    <div class="dimsop-marquee-container dimsop-marquee--slow" role="region" aria-label="<?php esc_attr_e( 'Proyectos referenciales de Dimsop', 'dimsop' ); ?>">
        <div class="dimsop-marquee-track">
            <?php foreach ( array( false, true ) as $dimsop_is_copy ) : ?>
                <?php foreach ( $dimsop_projects as $dimsop_project ) : ?>
                    <?php $dimsop_desc = get_the_excerpt( $dimsop_project ); ?>
                    <article class="dimsop-ui-card dimsop-project-card"<?php echo $dimsop_is_copy ? ' aria-hidden="true"' : ''; ?>>
                        <div class="dimsop-project-logo">
                            <?php
                            echo get_the_post_thumbnail(
                                $dimsop_project,
                                'dimsop-partner-logo',
                                array(
                                    'alt'     => $dimsop_is_copy ? '' : get_the_title( $dimsop_project ),
                                    'loading' => 'lazy',
                                )
                            );
                            ?>
                        </div>
                        <h3 class="dimsop-project-name"><?php echo esc_html( get_the_title( $dimsop_project ) ); ?></h3>
                        <?php if ( $dimsop_desc ) : ?>
                            <p class="dimsop-ui-card-text"><?php echo esc_html( $dimsop_desc ); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
