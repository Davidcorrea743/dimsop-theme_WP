<?php
/**
 * Pie de página estándar del Tema Dimsop
 *
 * @package Dimsop
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<footer id="dimsop_footer" class="dimsop-footer text-white">
    <div class="container py-5">
        <div class="row g-4">
            <!-- Columna 1: Logo + Contacto general -->
            <div class="col-lg-3 col-md-6">
                <a class="dimsop-logo d-inline-block mb-3" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo( 'name' ); ?>">
                    <img src="<?php echo esc_url( dimsop_asset_url( 'images/logos/logo_Dimsop_D_Degradado.webp' ) ); ?>"
                         alt="<?php bloginfo( 'name' ); ?>" class="img-fluid dimsop-footer-logo-img" loading="lazy">
                </a>
                <p class="mb-2 dimsop-footer-contact-item">
                    <i class="fa fa-envelope me-2" aria-hidden="true"></i>
                    <a href="mailto:<?php echo esc_attr( dimsop_get_option( 'contact_email' ) ); ?>" class="text-white text-decoration-none">
                        <?php echo esc_html( dimsop_get_option( 'contact_email' ) ); ?>
                    </a>
                </p>
                <p class="mb-0 dimsop-footer-contact-item">
                    <i class="fa fa-phone me-2" aria-hidden="true"></i>
                    <a href="tel:<?php echo esc_attr( dimsop_sanitize_phone( dimsop_get_option( 'contact_phone_ve' ) ) ); ?>" class="text-white text-decoration-none">
                        <?php echo esc_html( dimsop_get_option( 'contact_phone_ve' ) ); ?>
                    </a>
                </p>
            </div>

            <!-- Columna 2: Navegación -->
            <div class="col-lg-3 col-md-6">
                <h5 class="dimsop-footer-title"><?php esc_html_e( 'Navegación', 'dimsop' ); ?></h5>
                <ul class="list-unstyled">
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Inicio', 'dimsop' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/#nosotros' ) ); ?>"><?php esc_html_e( 'Nosotros', 'dimsop' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/#unidades' ) ); ?>"><?php esc_html_e( 'Unidades de Negocio', 'dimsop' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/#blog' ) ); ?>"><?php esc_html_e( 'Blog', 'dimsop' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/#contacto' ) ); ?>"><?php esc_html_e( 'Contacto', 'dimsop' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/politica-de-privacidad' ) ); ?>"><?php esc_html_e( 'Políticas de Privacidad', 'dimsop' ); ?></a></li>
                    <li>
                        <a href="#carreras" data-bs-toggle="modal" data-bs-target="#dimsop-carreras-modal">
                            <?php esc_html_e( 'Forma parte de nuestro equipo', 'dimsop' ); ?>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Columna 3: Sede Venezuela -->
            <div class="col-lg-3 col-md-6 dimsop-footer-map-col">
                <h5 class="dimsop-footer-title"><?php esc_html_e( 'Venezuela', 'dimsop' ); ?></h5>
                <p class="dimsop-footer-address">
                    <?php echo esc_html( dimsop_get_option( 'address_ve' ) ); ?>
                </p>
                <div class="dimsop-footer-map-wrap">
                    <iframe class="dimsop-footer-map"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3922.7!2d-66.8227312!3d10.4967188!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8c2a57469c306ea3%3A0xe03812c021d2fc41!2sDIMSOP%20TECHNOLOGY%20VE!5e0!3m2!1ses!2sve!4v1700000000000"
                            allowfullscreen="" loading="lazy"
                            title="<?php esc_attr_e( 'Ubicación Venezuela', 'dimsop' ); ?>"></iframe>
                </div>
                <a href="https://www.google.com/maps/place/DIMSOP+TECHNOLOGY+VE/@10.4967188,-66.8227312,17z"
                   target="_blank" rel="noopener noreferrer" class="dimsop-footer-map-link">
                    <?php esc_html_e( 'Abrir en Google Maps →', 'dimsop' ); ?>
                </a>
                <p class="mt-2 mb-0 dimsop-footer-meta">
                    <i class="fa fa-phone me-1" aria-hidden="true"></i>
                    <?php echo esc_html( dimsop_get_option( 'contact_phone_ve' ) ); ?>
                </p>
                <p class="mt-1 mb-0 dimsop-footer-meta">
                    <i class="fa fa-id-card me-1" aria-hidden="true"></i>
                    RIF: <?php echo esc_html( dimsop_get_option( 'rif_ve' ) ); ?>
                </p>
            </div>

            <!-- Columna 4: Sede Estados Unidos -->
            <div class="col-lg-3 col-md-6 dimsop-footer-map-col">
                <h5 class="dimsop-footer-title"><?php esc_html_e( 'Estados Unidos', 'dimsop' ); ?></h5>
                <p class="dimsop-footer-address">
                    <?php echo esc_html( dimsop_get_option( 'address_us' ) ); ?>
                </p>
                <div class="dimsop-footer-map-wrap">
                    <iframe class="dimsop-footer-map"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3585.5!2d-80.31312!3d25.8184393!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x88d9ba2ed7a89c0f%3A0x19d982c9a758f0ce!2s4995%20NW%2072nd%20Ave%20%23205%2C%20Miami%2C%20FL%2033166!5e0!3m2!1ses!2sus!4v1700000000000"
                            allowfullscreen="" loading="lazy"
                            title="<?php esc_attr_e( 'Ubicación Estados Unidos', 'dimsop' ); ?>"></iframe>
                </div>
                <a href="https://www.google.com/maps/place/4995+NW+72nd+Ave+%23205,+Miami,+FL+33166/@25.8184393,-80.31312,17z"
                   target="_blank" rel="noopener noreferrer" class="dimsop-footer-map-link">
                    <?php esc_html_e( 'Abrir en Google Maps →', 'dimsop' ); ?>
                </a>
                <p class="mt-2 mb-0 dimsop-footer-meta">
                    <i class="fa fa-phone me-1" aria-hidden="true"></i>
                    <?php echo esc_html( dimsop_get_option( 'contact_phone_us' ) ); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Barra inferior (Copyright + Redes sociales) -->
    <div class="dimsop-footer-bottom">
        <div class="container d-flex flex-wrap justify-content-between align-items-center py-3">
            <span>&copy; <?php echo esc_html( date( 'Y' ) ); ?> Dimsop. <?php esc_html_e( 'Todos los derechos reservados.', 'dimsop' ); ?></span>
            <div class="dimsop-social d-flex gap-2">
                <a href="<?php echo esc_url( dimsop_get_option( 'linkedin_url' ) ); ?>"
                   target="_blank" rel="noopener noreferrer" class="dimsop-social-btn" title="LinkedIn" aria-label="LinkedIn">
                    <i class="fa fa-linkedin" aria-hidden="true"></i>
                </a>
                <a href="<?php echo esc_url( dimsop_get_option( 'instagram_url' ) ); ?>"
                   target="_blank" rel="noopener noreferrer" class="dimsop-social-btn" title="Instagram" aria-label="Instagram">
                    <i class="fa fa-instagram" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
</footer>

<!-- Botón flotante persistente de WhatsApp -->
<?php
$whatsapp_num = dimsop_sanitize_phone( dimsop_get_option( 'whatsapp_number', '582128195400' ) );
if ( ! empty( $whatsapp_num ) ) :
?>
    <a id="dimsop-whatsapp-float"
       href="<?php echo esc_url( 'https://wa.me/' . $whatsapp_num ); ?>"
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
