<?php
/**
 * Ventana "Forma parte de nuestro equipo" (postulación con CV)
 *
 * Modal de Bootstrap que abren los enlaces con data-bs-target="#dimsop-carreras-modal"
 * (header, menú móvil y footer). initCareersForm() la envía por AJAX; sin JS hace
 * POST a admin-post.php y vuelve con ?postulacion= o ?postulacion_error=, y el JS
 * reabre la ventana para mostrar el aviso.
 *
 * @package Dimsop
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Solo se comparan contra listas fijas.
$dimsop_app_sent  = isset( $_GET['postulacion'] ) && 'enviada' === $_GET['postulacion'];
$dimsop_app_error = isset( $_GET['postulacion_error'] ) ? sanitize_key( wp_unslash( $_GET['postulacion_error'] ) ) : '';
// phpcs:enable

$dimsop_app_errors = array(
    'postulacion_incompleta' => __( 'Por favor completa tu nombre y un correo electrónico válido.', 'dimsop' ),
    'nombre_invalido'        => __( 'El nombre y apellido solo puede tener letras y espacios.', 'dimsop' ),
    'cv_requerido'           => __( 'Adjunta tu CV en PDF, DOC o DOCX.', 'dimsop' ),
    'cv_invalido'            => __( 'El CV no es válido. Adjunta un PDF, DOC o DOCX de máximo 5 MB.', 'dimsop' ),
    'limite'                 => __( 'Demasiados envíos recientes. Intenta de nuevo en unos minutos.', 'dimsop' ),
    'invalid_nonce'          => __( 'Tu sesión expiró. Recarga la página e intenta de nuevo.', 'dimsop' ),
    'db_error'               => __( 'No pudimos registrar tu postulación. Intenta más tarde.', 'dimsop' ),
);
?>

<div class="modal fade dimsop-modal" id="dimsop-carreras-modal" tabindex="-1" aria-labelledby="dimsop-carreras-modal-label" aria-hidden="true" data-lenis-prevent>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable dimsop-carreras-dialog">
        <div class="modal-content dimsop-modal-content">
            <div class="modal-header dimsop-modal-header">
                <h2 class="modal-title dimsop-modal-title" id="dimsop-carreras-modal-label"><?php esc_html_e( 'Forma parte de nuestro equipo', 'dimsop' ); ?></h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?php esc_attr_e( 'Cerrar', 'dimsop' ); ?>"></button>
            </div>

            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-6 dimsop-modal-form-side">
                        <p class="dimsop-modal-lead"><?php esc_html_e( 'Déjanos tus datos y tu CV. Nuestro equipo de talento te contactará si tu perfil encaja con alguna vacante.', 'dimsop' ); ?></p>

                        <form class="dimsop-careers-form" id="dimsop-carreras-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="dimsop_submit_application">
                            <?php dimsop_lang_field(); ?>
                            <input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'dimsop_careers_nonce' ) ); ?>">
                            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo esc_attr( 5 * 1024 * 1024 ); ?>">
                            <input type="text" name="homepage" value="" tabindex="-1" autocomplete="off" aria-hidden="true" class="dimsop-hp-field">

                            <div class="dimsop-field">
                                <label class="dimsop-label" for="dimsop-c-name"><?php esc_html_e( 'Nombre y apellido *', 'dimsop' ); ?></label>
                                <input class="dimsop-input" id="dimsop-c-name" type="text" name="name" maxlength="200" autocomplete="name" required>
                            </div>
                            <div class="dimsop-field">
                                <label class="dimsop-label" for="dimsop-c-email"><?php esc_html_e( 'Correo electrónico *', 'dimsop' ); ?></label>
                                <input class="dimsop-input" id="dimsop-c-email" type="email" name="email" maxlength="254" autocomplete="email" required>
                            </div>
                            <div class="row g-0 gx-sm-2">
                                <div class="col-sm-6 dimsop-field">
                                    <label class="dimsop-label" for="dimsop-c-phone"><?php esc_html_e( 'Teléfono', 'dimsop' ); ?> <span class="dimsop-label-optional"><?php esc_html_e( '(opcional)', 'dimsop' ); ?></span></label>
                                    <input class="dimsop-input" id="dimsop-c-phone" type="tel" name="phone" maxlength="40" autocomplete="tel">
                                </div>
                                <div class="col-sm-6 dimsop-field">
                                    <label class="dimsop-label" for="dimsop-c-profession"><?php esc_html_e( 'Profesión', 'dimsop' ); ?> <span class="dimsop-label-optional"><?php esc_html_e( '(opcional)', 'dimsop' ); ?></span></label>
                                    <input class="dimsop-input" id="dimsop-c-profession" type="text" name="profession" maxlength="200" autocomplete="organization-title">
                                </div>
                            </div>
                            <div class="dimsop-field">
                                <label class="dimsop-label" for="dimsop-c-city"><?php esc_html_e( 'Ciudad de residencia', 'dimsop' ); ?> <span class="dimsop-label-optional"><?php esc_html_e( '(opcional)', 'dimsop' ); ?></span></label>
                                <input class="dimsop-input" id="dimsop-c-city" type="text" name="city" maxlength="200" autocomplete="address-level2">
                            </div>
                            <div class="dimsop-field">
                                <label class="dimsop-label" for="dimsop-c-cv"><?php esc_html_e( 'Adjunta tu CV *', 'dimsop' ); ?></label>
                                <div class="dimsop-file">
                                    <input class="dimsop-file-input" id="dimsop-c-cv" type="file" name="cv" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required aria-describedby="dimsop-c-cv-hint">
                                    <span class="dimsop-file-box" aria-hidden="true">
                                        <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                                        <span class="dimsop-file-name" data-empty="<?php esc_attr_e( 'Seleccionar archivo', 'dimsop' ); ?>"><?php esc_html_e( 'Seleccionar archivo', 'dimsop' ); ?></span>
                                    </span>
                                </div>
                                <p class="dimsop-field-hint mt-1 mb-0" id="dimsop-c-cv-hint"><?php esc_html_e( 'PDF, DOC o DOCX, máximo 5 MB.', 'dimsop' ); ?></p>
                            </div>

                            <p class="dimsop-careers-privacy">
                                <?php
                                printf(
                                    /* translators: %s: enlace a la política de privacidad */
                                    esc_html__( 'Usaremos tus datos solo para este proceso de selección. Más información en nuestra %s.', 'dimsop' ),
                                    '<a href="' . esc_url( dimsop_page_url( 'politica-de-privacidad' ) ) . '">' . esc_html__( 'política de privacidad', 'dimsop' ) . '</a>'
                                );
                                ?>
                            </p>

                            <div class="text-center">
                                <button type="submit" class="dimsop-button dimsop-button--lg w-100">
                                    <span class="dimsop-button-label"><?php esc_html_e( 'Enviar postulación', 'dimsop' ); ?></span> <i class="fa fa-paper-plane dimsop-button-arrow" aria-hidden="true"></i>
                                </button>
                            </div>

                            <div id="dimsop-careers-feedback" class="mt-3" aria-live="polite">
                                <?php if ( $dimsop_app_sent ) : ?>
                                    <div class="dimsop-alert dimsop-alert--success">
                                        <i class="fa fa-check-circle" aria-hidden="true"></i>
                                        <span><?php esc_html_e( '¡Postulación enviada! Nuestro equipo de talento revisará tu CV pronto.', 'dimsop' ); ?></span>
                                    </div>
                                <?php elseif ( isset( $dimsop_app_errors[ $dimsop_app_error ] ) ) : ?>
                                    <div class="dimsop-alert dimsop-alert--error">
                                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                                        <span><?php echo esc_html( $dimsop_app_errors[ $dimsop_app_error ] ); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-6 dimsop-modal-img-side d-none d-md-flex">
                        <img src="<?php echo esc_url( dimsop_asset_url( 'images/Forma_parte_de_nuestro_equipo.webp' ) ); ?>"
                             alt="" width="1080" height="1440" loading="lazy" decoding="async">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
