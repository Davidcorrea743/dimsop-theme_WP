<?php
/**
 * Postulaciones "Forma parte de nuestro equipo" (CPT dimsop_job_app)
 *
 * Mismo esquema que el formulario de contacto (nonce, honeypot, límite por IP,
 * respuesta AJAX o redirección) más la subida segura del CV:
 *
 * - Solo PDF, DOC o DOCX de hasta 5 MB, comprobando la extensión y el contenido
 *   real del archivo (firma binaria), no solo lo que dice el navegador.
 * - Se guarda con un nombre aleatorio en una carpeta privada, fuera de la carpeta
 *   pública de la web cuando el servidor lo permite (ver dimsop_cv_storage_dir()).
 * - Solo se descarga desde el panel, por administradores (post-types.php).
 * - Al borrar la postulación definitivamente se borra también el archivo.
 *
 * @package Dimsop
 * @subpackage Forms
 */

defined( 'ABSPATH' ) || exit;

/** Tamaño máximo del CV en bytes (5 MB). */
const DIMSOP_CV_MAX_BYTES = 5 * 1024 * 1024;

/**
 * Estados de una postulación (equivalen a los de Odoo).
 *
 * @return array<string,string>
 */
function dimsop_job_app_statuses() {
    return array(
        'draft'    => __( 'Recibida', 'dimsop' ),
        'reviewed' => __( 'En revisión', 'dimsop' ),
        'hired'    => __( 'Contratada', 'dimsop' ),
    );
}

/**
 * Carpeta privada de los CV.
 *
 * 1. La constante DIMSOP_CV_DIR de wp-config.php, si existe.
 * 2. Una carpeta junto a la raíz pública de WordPress (no se puede pedir por URL).
 * 3. Si el hosting no deja escribir ahí: wp-content/uploads/dimsop-private/cv,
 *    protegida con .htaccess (Apache) y nombres aleatorios. En nginx hay que añadir
 *    una regla que la bloquee (anotado en PROGRESO.md).
 *
 * @return string|false Ruta absoluta sin barra final, o false si no se puede crear.
 */
function dimsop_cv_storage_dir() {
    static $dir = null;
    if ( null !== $dir ) {
        return $dir;
    }

    $candidates = array();
    if ( defined( 'DIMSOP_CV_DIR' ) && DIMSOP_CV_DIR ) {
        $candidates[] = untrailingslashit( DIMSOP_CV_DIR );
    }
    $candidates[] = dirname( untrailingslashit( ABSPATH ) ) . '/dimsop-private/cv';
    $uploads      = wp_upload_dir( null, false );
    if ( empty( $uploads['error'] ) ) {
        $candidates[] = untrailingslashit( $uploads['basedir'] ) . '/dimsop-private/cv';
    }

    foreach ( $candidates as $candidate ) {
        if ( ( is_dir( $candidate ) || wp_mkdir_p( $candidate ) ) && wp_is_writable( $candidate ) ) {
            dimsop_protect_dir( $candidate );
            $dir = $candidate;
            return $dir;
        }
    }

    $dir = false;
    return $dir;
}

/**
 * Ruta absoluta de un CV a partir de lo guardado en _dimsop_app_cv_file.
 *
 * Se guarda relativa a la carpeta privada ("2026-11/abc.pdf") para que siga valiendo
 * al mover el sitio de servidor; una ruta absoluta antigua se respeta tal cual.
 *
 * @return string Ruta, o '' si no hay archivo o carpeta.
 */
function dimsop_cv_absolute_path( $stored ) {
    $stored = (string) $stored;
    if ( '' === $stored ) {
        return '';
    }
    if ( preg_match( '#^([a-zA-Z]:)?[/\\\\]#', $stored ) ) {
        return $stored;
    }
    $dir = dimsop_cv_storage_dir();
    return $dir ? $dir . '/' . ltrim( $stored, '/' ) : '';
}

/**
 * Comprueba que una ruta esté dentro de la carpeta privada de CV (evita servir o borrar otros archivos).
 *
 * @return string|false Ruta real, o false si queda fuera o no existe.
 */
function dimsop_cv_safe_path( $path ) {
    $dir  = dimsop_cv_storage_dir();
    $real = $path ? realpath( $path ) : false;
    $base = $dir ? realpath( $dir ) : false;

    if ( ! $real || ! $base || 0 !== strpos( wp_normalize_path( $real ), trailingslashit( wp_normalize_path( $base ) ) ) ) {
        return false;
    }
    return $real;
}

/**
 * Deja la carpeta sin listado ni acceso directo en Apache/IIS.
 */
function dimsop_protect_dir( $dir ) {
    $files = array(
        '.htaccess'  => "Require all denied\nDeny from all\n",
        'index.php'  => "<?php\n// Silence is golden.\n",
        'web.config' => '<?xml version="1.0" encoding="UTF-8"?><configuration><system.webServer><authorization><deny users="*" /></authorization></system.webServer></configuration>',
    );
    foreach ( $files as $name => $content ) {
        if ( ! file_exists( $dir . '/' . $name ) ) {
            file_put_contents( $dir . '/' . $name, $content ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
        }
    }
}

/**
 * Valida el CV subido.
 *
 * @param array $file Entrada de $_FILES.
 * @return array{ext:string,name:string}|WP_Error
 */
function dimsop_validate_cv_upload( $file ) {
    if ( empty( $file ) || ! isset( $file['error'] ) || UPLOAD_ERR_NO_FILE === $file['error'] ) {
        return new WP_Error( 'cv_requerido', __( 'Adjunta tu CV en PDF, DOC o DOCX.', 'dimsop' ) );
    }
    if ( UPLOAD_ERR_INI_SIZE === $file['error'] || UPLOAD_ERR_FORM_SIZE === $file['error'] ) {
        return new WP_Error( 'cv_invalido', __( 'El CV supera el tamaño máximo de 5 MB.', 'dimsop' ) );
    }
    if ( UPLOAD_ERR_OK !== $file['error'] || ! is_uploaded_file( $file['tmp_name'] ) ) {
        return new WP_Error( 'cv_invalido', __( 'No pudimos recibir el archivo. Intenta de nuevo.', 'dimsop' ) );
    }

    $size = (int) filesize( $file['tmp_name'] );
    if ( $size <= 0 ) {
        return new WP_Error( 'cv_invalido', __( 'El archivo está vacío.', 'dimsop' ) );
    }
    if ( $size > DIMSOP_CV_MAX_BYTES ) {
        return new WP_Error( 'cv_invalido', __( 'El CV supera el tamaño máximo de 5 MB.', 'dimsop' ) );
    }

    $ext = strtolower( pathinfo( (string) $file['name'], PATHINFO_EXTENSION ) );
    if ( ! in_array( $ext, array( 'pdf', 'doc', 'docx' ), true ) ) {
        return new WP_Error( 'cv_invalido', __( 'Formato no válido. Solo se admiten archivos PDF, DOC o DOCX.', 'dimsop' ) );
    }

    // Firma binaria: el contenido debe corresponder a la extensión.
    $handle = fopen( $file['tmp_name'], 'rb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
    $head   = $handle ? (string) fread( $handle, 1024 ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fread
    if ( $handle ) {
        fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
    }

    switch ( $ext ) {
        case 'pdf':
            $valid = false !== strpos( $head, '%PDF-' );
            break;
        case 'doc':
            $valid = 0 === strpos( $head, "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1" ); // Documento OLE (Word 97-2003)
            break;
        default: // docx: un ZIP que contiene la carpeta word/
            $valid = 0 === strpos( $head, "PK\x03\x04" ) && dimsop_docx_has_word_part( $file['tmp_name'] );
    }

    if ( ! $valid ) {
        return new WP_Error( 'cv_invalido', __( 'El archivo no es un PDF, DOC o DOCX válido.', 'dimsop' ) );
    }

    // Nombre original solo para mostrarlo al descargar; nunca se usa como ruta.
    $name = sanitize_file_name( wp_basename( (string) $file['name'] ) );
    if ( '' === $name || '.' . $ext === $name ) {
        $name = 'cv.' . $ext;
    }

    return array(
        'ext'  => $ext,
        'name' => mb_substr( $name, 0, 150 ),
    );
}

/**
 * Comprueba que un ZIP sea un documento de Word. Sin ZipArchive se acepta la firma ZIP.
 */
function dimsop_docx_has_word_part( $path ) {
    if ( ! class_exists( 'ZipArchive' ) ) {
        return true;
    }
    $zip = new ZipArchive();
    if ( true !== $zip->open( $path ) ) {
        return false;
    }
    $ok = false !== $zip->locateName( 'word/document.xml' );
    $zip->close();
    return $ok;
}

/**
 * Procesa la postulación (AJAX o POST normal).
 */
function dimsop_handle_careers_form() {
    $is_ajax  = wp_doing_ajax();
    $redirect = array(
        'success_key' => 'postulacion',
        'error_key'   => 'postulacion_error',
        'anchor'      => 'carreras',
    );
    $respond  = function ( $success, $message, $code, $status ) use ( $is_ajax, $redirect ) {
        dimsop_form_response( $success, $message, $code, $status, $is_ajax, $redirect );
    };

    if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) {
        $respond( false, __( 'Método de solicitud no permitido.', 'dimsop' ), 'invalid_method', 405 );
        return;
    }

    $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
    if ( ! wp_verify_nonce( $nonce, 'dimsop_careers_nonce' ) ) {
        $respond( false, __( 'Tu sesión expiró. Recarga la página e intenta de nuevo.', 'dimsop' ), 'invalid_nonce', 403 );
        return;
    }

    $success_message = __( '¡Postulación enviada! Nuestro equipo de talento revisará tu CV pronto.', 'dimsop' );

    // Honeypot: se finge éxito y no se guarda nada.
    if ( ! empty( $_POST['homepage'] ) ) {
        $respond( true, $success_message, 'enviada', 200 );
        return;
    }

    if ( dimsop_is_rate_limited( 'career_apply', 3, 600 ) ) {
        $respond( false, __( 'Demasiados envíos recientes. Intenta de nuevo en unos minutos.', 'dimsop' ), 'limite', 429 );
        return;
    }

    $field = function ( $key, $max ) {
        return isset( $_POST[ $key ] ) ? sanitize_text_field( mb_substr( trim( wp_unslash( $_POST[ $key ] ) ), 0, $max ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verificado arriba.
    };

    $name       = trim( preg_replace( '/\s+/u', ' ', $field( 'name', 200 ) ) );
    $email      = sanitize_email( $field( 'email', 254 ) );
    $phone      = dimsop_sanitize_phone( $field( 'phone', 40 ) );
    $profession = $field( 'profession', 200 );
    $city       = $field( 'city', 200 );

    if ( '' === $name || ! is_email( $email ) ) {
        $respond( false, __( 'Por favor completa tu nombre y un correo electrónico válido.', 'dimsop' ), 'postulacion_incompleta', 400 );
        return;
    }
    if ( ! preg_match( '/^[\p{L}\p{M}]+(?: [\p{L}\p{M}]+)*$/u', $name ) ) {
        $respond( false, __( 'El nombre y apellido solo puede tener letras y espacios.', 'dimsop' ), 'nombre_invalido', 400 );
        return;
    }

    $cv = dimsop_validate_cv_upload( $_FILES['cv'] ?? array() ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- Validado en dimsop_validate_cv_upload().
    if ( is_wp_error( $cv ) ) {
        $respond( false, $cv->get_error_message(), $cv->get_error_code(), 400 );
        return;
    }

    $dir = dimsop_cv_storage_dir();
    if ( ! $dir ) {
        $respond( false, __( 'No pudimos guardar tu CV. Intenta más tarde.', 'dimsop' ), 'db_error', 500 );
        return;
    }

    // Subcarpeta por mes y nombre aleatorio: no se puede adivinar ni chocar.
    $month_dir = $dir . '/' . gmdate( 'Y-m' );
    if ( ! is_dir( $month_dir ) && ! wp_mkdir_p( $month_dir ) ) {
        $respond( false, __( 'No pudimos guardar tu CV. Intenta más tarde.', 'dimsop' ), 'db_error', 500 );
        return;
    }
    dimsop_protect_dir( $month_dir );
    $relative = gmdate( 'Y-m' ) . '/' . bin2hex( random_bytes( 16 ) ) . '.' . $cv['ext'];
    $path     = $dir . '/' . $relative;

    if ( ! move_uploaded_file( $_FILES['cv']['tmp_name'], $path ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- Validado arriba.
        $respond( false, __( 'No pudimos guardar tu CV. Intenta más tarde.', 'dimsop' ), 'db_error', 500 );
        return;
    }
    @chmod( $path, 0640 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_operations_chmod -- Sin efecto en Windows.

    $app_id = wp_insert_post( array(
        'post_title'  => $profession ? sprintf( '%s (%s)', $name, $profession ) : $name,
        'post_type'   => 'dimsop_job_app',
        'post_status' => 'publish',
    ), true );

    if ( is_wp_error( $app_id ) ) {
        wp_delete_file( $path );
        $respond( false, __( 'No pudimos registrar tu postulación. Intenta más tarde.', 'dimsop' ), 'db_error', 500 );
        return;
    }

    $meta = array(
        '_dimsop_app_name'        => $name,
        '_dimsop_app_email'       => $email,
        '_dimsop_app_phone'       => $phone,
        '_dimsop_app_profession'  => $profession,
        '_dimsop_app_city'        => $city,
        '_dimsop_app_cv_file'     => $relative, // relativa a la carpeta privada
        '_dimsop_app_cv_filename' => $cv['name'],
        '_dimsop_app_status'      => 'draft',
        '_dimsop_app_ip'          => dimsop_get_client_ip(),
    );
    foreach ( $meta as $key => $value ) {
        update_post_meta( $app_id, $key, $value );
    }

    dimsop_send_application_notification( $app_id, $meta, $path );

    $respond( true, $success_message, 'enviada', 200 );
}
add_action( 'wp_ajax_dimsop_submit_application', 'dimsop_handle_careers_form' );
add_action( 'wp_ajax_nopriv_dimsop_submit_application', 'dimsop_handle_careers_form' );
add_action( 'admin_post_dimsop_submit_application', 'dimsop_handle_careers_form' );
add_action( 'admin_post_nopriv_dimsop_submit_application', 'dimsop_handle_careers_form' );

/**
 * Aviso a talento@ con el CV adjunto (las postulaciones solo las ven administradores
 * en el panel, así que el equipo de talento lo recibe por correo).
 */
function dimsop_send_application_notification( $app_id, $meta, $cv_path ) {
    $to      = dimsop_get_option( 'careers_email', get_option( 'admin_email' ) );
    $subject = sprintf( '[Dimsop Web] Nueva postulación: %s', $meta['_dimsop_app_name'] );
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        sprintf( 'Reply-To: %s <%s>', $meta['_dimsop_app_name'], $meta['_dimsop_app_email'] ),
    );

    $rows = array(
        __( 'Candidato', 'dimsop' )            => $meta['_dimsop_app_name'],
        __( 'Correo', 'dimsop' )               => $meta['_dimsop_app_email'],
        __( 'Teléfono', 'dimsop' )             => $meta['_dimsop_app_phone'] ? $meta['_dimsop_app_phone'] : __( 'No indicado', 'dimsop' ),
        __( 'Profesión', 'dimsop' )            => $meta['_dimsop_app_profession'] ? $meta['_dimsop_app_profession'] : __( 'No indicada', 'dimsop' ),
        __( 'Ciudad de residencia', 'dimsop' ) => $meta['_dimsop_app_city'] ? $meta['_dimsop_app_city'] : __( 'No indicada', 'dimsop' ),
        __( 'CV', 'dimsop' )                   => $meta['_dimsop_app_cv_filename'] . ' ' . __( '(adjunto)', 'dimsop' ),
    );

    $body  = '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;line-height:1.6;color:#1d2a44;background:#f4f7fb;margin:0;padding:20px;">';
    $body .= '<div style="max-width:600px;margin:auto;background:#fff;border-radius:10px;overflow:hidden;border:1px solid #dce3ec;">';
    $body .= '<div style="background:#003362;padding:24px;text-align:center;"><h2 style="color:#fff;margin:0;font-size:20px;">' . esc_html__( 'Nueva postulación: Forma parte de nuestro equipo', 'dimsop' ) . '</h2></div>';
    $body .= '<div style="padding:24px;"><table style="width:100%;border-collapse:collapse;">';
    foreach ( $rows as $label => $value ) {
        $body .= sprintf( '<tr><td style="padding:8px 0;font-weight:bold;width:35%%;">%s:</td><td style="padding:8px 0;">%s</td></tr>', esc_html( $label ), esc_html( $value ) );
    }
    $body .= '</table>';
    $body .= sprintf(
        '<div style="margin-top:24px;text-align:center;"><a href="%s" style="background:#4da32f;color:#fff;text-decoration:none;padding:10px 20px;border-radius:6px;font-weight:bold;display:inline-block;">%s</a></div>',
        esc_url( admin_url( 'post.php?post=' . (int) $app_id . '&action=edit' ) ),
        esc_html__( 'Ver en el panel (solo administradores)', 'dimsop' )
    );
    $body .= '</div></div></body></html>';

    // El adjunto se envía con su nombre original legible, desde una copia temporal.
    $attachment = array();
    $tmp_dir    = trailingslashit( get_temp_dir() ) . 'dimsop-cv-' . bin2hex( random_bytes( 6 ) );
    if ( wp_mkdir_p( $tmp_dir ) && copy( $cv_path, $tmp_dir . '/' . $meta['_dimsop_app_cv_filename'] ) ) {
        $attachment[] = $tmp_dir . '/' . $meta['_dimsop_app_cv_filename'];
    }

    wp_mail( $to, $subject, $body, $headers, $attachment );

    foreach ( $attachment as $file ) {
        wp_delete_file( $file );
    }
    if ( is_dir( $tmp_dir ) ) {
        @rmdir( $tmp_dir ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_operations_rmdir
    }
}

/**
 * Al vaciar la papelera (borrado definitivo) se elimina también el CV del disco.
 */
function dimsop_delete_application_cv( $post_id ) {
    if ( 'dimsop_job_app' !== get_post_type( $post_id ) ) {
        return;
    }
    // Solo se borra si el archivo está dentro de la carpeta privada.
    $real = dimsop_cv_safe_path( dimsop_cv_absolute_path( get_post_meta( $post_id, '_dimsop_app_cv_file', true ) ) );
    if ( $real ) {
        wp_delete_file( $real );
    }
}
add_action( 'before_delete_post', 'dimsop_delete_application_cv' );
