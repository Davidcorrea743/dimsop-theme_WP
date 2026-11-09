<?php
/**
 * Páginas normales (política de privacidad y cualquier página creada en el panel)
 *
 * @package Dimsop
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main dimsop-page">
    <?php
    while ( have_posts() ) :
        the_post();

        get_template_part( 'template-parts/page-hero', null, array(
            'title'   => get_the_title(),
            'lead'    => has_excerpt() ? get_the_excerpt() : '',
            'compact' => true,
        ) );
        ?>

        <section class="dimsop-section pt-5">
            <div class="container">
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'dimsop-prose mx-auto' ); ?>>
                    <?php
                    the_content();

                    wp_link_pages( array(
                        'before' => '<nav class="dimsop-page-links">' . esc_html__( 'Páginas:', 'dimsop' ),
                        'after'  => '</nav>',
                    ) );
                    ?>
                    <p class="dimsop-prose-updated">
                        <?php
                        printf(
                            /* translators: %s: fecha de la última actualización */
                            esc_html__( 'Última actualización: %s', 'dimsop' ),
                            '<time datetime="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . esc_html( get_the_modified_date() ) . '</time>'
                        );
                        ?>
                    </p>
                </article>
            </div>
        </section>
        <?php
    endwhile;
    ?>
</main>

<?php
get_footer();
