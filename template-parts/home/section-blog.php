<?php
/**
 * Sección: Blog / Actualidad (3 últimas entradas)
 *
 * El botón "Ver todas" solo aparece si hay una página de entradas asignada
 * en Ajustes › Lectura. Las plantillas de listado y entrada llegan el Día 13.
 *
 * @package Dimsop
 */

defined( 'ABSPATH' ) || exit;

$dimsop_posts = get_posts( array(
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'numberposts'         => 3,
    'ignore_sticky_posts' => true,
) );

if ( ! $dimsop_posts ) {
    return;
}

$dimsop_blog_page = (int) get_option( 'page_for_posts' );
?>

<section id="blog" class="dimsop-section dimsop-blog-section">
    <div class="container">
        <div class="dimsop-section-head text-center mb-5">
            <span class="dimsop-badge mb-3"><span class="dimsop-badge-dot"></span> <?php esc_html_e( 'Blog', 'dimsop' ); ?></span>
            <h2 class="dimsop-h2 mb-2"><?php esc_html_e( 'Actualidad y Tendencias Tecnológicas', 'dimsop' ); ?></h2>
            <p class="dimsop-section-lead mx-auto">
                <?php esc_html_e( 'Ideas y novedades sobre nube, ciberseguridad, redes e inteligencia artificial.', 'dimsop' ); ?>
            </p>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 justify-content-center">
            <?php foreach ( $dimsop_posts as $dimsop_post ) : ?>
                <?php $dimsop_permalink = get_permalink( $dimsop_post ); ?>
                <div class="col">
                    <article class="dimsop-ui-card dimsop-post-card">
                        <a class="dimsop-post-cover" href="<?php echo esc_url( $dimsop_permalink ); ?>" tabindex="-1" aria-hidden="true">
                            <?php if ( has_post_thumbnail( $dimsop_post ) ) : ?>
                                <?php echo get_the_post_thumbnail( $dimsop_post, 'dimsop-blog-card', array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
                            <?php else : ?>
                                <i class="fa fa-newspaper-o" aria-hidden="true"></i>
                            <?php endif; ?>
                        </a>
                        <div class="dimsop-post-body">
                            <time class="dimsop-post-date" datetime="<?php echo esc_attr( get_the_date( 'c', $dimsop_post ) ); ?>"><?php echo esc_html( get_the_date( '', $dimsop_post ) ); ?></time>
                            <h3 class="dimsop-ui-card-title"><a href="<?php echo esc_url( $dimsop_permalink ); ?>"><?php echo esc_html( get_the_title( $dimsop_post ) ); ?></a></h3>
                            <p class="dimsop-ui-card-text"><?php echo esc_html( wp_trim_words( get_the_excerpt( $dimsop_post ), 20 ) ); ?></p>
                            <a class="dimsop-button dimsop-button--outline dimsop-button--sm mt-auto" href="<?php echo esc_url( $dimsop_permalink ); ?>">
                                <?php esc_html_e( 'Leer más', 'dimsop' ); ?> <i class="fa fa-arrow-right dimsop-button-arrow" aria-hidden="true"></i>
                            </a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ( $dimsop_blog_page ) : ?>
            <div class="text-center mt-5">
                <a class="dimsop-button dimsop-button--secondary" href="<?php echo esc_url( get_permalink( $dimsop_blog_page ) ); ?>">
                    <?php esc_html_e( 'Ver todas las publicaciones', 'dimsop' ); ?> <i class="fa fa-arrow-right dimsop-button-arrow" aria-hidden="true"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>
