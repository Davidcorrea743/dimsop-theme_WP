<?php
/**
 * Página no encontrada (404)
 *
 * WordPress ya responde con código 404 al cargar esta plantilla.
 *
 * @package Dimsop
 */

defined( 'ABSPATH' ) || exit;

get_header();

$dimsop_actions = sprintf(
    '<div class="d-flex flex-wrap justify-content-center gap-3 mb-4"><a class="dimsop-button dimsop-button--lg" href="%1$s"><i class="fa fa-home" aria-hidden="true"></i> %2$s</a><a class="dimsop-button dimsop-button--lg dimsop-button--outline dimsop-page-hero-outline" href="%3$s">%4$s</a></div>',
    esc_url( dimsop_home_url( '/' ) ),
    esc_html__( 'Ir al inicio', 'dimsop' ),
    esc_url( dimsop_home_url( '/#contacto' ) ),
    esc_html__( 'Contáctanos', 'dimsop' )
);
$dimsop_actions .= '<div class="dimsop-page-hero-search mx-auto">' . get_search_form( array( 'echo' => false ) ) . '</div>';
?>

<main id="primary" class="site-main dimsop-error-404">
    <?php
    get_template_part( 'template-parts/page-hero', null, array(
        'kicker'  => '404',
        'title'   => __( 'Página no encontrada', 'dimsop' ),
        'lead'    => __( 'Esta página no existe o cambió de dirección. Puedes volver al inicio, buscar lo que necesitas o escribirnos.', 'dimsop' ),
        'actions' => $dimsop_actions,
    ) );
    ?>

    <section class="dimsop-section dimsop-section-alt pt-5">
        <div class="container">
            <h2 class="dimsop-h2 text-center mb-4"><?php esc_html_e( 'Quizás te interese', 'dimsop' ); ?></h2>
            <div class="row g-4 justify-content-center">
                <?php
                $dimsop_links = array(
                    array( 'url' => dimsop_home_url( '/#nosotros' ), 'icon' => 'fa-building-o', 'title' => __( 'Nosotros', 'dimsop' ), 'text' => __( 'Quiénes somos, misión, visión y valores.', 'dimsop' ) ),
                    array( 'url' => dimsop_home_url( '/#unidades' ), 'icon' => 'fa-th-large', 'title' => __( 'Unidades de negocio', 'dimsop' ), 'text' => __( 'Nube, redes, ciberseguridad, IA y más.', 'dimsop' ) ),
                    // Sin entradas en este idioma, la tercera tarjeta lleva a Contacto en vez del Blog.
                    dimsop_has_blog_posts()
                        ? array( 'url' => dimsop_home_url( '/#blog' ), 'icon' => 'fa-newspaper-o', 'title' => __( 'Blog', 'dimsop' ), 'text' => __( 'Actualidad y tendencias tecnológicas.', 'dimsop' ) )
                        : array( 'url' => dimsop_home_url( '/#contacto' ), 'icon' => 'fa-envelope-o', 'title' => __( 'Contacto', 'dimsop' ), 'text' => __( 'Escríbenos y te responderemos a la brevedad.', 'dimsop' ) ),
                );
                foreach ( $dimsop_links as $dimsop_link ) :
                    ?>
                    <div class="col-sm-6 col-lg-4">
                        <a class="dimsop-ui-card dimsop-link-card" href="<?php echo esc_url( $dimsop_link['url'] ); ?>">
                            <span class="dimsop-ui-card-icon"><i class="fa <?php echo esc_attr( $dimsop_link['icon'] ); ?>" aria-hidden="true"></i></span>
                            <span class="dimsop-ui-card-title"><?php echo esc_html( $dimsop_link['title'] ); ?></span>
                            <span class="dimsop-ui-card-text"><?php echo esc_html( $dimsop_link['text'] ); ?></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
