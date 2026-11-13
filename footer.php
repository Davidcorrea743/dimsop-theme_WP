<?php
/**
 * Pie de página del Tema Dimsop
 *
 * - Cuatro columnas: Marca, Navegación, Soluciones (unidades de negocio) y Sedes.
 * - Las sedes son tarjetas compactas; el mapa de Google se carga solo si el
 *   visitante pulsa "Ver mapa" (sin peso ni cookies de Google por defecto).
 * - Barra legal: copyright, RIF, privacidad, redes y "Volver arriba".
 *
 * @package Dimsop
 */

defined( 'ABSPATH' ) || exit;

$dimsop_whatsapp = dimsop_sanitize_phone( dimsop_get_option( 'whatsapp_number', '582128195400' ) );
$dimsop_email    = dimsop_get_option( 'contact_email' );

// Unidades de negocio para la columna Soluciones (no tienen página propia: llevan a la sección).
$dimsop_footer_units = dimsop_get_units( 8 )->posts;

$dimsop_sedes = array(
    array(
        'id'      => 've',
        'title'   => __( 'Venezuela', 'dimsop' ),
        'city'    => __( 'Caracas', 'dimsop' ),
        'address' => dimsop_get_option( 'address_ve' ),
        'phone'   => dimsop_get_option( 'contact_phone_ve' ),
        'map'     => dimsop_map_links( dimsop_get_option( 'maps_query_ve' ) ),
        'url'     => dimsop_get_option( 'maps_url_ve' ),
    ),
    array(
        'id'      => 'us',
        'title'   => __( 'Estados Unidos', 'dimsop' ),
        'city'    => __( 'Miami', 'dimsop' ),
        'address' => dimsop_get_option( 'address_us' ),
        'phone'   => dimsop_get_option( 'contact_phone_us' ),
        'map'     => dimsop_map_links( dimsop_get_option( 'maps_query_us' ) ),
        'url'     => dimsop_get_option( 'maps_url_us' ),
    ),
);
?>

<footer id="dimsop_footer" class="dimsop-footer">
    <div class="container dimsop-footer-main">
        <div class="row gy-4 gy-lg-5 gx-4">
            <!-- Marca -->
            <div class="col-12 col-lg-4 dimsop-footer-col dimsop-footer-brand">
                <a class="dimsop-footer-logo" href="<?php echo esc_url( dimsop_home_url( '/' ) ); ?>">
                    <img src="<?php echo esc_url( dimsop_asset_url( 'images/logos/logo-footer-white.webp' ) ); ?>"
                         alt="<?php bloginfo( 'name' ); ?>" class="dimsop-footer-logo-img" width="480" height="178" loading="lazy">
                </a>
                <p class="dimsop-footer-tagline"><?php esc_html_e( 'Soluciones TIC de Vanguardia', 'dimsop' ); ?></p>
                <p class="dimsop-footer-about"><?php esc_html_e( 'Integramos nube, redes, ciberseguridad e inteligencia artificial para que tu empresa crezca con tecnología confiable.', 'dimsop' ); ?></p>
                <span class="dimsop-footer-iso">
                    <i class="fa fa-certificate" aria-hidden="true"></i>
                    <?php esc_html_e( 'Rumbo a la certificación ISO 9001', 'dimsop' ); ?>
                </span>
                <?php if ( $dimsop_email ) : ?>
                    <a class="dimsop-footer-email" href="<?php echo esc_attr( 'mailto:' . $dimsop_email ); ?>">
                        <i class="fa fa-envelope-o" aria-hidden="true"></i> <?php echo esc_html( $dimsop_email ); ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Navegación -->
            <nav class="col-6 col-lg-2 dimsop-footer-col" aria-labelledby="dimsop-footer-nav-title">
                <h2 id="dimsop-footer-nav-title" class="dimsop-footer-title"><?php esc_html_e( 'Navegación', 'dimsop' ); ?></h2>
                <ul class="dimsop-footer-links">
                    <li><a href="<?php echo esc_url( dimsop_home_url( '/' ) ); ?>"><?php esc_html_e( 'Inicio', 'dimsop' ); ?></a></li>
                    <li><a href="<?php echo esc_url( dimsop_home_url( '/#nosotros' ) ); ?>"><?php esc_html_e( 'Nosotros', 'dimsop' ); ?></a></li>
                    <li><a href="<?php echo esc_url( dimsop_page_url( 'catalogo' ) ); ?>"><?php esc_html_e( 'Catálogo', 'dimsop' ); ?></a></li>
                    <?php if ( dimsop_has_blog_posts() ) : ?>
                        <li><a href="<?php echo esc_url( dimsop_home_url( '/#blog' ) ); ?>"><?php esc_html_e( 'Blog', 'dimsop' ); ?></a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo esc_url( dimsop_home_url( '/#contacto' ) ); ?>"><?php esc_html_e( 'Contacto', 'dimsop' ); ?></a></li>
                    <li>
                        <a href="#carreras" data-bs-toggle="modal" data-bs-target="#dimsop-carreras-modal">
                            <?php esc_html_e( 'Forma parte de nuestro equipo', 'dimsop' ); ?>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Soluciones -->
            <?php if ( $dimsop_footer_units ) : ?>
                <nav class="col-6 col-lg-3 dimsop-footer-col" aria-labelledby="dimsop-footer-units-title">
                    <h2 id="dimsop-footer-units-title" class="dimsop-footer-title"><?php esc_html_e( 'Soluciones', 'dimsop' ); ?></h2>
                    <ul class="dimsop-footer-links">
                        <?php foreach ( $dimsop_footer_units as $dimsop_unit ) : ?>
                            <li><a href="<?php echo esc_url( dimsop_home_url( '/#unidades' ) ); ?>"><?php echo esc_html( get_the_title( $dimsop_unit ) ); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            <?php endif; ?>

            <!-- Sedes -->
            <div class="col-12 col-lg-3 dimsop-footer-col">
                <h2 class="dimsop-footer-title"><?php esc_html_e( 'Sedes', 'dimsop' ); ?></h2>
                <div class="dimsop-footer-sedes">
                    <?php foreach ( $dimsop_sedes as $dimsop_sede ) : ?>
                        <?php
                        $dimsop_embed = $dimsop_sede['map']['embed'];
                        // "Cómo llegar": la ficha de Google Maps indicada en Opciones; si no hay, la ruta a la ubicación.
                        $dimsop_directions = $dimsop_sede['url'] ? $dimsop_sede['url'] : $dimsop_sede['map']['directions'];
                        $dimsop_map_id = 'dimsop-footer-map-' . $dimsop_sede['id'];
                        ?>
                        <div class="dimsop-footer-sede">
                            <p class="dimsop-footer-sede-name">
                                <span class="dimsop-footer-sede-dot" aria-hidden="true"></span>
                                <?php echo esc_html( $dimsop_sede['title'] ); ?>
                                <span class="dimsop-footer-sede-city"><?php echo esc_html( $dimsop_sede['city'] ); ?></span>
                            </p>
                            <?php if ( $dimsop_sede['address'] ) : ?>
                                <address class="dimsop-footer-sede-address"><?php echo esc_html( $dimsop_sede['address'] ); ?></address>
                            <?php endif; ?>
                            <?php if ( $dimsop_sede['phone'] ) : ?>
                                <a class="dimsop-footer-sede-phone" href="<?php echo esc_attr( 'tel:' . dimsop_sanitize_phone( $dimsop_sede['phone'] ) ); ?>">
                                    <i class="fa fa-phone" aria-hidden="true"></i> <?php echo esc_html( $dimsop_sede['phone'] ); ?>
                                </a>
                            <?php endif; ?>
                            <div class="dimsop-footer-sede-actions">
                                <?php if ( $dimsop_directions ) : ?>
                                    <a href="<?php echo esc_url( $dimsop_directions ); ?>" target="_blank" rel="noopener noreferrer">
                                        <?php esc_html_e( 'Cómo llegar', 'dimsop' ); ?> <i class="fa fa-external-link" aria-hidden="true"></i>
                                        <span class="visually-hidden"><?php esc_html_e( '(se abre en una pestaña nueva)', 'dimsop' ); ?></span>
                                    </a>
                                <?php endif; ?>
                                <?php if ( $dimsop_embed ) : ?>
                                    <button type="button" class="dimsop-footer-map-toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $dimsop_map_id ); ?>" hidden>
                                        <span class="dimsop-footer-map-toggle-show"><?php esc_html_e( 'Ver mapa', 'dimsop' ); ?></span>
                                        <span class="dimsop-footer-map-toggle-hide"><?php esc_html_e( 'Ocultar mapa', 'dimsop' ); ?></span>
                                    </button>
                                <?php endif; ?>
                            </div>
                            <?php if ( $dimsop_embed ) : ?>
                                <div id="<?php echo esc_attr( $dimsop_map_id ); ?>" class="dimsop-footer-map-wrap" hidden
                                     data-src="<?php echo esc_url( $dimsop_embed ); ?>"
                                     data-title="<?php /* translators: %s: país de la sede */ echo esc_attr( sprintf( __( 'Mapa de la sede de %s', 'dimsop' ), $dimsop_sede['title'] ) ); ?>"></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra legal -->
    <div class="dimsop-footer-bottom">
        <div class="container dimsop-footer-bottom-inner">
            <p class="dimsop-footer-legal">
                &copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> Dimsop Technology.
                <?php esc_html_e( 'Todos los derechos reservados.', 'dimsop' ); ?>
                <?php if ( dimsop_get_option( 'rif_ve' ) ) : ?>
                    <span class="dimsop-footer-rif">RIF <?php echo esc_html( dimsop_get_option( 'rif_ve' ) ); ?></span>
                <?php endif; ?>
            </p>
            <div class="dimsop-footer-bottom-end">
                <a class="dimsop-footer-privacy" href="<?php echo esc_url( dimsop_page_url( 'politica-de-privacidad' ) ); ?>"><?php esc_html_e( 'Política de privacidad', 'dimsop' ); ?></a>
                <?php /* Día 11: aquí irá el selector de idioma ES/EN. */ ?>
                <div class="dimsop-social">
                    <a href="<?php echo esc_url( dimsop_get_option( 'linkedin_url' ) ); ?>"
                       target="_blank" rel="noopener noreferrer" class="dimsop-social-btn" aria-label="<?php esc_attr_e( 'LinkedIn (se abre en una pestaña nueva)', 'dimsop' ); ?>">
                        <i class="fa fa-linkedin" aria-hidden="true"></i>
                    </a>
                    <a href="<?php echo esc_url( dimsop_get_option( 'instagram_url' ) ); ?>"
                       target="_blank" rel="noopener noreferrer" class="dimsop-social-btn" aria-label="<?php esc_attr_e( 'Instagram (se abre en una pestaña nueva)', 'dimsop' ); ?>">
                        <i class="fa fa-instagram" aria-hidden="true"></i>
                    </a>
                </div>
                <a class="dimsop-back-to-top" href="#page-top" aria-label="<?php esc_attr_e( 'Volver arriba', 'dimsop' ); ?>">
                    <i class="fa fa-arrow-up" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
</footer>

<!-- Botón flotante persistente de WhatsApp -->
<?php if ( ! empty( $dimsop_whatsapp ) ) : ?>
    <a id="dimsop-whatsapp-float"
       href="<?php echo esc_url( 'https://wa.me/' . $dimsop_whatsapp ); ?>"
       target="_blank" rel="noopener noreferrer"
       title="<?php esc_attr_e( 'Contáctanos por WhatsApp', 'dimsop' ); ?>"
       aria-label="<?php esc_attr_e( 'WhatsApp', 'dimsop' ); ?>">
        <i class="fa fa-whatsapp" aria-hidden="true"></i>
    </a>
<?php endif; ?>

<!-- Modales globales del sitio -->
<?php
if ( locate_template( 'template-parts/modals/modal-careers.php' ) ) {
    get_template_part( 'template-parts/modals/modal-careers' );
}
?>

<?php wp_footer(); ?>
</body>
</html>
