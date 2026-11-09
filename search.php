<?php
/**
 * Resultados de búsqueda
 *
 * Tarjetas como las del blog de la portada, con paginación. No se indexa.
 *
 * @package Dimsop
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'wp_robots', 'wp_robots_no_robots' );

get_header();

global $wp_query;
$dimsop_total = (int) $wp_query->found_posts;
$dimsop_query = get_search_query();
?>

<main id="primary" class="site-main dimsop-search-results">
    <?php
    get_template_part( 'template-parts/page-hero', null, array(
        'badge'   => __( 'Búsqueda', 'dimsop' ),
        'title'   => '' !== $dimsop_query
            /* translators: %s: términos buscados */
            ? sprintf( __( 'Resultados para "%s"', 'dimsop' ), $dimsop_query )
            : __( 'Buscar en el sitio', 'dimsop' ),
        'lead'    => $dimsop_total
            /* translators: %d: cantidad de resultados */
            ? sprintf( _n( 'Encontramos %d resultado.', 'Encontramos %d resultados.', $dimsop_total, 'dimsop' ), $dimsop_total )
            : '',
        'actions' => '<div class="dimsop-page-hero-search mx-auto">' . get_search_form( array( 'echo' => false ) ) . '</div>',
        'compact' => true,
    ) );
    ?>

    <section class="dimsop-section pt-5">
        <div class="container">
            <?php if ( have_posts() ) : ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php
                    while ( have_posts() ) :
                        the_post();
                        $dimsop_type = get_post_type_object( get_post_type() );
                        ?>
                        <div class="col">
                            <article class="dimsop-ui-card dimsop-post-card">
                                <a class="dimsop-post-cover" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <?php the_post_thumbnail( 'dimsop-blog-card', array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
                                    <?php else : ?>
                                        <i class="fa <?php echo 'page' === get_post_type() ? 'fa-file-text-o' : 'fa-newspaper-o'; ?>" aria-hidden="true"></i>
                                    <?php endif; ?>
                                </a>
                                <div class="dimsop-post-body">
                                    <span class="dimsop-badge"><?php echo esc_html( 'post' === get_post_type() ? __( 'Blog', 'dimsop' ) : $dimsop_type->labels->singular_name ); ?></span>
                                    <h2 class="dimsop-ui-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                    <p class="dimsop-ui-card-text"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
                                    <a class="dimsop-button dimsop-button--outline dimsop-button--sm mt-auto" href="<?php the_permalink(); ?>">
                                        <?php esc_html_e( 'Ver más', 'dimsop' ); ?> <i class="fa fa-arrow-right dimsop-button-arrow" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </article>
                        </div>
                    <?php endwhile; ?>
                </div>

                <?php
                the_posts_pagination( array(
                    'class'              => 'dimsop-pagination',
                    'mid_size'           => 1,
                    'prev_text'          => '<i class="fa fa-chevron-left" aria-hidden="true"></i><span class="visually-hidden">' . esc_html__( 'Anterior', 'dimsop' ) . '</span>',
                    'next_text'          => '<span class="visually-hidden">' . esc_html__( 'Siguiente', 'dimsop' ) . '</span><i class="fa fa-chevron-right" aria-hidden="true"></i>',
                    'screen_reader_text' => __( 'Más resultados', 'dimsop' ),
                ) );
                ?>
            <?php else : ?>
                <div class="dimsop-empty-state text-center mx-auto">
                    <span class="dimsop-ui-card-icon mx-auto"><i class="fa fa-search" aria-hidden="true"></i></span>
                    <h2 class="dimsop-h3 mt-3"><?php esc_html_e( 'No encontramos resultados', 'dimsop' ); ?></h2>
                    <p class="dimsop-section-lead mx-auto"><?php esc_html_e( 'Prueba con otras palabras o visita alguna de estas secciones:', 'dimsop' ); ?></p>
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <a class="dimsop-button dimsop-button--outline dimsop-button--sm" href="<?php echo esc_url( home_url( '/#unidades' ) ); ?>"><?php esc_html_e( 'Unidades de negocio', 'dimsop' ); ?></a>
                        <a class="dimsop-button dimsop-button--outline dimsop-button--sm" href="<?php echo esc_url( home_url( '/#blog' ) ); ?>"><?php esc_html_e( 'Blog', 'dimsop' ); ?></a>
                        <a class="dimsop-button dimsop-button--sm" href="<?php echo esc_url( home_url( '/#contacto' ) ); ?>"><?php esc_html_e( 'Contáctanos', 'dimsop' ); ?></a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
get_footer();
