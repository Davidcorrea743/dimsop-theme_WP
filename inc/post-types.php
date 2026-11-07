<?php
/**
 * Registro de Custom Post Types (CPTs), Taxonomías y Metadatos del Tema Dimsop
 *
 * Modelado de:
 * 1. Proyectos Referenciales (`dimsop_project`)
 * 2. Aliados Tecnológicos (`dimsop_partner`)
 * 3. Unidades de Negocio (`dimsop_unit`)
 * 4. Prospectos / Contactos CRM (`dimsop_lead`) - Privado
 * 5. Postulaciones Laborales (`dimsop_job_app`) - Privado y Seguro
 *
 * @package Dimsop
 * @subpackage DataLayer
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * 1. Registro de Custom Post Types
 */
function dimsop_register_post_types() {

    // -------------------------------------------------------------
    // CPT: Proyectos Referenciales
    // -------------------------------------------------------------
    $labels_projects = array(
        'name'               => esc_html__( 'Proyectos', 'dimsop' ),
        'singular_name'      => esc_html__( 'Proyecto Referencial', 'dimsop' ),
        'menu_name'          => esc_html__( 'Proyectos Dimsop', 'dimsop' ),
        'add_new'            => esc_html__( 'Añadir Nuevo', 'dimsop' ),
        'add_new_item'       => esc_html__( 'Añadir Nuevo Proyecto', 'dimsop' ),
        'edit_item'          => esc_html__( 'Editar Proyecto', 'dimsop' ),
        'new_item'           => esc_html__( 'Nuevo Proyecto', 'dimsop' ),
        'view_item'          => esc_html__( 'Ver Proyecto', 'dimsop' ),
        'search_items'       => esc_html__( 'Buscar Proyectos', 'dimsop' ),
        'not_found'          => esc_html__( 'No se encontraron proyectos', 'dimsop' ),
        'not_found_in_trash' => esc_html__( 'No hay proyectos en la papelera', 'dimsop' ),
    );

    register_post_type( 'dimsop_project', array(
        'labels'              => $labels_projects,
        'public'              => true,
        'has_archive'         => false,
        'publicly_queryable'  => false, // Se consumen en la home y modales
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-portfolio',
        'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
        'rewrite'             => false,
        'show_in_rest'        => false,
    ) );

    // -------------------------------------------------------------
    // CPT: Aliados Tecnológicos (Logos de Marcas)
    // -------------------------------------------------------------
    $labels_partners = array(
        'name'               => esc_html__( 'Aliados', 'dimsop' ),
        'singular_name'      => esc_html__( 'Aliado Tecnológico', 'dimsop' ),
        'menu_name'          => esc_html__( 'Aliados (Marcas)', 'dimsop' ),
        'add_new'            => esc_html__( 'Añadir Aliado', 'dimsop' ),
        'add_new_item'       => esc_html__( 'Añadir Nuevo Aliado', 'dimsop' ),
        'edit_item'          => esc_html__( 'Editar Aliado', 'dimsop' ),
        'all_items'          => esc_html__( 'Todos los Aliados', 'dimsop' ),
        'search_items'       => esc_html__( 'Buscar Aliados', 'dimsop' ),
    );

    register_post_type( 'dimsop_partner', array(
        'labels'              => $labels_partners,
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-networking',
        'supports'            => array( 'title', 'thumbnail', 'page-attributes' ),
        'show_in_rest'        => false,
    ) );

    // -------------------------------------------------------------
    // CPT: Unidades de Negocio
    // -------------------------------------------------------------
    $labels_units = array(
        'name'               => esc_html__( 'Unidades de Negocio', 'dimsop' ),
        'singular_name'      => esc_html__( 'Unidad de Negocio', 'dimsop' ),
        'menu_name'          => esc_html__( 'Unidades TIC', 'dimsop' ),
        'add_new'            => esc_html__( 'Añadir Unidad', 'dimsop' ),
        'add_new_item'       => esc_html__( 'Añadir Nueva Unidad de Negocio', 'dimsop' ),
        'edit_item'          => esc_html__( 'Editar Unidad', 'dimsop' ),
        'all_items'          => esc_html__( 'Todas las Unidades', 'dimsop' ),
    );

    register_post_type( 'dimsop_unit', array(
        'labels'              => $labels_units,
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-screenoptions',
        'supports'            => array( 'title', 'editor', 'excerpt', 'page-attributes' ),
        'show_in_rest'        => false,
    ) );

    // -------------------------------------------------------------
    // CPT: Prospectos / Contactos (CRM Leads) - Totalmente Privado
    // -------------------------------------------------------------
    $labels_leads = array(
        'name'               => esc_html__( 'Prospectos (CRM)', 'dimsop' ),
        'singular_name'      => esc_html__( 'Prospecto', 'dimsop' ),
        'menu_name'          => esc_html__( 'Prospectos CRM', 'dimsop' ),
        'all_items'          => esc_html__( 'Todos los Prospectos', 'dimsop' ),
        'edit_item'          => esc_html__( 'Ver Prospecto', 'dimsop' ),
        'view_item'          => esc_html__( 'Ver Prospecto', 'dimsop' ),
        'search_items'       => esc_html__( 'Buscar Prospectos', 'dimsop' ),
        'not_found'          => esc_html__( 'No hay prospectos registrados', 'dimsop' ),
    );

    register_post_type( 'dimsop_lead', array(
        'labels'              => $labels_leads,
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-id-alt',
        'supports'            => array( 'title' ),
        // Contienen datos personales: solo administradores y editores (los únicos con
        // edit_others_posts), también por URL directa.
        'capabilities'        => array(
            'create_posts'           => 'do_not_allow', // Solo el formulario puede crear
            'edit_post'              => 'edit_others_posts',
            'read_post'              => 'edit_others_posts',
            'delete_post'            => 'edit_others_posts',
            'edit_posts'             => 'edit_others_posts',
            'edit_others_posts'      => 'edit_others_posts',
            'edit_private_posts'     => 'edit_others_posts',
            'edit_published_posts'   => 'edit_others_posts',
            'publish_posts'          => 'edit_others_posts',
            'read_private_posts'     => 'edit_others_posts',
            'delete_posts'           => 'edit_others_posts',
            'delete_others_posts'    => 'edit_others_posts',
            'delete_private_posts'   => 'edit_others_posts',
            'delete_published_posts' => 'edit_others_posts',
        ),
        'map_meta_cap'        => false,
        'show_in_rest'        => false,
        'exclude_from_search' => true,
    ) );

    // -------------------------------------------------------------
    // CPT: Postulaciones Laborales (CVs) - Totalmente Privado
    // -------------------------------------------------------------
    $labels_apps = array(
        'name'               => esc_html__( 'Postulaciones', 'dimsop' ),
        'singular_name'      => esc_html__( 'Postulación Laboral', 'dimsop' ),
        'menu_name'          => esc_html__( 'Postulaciones / CVs', 'dimsop' ),
        'all_items'          => esc_html__( 'Todas las Postulaciones', 'dimsop' ),
        'edit_item'          => esc_html__( 'Ver Postulación', 'dimsop' ),
        'view_item'          => esc_html__( 'Ver Postulación', 'dimsop' ),
        'search_items'       => esc_html__( 'Buscar Postulaciones', 'dimsop' ),
        'not_found'          => esc_html__( 'No hay postulaciones registradas', 'dimsop' ),
    );

    register_post_type( 'dimsop_job_app', array(
        'labels'              => $labels_apps,
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-businessman',
        'supports'            => array( 'title' ),
        // Contienen datos personales y CVs: solo administradores, también por URL directa.
        'capabilities'        => array(
            'create_posts'           => 'do_not_allow', // Solo el formulario público puede crear
            'edit_post'              => 'manage_options',
            'read_post'              => 'manage_options',
            'delete_post'            => 'manage_options',
            'edit_posts'             => 'manage_options',
            'edit_others_posts'      => 'manage_options',
            'edit_private_posts'     => 'manage_options',
            'edit_published_posts'   => 'manage_options',
            'publish_posts'          => 'manage_options',
            'read_private_posts'     => 'manage_options',
            'delete_posts'           => 'manage_options',
            'delete_others_posts'    => 'manage_options',
            'delete_private_posts'   => 'manage_options',
            'delete_published_posts' => 'manage_options',
        ),
        'map_meta_cap'        => false,
        'show_in_rest'        => false,
        'exclude_from_search' => true,
    ) );
}
add_action( 'init', 'dimsop_register_post_types' );

/**
 * 2. Catálogo de Servicios de Interés
 */
function dimsop_get_service_options() {
    return array(
        'cloud'    => esc_html__( 'Soluciones en la Nube', 'dimsop' ),
        'network'  => esc_html__( 'Productos y Soluciones de Redes', 'dimsop' ),
        'security' => esc_html__( 'Ciberseguridad Avanzada', 'dimsop' ),
        'managed'  => esc_html__( 'Servicios Gestionados', 'dimsop' ),
        'fttx_isp' => esc_html__( 'Soluciones FTTX / ISP', 'dimsop' ),
        'unified'  => esc_html__( 'Comunicaciones Unificadas', 'dimsop' ),
        'ia'       => esc_html__( 'Inteligencia Artificial y Desarrollo', 'dimsop' ),
        'other'    => esc_html__( 'Otro', 'dimsop' ),
    );
}

/**
 * 3. Meta Boxes de Administración
 */
function dimsop_register_meta_boxes() {
    // Meta box para Proyectos
    add_meta_box(
        'dimsop_project_details',
        esc_html__( 'Detalles del Proyecto Referencial', 'dimsop' ),
        'dimsop_render_project_metabox',
        'dimsop_project',
        'normal',
        'high'
    );

    // Meta box para Unidades de Negocio
    add_meta_box(
        'dimsop_unit_details',
        esc_html__( 'Configuración de Unidad TIC', 'dimsop' ),
        'dimsop_render_unit_metabox',
        'dimsop_unit',
        'normal',
        'high'
    );

    // Meta box para Prospectos CRM (Solo Lectura Administrador)
    add_meta_box(
        'dimsop_lead_details',
        esc_html__( 'Información del Prospecto', 'dimsop' ),
        'dimsop_render_lead_metabox',
        'dimsop_lead',
        'normal',
        'high'
    );

    // Meta box para Postulaciones Laborales (Solo Lectura y Descarga Segura)
    add_meta_box(
        'dimsop_app_details',
        esc_html__( 'Información del Candidato y CV', 'dimsop' ),
        'dimsop_render_app_metabox',
        'dimsop_job_app',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'dimsop_register_meta_boxes' );

/**
 * Render Meta Box: Proyectos
 */
function dimsop_render_project_metabox( $post ) {
    wp_nonce_field( 'dimsop_project_save', 'dimsop_project_nonce' );

    $client = get_post_meta( $post->ID, '_dimsop_client_name', true );
    $sector = get_post_meta( $post->ID, '_dimsop_project_sector', true );
    $tech   = get_post_meta( $post->ID, '_dimsop_project_tech', true );
    ?>
    <p>
        <label for="dimsop_client_name"><strong><?php esc_html_e( 'Nombre del Cliente:', 'dimsop' ); ?></strong></label><br>
        <input type="text" id="dimsop_client_name" name="dimsop_client_name" value="<?php echo esc_attr( $client ); ?>" class="widefat">
    </p>
    <p>
        <label for="dimsop_project_sector"><strong><?php esc_html_e( 'Sector / Industria:', 'dimsop' ); ?></strong></label><br>
        <input type="text" id="dimsop_project_sector" name="dimsop_project_sector" value="<?php echo esc_attr( $sector ); ?>" class="widefat" placeholder="Ej: Telecomunicaciones, Banca, Salud...">
    </p>
    <p>
        <label for="dimsop_project_tech"><strong><?php esc_html_e( 'Tecnologías / Marcas implementadas:', 'dimsop' ); ?></strong></label><br>
        <input type="text" id="dimsop_project_tech" name="dimsop_project_tech" value="<?php echo esc_attr( $tech ); ?>" class="widefat" placeholder="Ej: CISCO, Fortinet, AWS...">
    </p>
    <?php
}

/**
 * Render Meta Box: Unidades de Negocio
 */
function dimsop_render_unit_metabox( $post ) {
    wp_nonce_field( 'dimsop_unit_save', 'dimsop_unit_nonce' );

    $icon = get_post_meta( $post->ID, '_dimsop_unit_icon', true );
    ?>
    <p>
        <label for="dimsop_unit_icon"><strong><?php esc_html_e( 'Clase del Icono FontAwesome:', 'dimsop' ); ?></strong></label><br>
        <input type="text" id="dimsop_unit_icon" name="dimsop_unit_icon" value="<?php echo esc_attr( $icon ); ?>" class="widefat" placeholder="fa fa-cloud, fa fa-shield, fa fa-sitemap...">
        <small class="text-muted"><?php esc_html_e( 'Ejemplo: fa fa-cloud para nube, fa fa-shield para ciberseguridad.', 'dimsop' ); ?></small>
    </p>
    <?php
}

/**
 * Render Meta Box: Prospectos CRM
 */
function dimsop_render_lead_metabox( $post ) {
    $name     = get_post_meta( $post->ID, '_dimsop_lead_name', true );
    $email    = get_post_meta( $post->ID, '_dimsop_lead_email', true );
    $phone    = get_post_meta( $post->ID, '_dimsop_lead_phone', true );
    $company  = get_post_meta( $post->ID, '_dimsop_lead_company', true );
    $position = get_post_meta( $post->ID, '_dimsop_lead_position', true );
    $service  = get_post_meta( $post->ID, '_dimsop_lead_service', true );
    $message  = get_post_meta( $post->ID, '_dimsop_lead_message', true );
    $ip       = get_post_meta( $post->ID, '_dimsop_lead_ip', true );

    $services = dimsop_get_service_options();
    $service_label = isset( $services[ $service ] ) ? $services[ $service ] : ( $service ? $service : esc_html__( 'No especificado', 'dimsop' ) );
    ?>
    <table class="form-table" role="presentation">
        <tr>
            <th><strong><?php esc_html_e( 'Nombre Completo:', 'dimsop' ); ?></strong></th>
            <td><?php echo esc_html( $name ); ?></td>
        </tr>
        <tr>
            <th><strong><?php esc_html_e( 'Correo Electrónico:', 'dimsop' ); ?></strong></th>
            <td><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></td>
        </tr>
        <tr>
            <th><strong><?php esc_html_e( 'Teléfono:', 'dimsop' ); ?></strong></th>
            <td><a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a></td>
        </tr>
        <tr>
            <th><strong><?php esc_html_e( 'Empresa:', 'dimsop' ); ?></strong></th>
            <td><?php echo esc_html( $company ? $company : '-' ); ?></td>
        </tr>
        <tr>
            <th><strong><?php esc_html_e( 'Cargo:', 'dimsop' ); ?></strong></th>
            <td><?php echo esc_html( $position ? $position : '-' ); ?></td>
        </tr>
        <tr>
            <th><strong><?php esc_html_e( 'Servicio de Interés:', 'dimsop' ); ?></strong></th>
            <td><span class="badge" style="background:#003362;color:#fff;padding:4px 8px;border-radius:4px;"><?php echo esc_html( $service_label ); ?></span></td>
        </tr>
        <tr>
            <th><strong><?php esc_html_e( 'Mensaje / Motivo:', 'dimsop' ); ?></strong></th>
            <td><div style="background:#f8f9fa;padding:12px;border-radius:6px;border:1px solid #e2e8f0;white-space:pre-wrap;"><?php echo esc_html( $message ? $message : '-' ); ?></div></td>
        </tr>
        <tr>
            <th><strong><?php esc_html_e( 'IP de Registro:', 'dimsop' ); ?></strong></th>
            <td><code><?php echo esc_html( $ip ? $ip : '-' ); ?></code></td>
        </tr>
    </table>
    <?php
}

/**
 * Render Meta Box: Postulaciones Laborales (CV)
 */
function dimsop_render_app_metabox( $post ) {
    $name       = get_post_meta( $post->ID, '_dimsop_app_name', true );
    $email      = get_post_meta( $post->ID, '_dimsop_app_email', true );
    $phone      = get_post_meta( $post->ID, '_dimsop_app_phone', true );
    $profession = get_post_meta( $post->ID, '_dimsop_app_profession', true );
    $city       = get_post_meta( $post->ID, '_dimsop_app_city', true );
    $cv_file    = get_post_meta( $post->ID, '_dimsop_app_cv_file', true );
    $cv_name    = get_post_meta( $post->ID, '_dimsop_app_cv_filename', true );
    $status     = get_post_meta( $post->ID, '_dimsop_app_status', true );
    $ip         = get_post_meta( $post->ID, '_dimsop_app_ip', true );

    // URL segura para descarga autenticada
    $download_url = add_query_arg( array(
        'action' => 'dimsop_download_cv',
        'app_id' => $post->ID,
        'nonce'  => wp_create_nonce( 'dimsop_download_cv_' . $post->ID ),
    ), admin_url( 'admin-post.php' ) );
    ?>
    <table class="form-table" role="presentation">
        <tr>
            <th><strong><?php esc_html_e( 'Candidato:', 'dimsop' ); ?></strong></th>
            <td><?php echo esc_html( $name ); ?></td>
        </tr>
        <tr>
            <th><strong><?php esc_html_e( 'Correo Electrónico:', 'dimsop' ); ?></strong></th>
            <td><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></td>
        </tr>
        <tr>
            <th><strong><?php esc_html_e( 'Teléfono:', 'dimsop' ); ?></strong></th>
            <td><a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a></td>
        </tr>
        <tr>
            <th><strong><?php esc_html_e( 'Profesión:', 'dimsop' ); ?></strong></th>
            <td><?php echo esc_html( $profession ? $profession : '-' ); ?></td>
        </tr>
        <tr>
            <th><strong><?php esc_html_e( 'Ciudad de Residencia:', 'dimsop' ); ?></strong></th>
            <td><?php echo esc_html( $city ? $city : '-' ); ?></td>
        </tr>
        <tr>
            <th><strong><?php esc_html_e( 'Curriculum Vitae (CV):', 'dimsop' ); ?></strong></th>
            <td>
                <?php if ( ! empty( $cv_file ) ) : ?>
                    <a href="<?php echo esc_url( $download_url ); ?>" class="button button-primary">
                        <span class="dashicons dashicons-media-document" style="margin-top:4px;"></span>
                        <?php echo esc_html__( 'Descargar CV Protegido', 'dimsop' ) . ' (' . esc_html( $cv_name ? $cv_name : 'Documento' ) . ')'; ?>
                    </a>
                <?php else : ?>
                    <span class="text-muted"><?php esc_html_e( 'Sin archivo adjunto', 'dimsop' ); ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><strong><?php esc_html_e( 'IP de Postulación:', 'dimsop' ); ?></strong></th>
            <td><code><?php echo esc_html( $ip ? $ip : '-' ); ?></code></td>
        </tr>
    </table>
    <?php
}

/**
 * 4. Guardado Seguro de Metadatos (Proyectos y Unidades)
 */
function dimsop_save_custom_meta( $post_id ) {
    // Evitar guardado en autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Comprobar permisos
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Guardado de Proyectos
    if ( isset( $_POST['dimsop_project_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dimsop_project_nonce'] ) ), 'dimsop_project_save' ) ) {
        if ( isset( $_POST['dimsop_client_name'] ) ) {
            update_post_meta( $post_id, '_dimsop_client_name', sanitize_text_field( wp_unslash( $_POST['dimsop_client_name'] ) ) );
        }
        if ( isset( $_POST['dimsop_project_sector'] ) ) {
            update_post_meta( $post_id, '_dimsop_project_sector', sanitize_text_field( wp_unslash( $_POST['dimsop_project_sector'] ) ) );
        }
        if ( isset( $_POST['dimsop_project_tech'] ) ) {
            update_post_meta( $post_id, '_dimsop_project_tech', sanitize_text_field( wp_unslash( $_POST['dimsop_project_tech'] ) ) );
        }
    }

    // Guardado de Unidades de Negocio
    if ( isset( $_POST['dimsop_unit_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dimsop_unit_nonce'] ) ), 'dimsop_unit_save' ) ) {
        if ( isset( $_POST['dimsop_unit_icon'] ) ) {
            update_post_meta( $post_id, '_dimsop_unit_icon', sanitize_text_field( wp_unslash( $_POST['dimsop_unit_icon'] ) ) );
        }
    }
}
add_action( 'save_post', 'dimsop_save_custom_meta' );

/**
 * 5. Columnas Personalizadas en el Panel de Administración (UX Optimizada)
 */

// Columnas para Prospectos CRM
add_filter( 'manage_dimsop_lead_posts_columns', function( $columns ) {
    return array(
        'cb'       => $columns['cb'],
        'title'    => esc_html__( 'Contacto', 'dimsop' ),
        'email'    => esc_html__( 'Correo', 'dimsop' ),
        'phone'    => esc_html__( 'Teléfono', 'dimsop' ),
        'company'  => esc_html__( 'Empresa', 'dimsop' ),
        'service'  => esc_html__( 'Servicio', 'dimsop' ),
        'date'     => esc_html__( 'Fecha', 'dimsop' ),
    );
} );

add_action( 'manage_dimsop_lead_posts_custom_column', function( $column, $post_id ) {
    switch ( $column ) {
        case 'email':
            $email = get_post_meta( $post_id, '_dimsop_lead_email', true );
            echo esc_html( $email ? $email : '-' );
            break;
        case 'phone':
            $phone = get_post_meta( $post_id, '_dimsop_lead_phone', true );
            echo esc_html( $phone ? $phone : '-' );
            break;
        case 'company':
            $company = get_post_meta( $post_id, '_dimsop_lead_company', true );
            echo esc_html( $company ? $company : '-' );
            break;
        case 'service':
            $service = get_post_meta( $post_id, '_dimsop_lead_service', true );
            $services = dimsop_get_service_options();
            echo esc_html( isset( $services[ $service ] ) ? $services[ $service ] : ( $service ? $service : '-' ) );
            break;
    }
}, 10, 2 );

// Columnas para Postulaciones / CVs
add_filter( 'manage_dimsop_job_app_posts_columns', function( $columns ) {
    return array(
        'cb'         => $columns['cb'],
        'title'      => esc_html__( 'Candidato', 'dimsop' ),
        'profession' => esc_html__( 'Profesión', 'dimsop' ),
        'city'       => esc_html__( 'Ciudad', 'dimsop' ),
        'cv'         => esc_html__( 'CV Adjunto', 'dimsop' ),
        'date'       => esc_html__( 'Fecha de Recepción', 'dimsop' ),
    );
} );

add_action( 'manage_dimsop_job_app_posts_custom_column', function( $column, $post_id ) {
    switch ( $column ) {
        case 'profession':
            $prof = get_post_meta( $post_id, '_dimsop_app_profession', true );
            echo esc_html( $prof ? $prof : '-' );
            break;
        case 'city':
            $city = get_post_meta( $post_id, '_dimsop_app_city', true );
            echo esc_html( $city ? $city : '-' );
            break;
        case 'cv':
            $cv_file = get_post_meta( $post_id, '_dimsop_app_cv_file', true );
            if ( ! empty( $cv_file ) ) {
                $download_url = add_query_arg( array(
                    'action' => 'dimsop_download_cv',
                    'app_id' => $post_id,
                    'nonce'  => wp_create_nonce( 'dimsop_download_cv_' . $post_id ),
                ), admin_url( 'admin-post.php' ) );
                echo '<a href="' . esc_url( $download_url ) . '" class="button button-small"><span class="dashicons dashicons-download" style="font-size:14px;line-height:1.7;"></span> ' . esc_html__( 'Descargar', 'dimsop' ) . '</a>';
            } else {
                echo '<span class="text-muted">' . esc_html__( 'Sin archivo', 'dimsop' ) . '</span>';
            }
            break;
    }
}, 10, 2 );

/**
 * 6. Endpoint de Descarga Autenticada y Segura de Archivos CV
 *
 * Los archivos NUNCA se sirven con URL directa; este endpoint verifica permisos ('manage_options')
 * y tokens criptográficos antes de enviar los bytes al navegador.
 */
function dimsop_handle_secure_cv_download() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'Acceso denegado: Se requieren permisos administrativos para acceder a este CV.', 'dimsop' ), 403 );
    }

    $app_id = isset( $_GET['app_id'] ) ? absint( $_GET['app_id'] ) : 0;
    $nonce  = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';

    if ( ! $app_id || ! wp_verify_nonce( $nonce, 'dimsop_download_cv_' . $app_id ) ) {
        wp_die( esc_html__( 'Enlace de descarga no válido o expirado.', 'dimsop' ), 403 );
    }

    $file_path = get_post_meta( $app_id, '_dimsop_app_cv_file', true );
    $file_name = get_post_meta( $app_id, '_dimsop_app_cv_filename', true );

    if ( empty( $file_path ) || ! file_exists( $file_path ) ) {
        wp_die( esc_html__( 'El archivo solicitado ya no existe en el servidor.', 'dimsop' ), 404 );
    }

    // Servir el archivo con cabeceras estrictas
    $ext = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );
    $mime_types = array(
        'pdf'  => 'application/pdf',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    );
    $content_type = isset( $mime_types[ $ext ] ) ? $mime_types[ $ext ] : 'application/octet-stream';

    header( 'Content-Description: File Transfer' );
    header( 'Content-Type: ' . $content_type );
    header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $file_name ? $file_name : 'cv-' . $app_id . '.' . $ext ) . '"' );
    header( 'Content-Transfer-Encoding: binary' );
    header( 'Expires: 0' );
    header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
    header( 'Pragma: public' );
    header( 'Content-Length: ' . filesize( $file_path ) );
    header( 'X-Content-Type-Options: nosniff' );

    readfile( $file_path );
    exit;
}
add_action( 'admin_post_dimsop_download_cv', 'dimsop_handle_secure_cv_download' );

/**
 * 7. Funciones Helper para las Plantillas Frontend
 */

/**
 * Obtener listado de proyectos referenciales
 */
function dimsop_get_projects( $limit = -1 ) {
    return new WP_Query( array(
        'post_type'      => 'dimsop_project',
        'posts_per_page' => $limit,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order title',
        'order'          => 'ASC',
    ) );
}

/**
 * Obtener listado de aliados (marcas)
 */
function dimsop_get_partners( $limit = -1 ) {
    return new WP_Query( array(
        'post_type'      => 'dimsop_partner',
        'posts_per_page' => $limit,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order title',
        'order'          => 'ASC',
    ) );
}

/**
 * Obtener listado de unidades de negocio
 */
function dimsop_get_units( $limit = 8 ) {
    return new WP_Query( array(
        'post_type'      => 'dimsop_unit',
        'posts_per_page' => $limit,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order date',
        'order'          => 'ASC',
    ) );
}
