<?php
/**
 * Manejador Seguro del Formulario de Contacto (Leads CRM)
 *
 * Soporta envíos AJAX y fallback POST estándar.
 * Incluye: Verificación Nonce, Rate Limiting por IP, Honeypot,
 * Sanitización estricta, registro en BD (CPT dimsop_lead) y notificación por email corporativo.
 *
 * @package Dimsop
 * @subpackage Forms
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Procesador principal del formulario de contacto
 */
function dimsop_handle_contact_form() {
    $is_ajax = wp_doing_ajax();

    // 1. Verificación de Método HTTP
    if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
        dimsop_form_response( false, esc_html__( 'Método de solicitud no permitido.', 'dimsop' ), 'invalid_method', 405, $is_ajax );
        return;
    }

    // 2. Verificación de Token CSRF (Nonce)
    $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
    if ( ! wp_verify_nonce( $nonce, 'dimsop_public_nonce' ) ) {
        dimsop_form_response( false, esc_html__( 'Tu sesión expiró. Recarga la página e intenta de nuevo.', 'dimsop' ), 'invalid_nonce', 403, $is_ajax );
        return;
    }

    // 3. Honeypot Anti-Bots (Campo oculto 'homepage')
    $honeypot = isset( $_POST['homepage'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['homepage'] ) ) ) : '';
    if ( ! empty( $honeypot ) ) {
        // Simular éxito para despistar al bot sin guardar ningún registro en el sistema
        dimsop_form_response( true, esc_html__( '¡Gracias por contactarnos! Un especialista de Dimsop se comunicará contigo a la brevedad.', 'dimsop' ), 'enviado', 200, $is_ajax );
        return;
    }

    // 4. Rate Limiting por IP (Máximo 5 envíos cada 10 minutos)
    if ( dimsop_is_rate_limited( 'contact_form', 5, 600 ) ) {
        dimsop_form_response( false, esc_html__( 'Demasiados envíos recientes. Intenta de nuevo en unos minutos.', 'dimsop' ), 'limite', 429, $is_ajax );
        return;
    }

    // 5. Captura y Sanitización Estricta de Campos (con límites de longitud)
    $name     = isset( $_POST['name'] ) ? sanitize_text_field( substr( trim( wp_unslash( $_POST['name'] ) ), 0, 200 ) ) : '';
    $email    = isset( $_POST['email'] ) ? sanitize_email( substr( trim( wp_unslash( $_POST['email'] ) ), 0, 254 ) ) : '';
    $phone    = isset( $_POST['phone'] ) ? dimsop_sanitize_phone( substr( trim( wp_unslash( $_POST['phone'] ) ), 0, 40 ) ) : '';
    $company  = isset( $_POST['company'] ) ? sanitize_text_field( substr( trim( wp_unslash( $_POST['company'] ) ), 0, 200 ) ) : '';
    $position = isset( $_POST['position'] ) ? sanitize_text_field( substr( trim( wp_unslash( $_POST['position'] ) ), 0, 200 ) ) : '';
    $service  = isset( $_POST['service'] ) ? sanitize_text_field( trim( wp_unslash( $_POST['service'] ) ) ) : '';
    $message  = isset( $_POST['message'] ) ? sanitize_textarea_field( substr( trim( wp_unslash( $_POST['message'] ) ), 0, 1000 ) ) : '';

    // 6. Validación de Campos Obligatorios
    if ( empty( $name ) || empty( $email ) || ! is_email( $email ) ) {
        dimsop_form_response( false, esc_html__( 'Por favor completa tu nombre y un correo electrónico válido.', 'dimsop' ), 'datos_incompletos', 400, $is_ajax );
        return;
    }

    // Validar servicio de interés contra lista blanca
    $valid_services = array_keys( dimsop_get_service_options() );
    if ( ! in_array( $service, $valid_services, true ) ) {
        $service = '';
    }

    $ip = dimsop_get_client_ip();

    // 7. Registro en Base de Datos (CPT dimsop_lead)
    $post_title = $company ? sprintf( '%s (%s)', $name, $company ) : $name;

    $lead_id = wp_insert_post( array(
        'post_title'   => $post_title,
        'post_type'    => 'dimsop_lead',
        'post_status'  => 'publish',
        'post_content' => $message,
    ), true );

    if ( is_wp_error( $lead_id ) ) {
        dimsop_form_response( false, esc_html__( 'No pudimos registrar tu solicitud. Intenta más tarde.', 'dimsop' ), 'db_error', 500, $is_ajax );
        return;
    }

    // Guardar Metadatos del Prospecto
    update_post_meta( $lead_id, '_dimsop_lead_name', $name );
    update_post_meta( $lead_id, '_dimsop_lead_email', $email );
    update_post_meta( $lead_id, '_dimsop_lead_phone', $phone );
    update_post_meta( $lead_id, '_dimsop_lead_company', $company );
    update_post_meta( $lead_id, '_dimsop_lead_position', $position );
    update_post_meta( $lead_id, '_dimsop_lead_service', $service );
    update_post_meta( $lead_id, '_dimsop_lead_message', $message );
    update_post_meta( $lead_id, '_dimsop_lead_ip', $ip );
    update_post_meta( $lead_id, '_dimsop_lead_date', current_time( 'mysql' ) );

    // 8. Envío de Notificación por Correo Corporativo
    dimsop_send_lead_notification( $name, $email, $phone, $company, $position, $service, $message, $ip, $lead_id );

    // 9. Respuesta Exitosa
    dimsop_form_response( true, esc_html__( '¡Gracias por contactarnos! Un especialista de Dimsop se comunicará contigo a la brevedad.', 'dimsop' ), 'enviado', 200, $is_ajax );
}

// Registro de Hooks AJAX y Form POST estándar
add_action( 'wp_ajax_dimsop_submit_contact', 'dimsop_handle_contact_form' );
add_action( 'wp_ajax_nopriv_dimsop_submit_contact', 'dimsop_handle_contact_form' );
add_action( 'admin_post_dimsop_submit_contact', 'dimsop_handle_contact_form' );
add_action( 'admin_post_nopriv_dimsop_submit_contact', 'dimsop_handle_contact_form' );

/**
 * Notificación por Email Corporativo
 */
function dimsop_send_lead_notification( $name, $email, $phone, $company, $position, $service, $message, $ip, $lead_id ) {
    $to = dimsop_get_option( 'contact_email', get_option( 'admin_email' ) );
    $subject = sprintf( '[Dimsop Web] Nuevo Prospecto: %s', $name );
    if ( $company ) {
        $subject .= sprintf( ' (%s)', $company );
    }

    $services = dimsop_get_service_options();
    $service_label = isset( $services[ $service ] ) ? $services[ $service ] : ( $service ? $service : 'No especificado' );

    $admin_url = admin_url( 'post.php?post=' . $lead_id . '&action=edit' );

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        sprintf( 'Reply-To: %s <%s>', $name, $email ),
    );

    // Plantilla HTML de Notificación
    $body = '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;line-height:1.6;color:#1d2a44;background:#f4f7fb;margin:0;padding:20px;">';
    $body .= '<div style="max-width:600px;margin:auto;background:#fff;border-radius:10px;overflow:hidden;border:1px solid #dce3ec;">';
    $body .= '<div style="background:#003362;padding:24px;text-align:center;"><h2 style="color:#ffffff;margin:0;font-size:20px;">Nuevo Contacto Web Dimsop</h2></div>';
    $body .= '<div style="padding:24px;">';
    $body .= '<table style="width:100%;border-collapse:collapse;">';
    $body .= sprintf( '<tr><td style="padding:8px 0;font-weight:bold;width:35%%;">Nombre:</td><td style="padding:8px 0;">%s</td></tr>', esc_html( $name ) );
    $body .= sprintf( '<tr><td style="padding:8px 0;font-weight:bold;">Correo:</td><td style="padding:8px 0;"><a href="mailto:%s">%s</a></td></tr>', esc_attr( $email ), esc_html( $email ) );
    $body .= sprintf( '<tr><td style="padding:8px 0;font-weight:bold;">Teléfono:</td><td style="padding:8px 0;">%s</td></tr>', esc_html( $phone ? $phone : 'No indicado' ) );
    $body .= sprintf( '<tr><td style="padding:8px 0;font-weight:bold;">Empresa:</td><td style="padding:8px 0;">%s</td></tr>', esc_html( $company ? $company : 'No indicada' ) );
    $body .= sprintf( '<tr><td style="padding:8px 0;font-weight:bold;">Cargo:</td><td style="padding:8px 0;">%s</td></tr>', esc_html( $position ? $position : 'No indicado' ) );
    $body .= sprintf( '<tr><td style="padding:8px 0;font-weight:bold;">Servicio de Interés:</td><td style="padding:8px 0;color:#003362;font-weight:bold;">%s</td></tr>', esc_html( $service_label ) );
    if ( $message ) {
        $body .= sprintf( '<tr><td style="padding:8px 0;font-weight:bold;vertical-align:top;">Mensaje / Motivo:</td><td style="padding:8px 0;background:#f8f9fa;padding:10px;border-radius:6px;">%s</td></tr>', nl2br( esc_html( $message ) ) );
    }
    $body .= sprintf( '<tr><td style="padding:8px 0;font-weight:bold;">IP de origen:</td><td style="padding:8px 0;"><code>%s</code></td></tr>', esc_html( $ip ) );
    $body .= '</table>';
    $body .= sprintf( '<div style="margin-top:24px;text-align:center;"><a href="%s" style="background:#4da32f;color:#fff;text-decoration:none;padding:10px 20px;border-radius:6px;font-weight:bold;display:inline-block;">Ver Lead en Panel de WordPress</a></div>', esc_url( $admin_url ) );
    $body .= '</div></div></body></html>';

    wp_mail( $to, $subject, $body, $headers );
}

/**
 * Despachador de respuesta adaptable (AJAX / Redirección HTTP)
 */
function dimsop_form_response( $success, $message, $code, $status_code = 200, $is_ajax = true ) {
    if ( $is_ajax ) {
        status_header( $status_code );
        if ( $success ) {
            wp_send_json_success( array(
                'message' => $message,
                'code'    => $code,
            ) );
        } else {
            wp_send_json_error( array(
                'message' => $message,
                'code'    => $code,
            ) );
        }
    } else {
        // Redirección para POST sin Javascript
        $redirect_url = home_url( '/' );
        if ( $success ) {
            $redirect_url = add_query_arg( 'mensaje', $code, $redirect_url );
        } else {
            // "error" es una variable de consulta reservada de WordPress.
            $redirect_url = add_query_arg( 'form_error', $code, $redirect_url );
        }
        $redirect_url .= '#contacto';
        wp_safe_redirect( $redirect_url );
        exit;
    }
}
