<?php
/**
 * Sección: Contacto
 *
 * Lo envía initContactForm() por AJAX, sin recargar la página. Sin JS, envía por POST a
 * admin-post.php (dimsop_handle_contact_form), que redirige con ?mensaje= o ?form_error=.
 *
 * @package Dimsop
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Solo se comparan contra listas fijas.
$dimsop_sent  = isset( $_GET['mensaje'] ) && 'enviado' === $_GET['mensaje'];
$dimsop_error = isset( $_GET['form_error'] ) ? sanitize_key( wp_unslash( $_GET['form_error'] ) ) : '';
// phpcs:enable

$dimsop_errors = array(
    'datos_incompletos' => __( 'Por favor completa tu nombre y un correo electrónico válido.', 'dimsop' ),
    'nombre_invalido'   => __( 'El nombre y apellido solo puede tener letras y espacios.', 'dimsop' ),
    'motivo_requerido'  => __( 'Elegiste "Otro": indícanos el motivo de tu contacto.', 'dimsop' ),
    'limite'            => __( 'Demasiados envíos recientes. Intenta de nuevo en unos minutos.', 'dimsop' ),
    'invalid_nonce'     => __( 'Tu sesión expiró. Recarga la página e intenta de nuevo.', 'dimsop' ),
    'db_error'          => __( 'No pudimos registrar tu solicitud. Intenta más tarde.', 'dimsop' ),
    'invalid_method'    => __( 'No pudimos procesar tu solicitud. Intenta de nuevo.', 'dimsop' ),
);

?>

<section id="contacto" class="dimsop-section dimsop-section-alt dimsop-contacto">
    <div class="container">
        <div class="dimsop-section-head text-center mb-5">
            <span class="dimsop-badge mb-3"><span class="dimsop-badge-dot"></span> <?php esc_html_e( 'Conversemos', 'dimsop' ); ?></span>
            <h2 class="dimsop-h2 mb-2"><?php esc_html_e( 'Contáctanos', 'dimsop' ); ?></h2>
            <p class="dimsop-section-lead mx-auto">
                <?php esc_html_e( 'Déjanos tus datos y nos pondremos en contacto contigo a la brevedad.', 'dimsop' ); ?>
            </p>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-lg-8">
                <form class="dimsop-form row g-0 gx-md-3" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
                    <input type="hidden" name="action" value="dimsop_submit_contact">
                    <?php dimsop_lang_field(); ?>
                    <input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'dimsop_public_nonce' ) ); ?>">
                    <input type="text" name="homepage" value="" tabindex="-1" autocomplete="off" aria-hidden="true" class="dimsop-hp-field">

                    <div class="col-12 dimsop-field">
                        <label class="dimsop-label" for="dimsop-f-name"><?php esc_html_e( 'Nombre y apellido *', 'dimsop' ); ?></label>
                        <input class="dimsop-input" id="dimsop-f-name" type="text" name="name" maxlength="200" autocomplete="name" required>
                    </div>
                    <div class="col-md-6 dimsop-field">
                        <label class="dimsop-label" for="dimsop-f-email"><?php esc_html_e( 'Correo electrónico *', 'dimsop' ); ?></label>
                        <input class="dimsop-input" id="dimsop-f-email" type="email" name="email" maxlength="254" autocomplete="email" required>
                    </div>
                    <div class="col-md-6 dimsop-field">
                        <label class="dimsop-label" for="dimsop-f-phone"><?php esc_html_e( 'Número de teléfono', 'dimsop' ); ?> <span class="dimsop-label-optional"><?php esc_html_e( '(opcional)', 'dimsop' ); ?></span></label>
                        <input class="dimsop-input" id="dimsop-f-phone" type="tel" name="phone" maxlength="40" autocomplete="tel">
                    </div>
                    <div class="col-md-6 dimsop-field">
                        <label class="dimsop-label" for="dimsop-f-company"><?php esc_html_e( 'Empresa', 'dimsop' ); ?> <span class="dimsop-label-optional"><?php esc_html_e( '(opcional)', 'dimsop' ); ?></span></label>
                        <input class="dimsop-input" id="dimsop-f-company" type="text" name="company" maxlength="200" autocomplete="organization">
                    </div>
                    <div class="col-md-6 dimsop-field">
                        <label class="dimsop-label" for="dimsop-f-position"><?php esc_html_e( 'Cargo', 'dimsop' ); ?> <span class="dimsop-label-optional"><?php esc_html_e( '(opcional)', 'dimsop' ); ?></span></label>
                        <input class="dimsop-input" id="dimsop-f-position" type="text" name="position" maxlength="200" autocomplete="organization-title">
                    </div>
                    <div class="col-12 dimsop-field">
                        <label class="dimsop-label" for="dimsop-f-service"><?php esc_html_e( 'Servicio de interés', 'dimsop' ); ?> <span class="dimsop-label-optional"><?php esc_html_e( '(opcional)', 'dimsop' ); ?></span></label>
                        <select class="dimsop-input" id="dimsop-f-service" name="service">
                            <option value=""><?php esc_html_e( 'Selecciona un servicio', 'dimsop' ); ?></option>
                            <?php foreach ( dimsop_get_service_options() as $dimsop_value => $dimsop_label ) : ?>
                                <option value="<?php echo esc_attr( $dimsop_value ); ?>"><?php echo esc_html( $dimsop_label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php // Visible sin JS; initContactForm() lo muestra solo cuando el servicio elegido es "Otro". ?>
                    <div class="col-12 dimsop-field" id="dimsop-f-other-wrap">
                        <label class="dimsop-label" for="dimsop-f-other"><?php esc_html_e( 'Si elegiste "Otro", indícanos el motivo de tu contacto', 'dimsop' ); ?></label>
                        <input class="dimsop-input" id="dimsop-f-other" type="text" name="other_reason" maxlength="200" placeholder="<?php esc_attr_e( 'Ej.: soporte técnico, consultoría…', 'dimsop' ); ?>">
                    </div>
                    <div class="col-12 dimsop-field">
                        <label class="dimsop-label" for="dimsop-f-message"><?php esc_html_e( 'Mensaje', 'dimsop' ); ?> <span class="dimsop-label-optional"><?php esc_html_e( '(opcional)', 'dimsop' ); ?></span></label>
                        <textarea class="dimsop-input" id="dimsop-f-message" name="message" maxlength="1000" placeholder="<?php esc_attr_e( 'Cuéntanos sobre tu proyecto', 'dimsop' ); ?>"></textarea>
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="dimsop-button dimsop-button--lg">
                            <span class="dimsop-button-label"><?php esc_html_e( 'Enviar mensaje', 'dimsop' ); ?></span> <i class="fa fa-paper-plane dimsop-button-arrow" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="col-12" id="dimsop-contact-feedback" aria-live="polite">
                        <?php if ( $dimsop_sent ) : ?>
                            <div class="dimsop-alert dimsop-alert--success">
                                <i class="fa fa-check-circle" aria-hidden="true"></i>
                                <span><?php esc_html_e( '¡Gracias por contactarnos! Un especialista de Dimsop se comunicará contigo a la brevedad.', 'dimsop' ); ?></span>
                            </div>
                        <?php elseif ( isset( $dimsop_errors[ $dimsop_error ] ) ) : ?>
                            <div class="dimsop-alert dimsop-alert--error">
                                <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                                <span><?php echo esc_html( $dimsop_errors[ $dimsop_error ] ); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
