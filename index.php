<?php
/**
 * Main template file (Fallback)
 *
 * @package Dimsop
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main dimsop-main-fallback py-5">
    <div class="container">
        <?php
        if ( have_posts() ) :
            while ( have_posts() ) :
                the_post();
                the_content();
            endwhile;
        else :
            ?>
            <div class="text-center py-5">
                <h1 class="dimsop-h2 mb-3"><?php esc_html_e( 'Contenido no encontrado', 'dimsop' ); ?></h1>
                <p class="text-muted"><?php esc_html_e( 'Lo sentimos, no hay contenido disponible en esta sección.', 'dimsop' ); ?></p>
            </div>
            <?php
        endif;
        ?>
    </div>
</main>

<?php
get_footer();
