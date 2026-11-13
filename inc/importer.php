<?php
/**
 * Importador del contenido inicial migrado desde Odoo
 *
 * Crea unidades de negocio, aliados y proyectos con sus logos (tomados de
 * assets/images del tema y copiados a la Biblioteca de medios). Es idempotente:
 * si ya existe una entrada con el mismo título, se deja como está.
 *
 * @package Dimsop
 * @subpackage Importer
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Unidades de negocio (orden e iconos Font Awesome 4.7 del sitio Odoo).
 *
 * @return array<int, array{title: string, excerpt: string, icon: string}>
 */
function dimsop_seed_units() {
    return array(
        array( 'title' => 'Soluciones en la Nube',                            'icon' => 'fa-cloud',     'excerpt' => 'Arquitecturas híbridas, privadas y públicas con máxima escalabilidad.' ),
        array( 'title' => 'Productos y Soluciones de Redes',                  'icon' => 'fa-sitemap',   'excerpt' => 'Conectividad de alto rendimiento e infraestructura corporativa.' ),
        array( 'title' => 'Ciberseguridad Avanzada',                          'icon' => 'fa-shield',    'excerpt' => 'Protección perimetral, detección de amenazas y blindaje de activos.' ),
        array( 'title' => 'Servicios Gestionados',                            'icon' => 'fa-cogs',      'excerpt' => 'Monitoreo 24/7 y gestión integral de la infraestructura de TI.' ),
        array( 'title' => 'Soluciones FTTX / ISP',                            'icon' => 'fa-exchange',  'excerpt' => 'Despliegue y equipamiento para redes masivas de fibra óptica.' ),
        array( 'title' => 'Comunicaciones Unificadas',                        'icon' => 'fa-phone',     'excerpt' => 'Integración de voz, video y colaboración empresarial.' ),
        array( 'title' => 'Unidad de Desarrollo e Inteligencia Artificial',   'icon' => 'fa-microchip', 'excerpt' => 'Desarrollo de software, automatización y soluciones con IA.' ),
    );
}

/**
 * Aliados tecnológicos: título => archivo en assets/images/logos/aliados.
 *
 * @return array<string, string>
 */
function dimsop_seed_partners() {
    return array(
        'Hewlett Packard' => 'hewlett-packard', 'HPE Aruba' => 'hpe-aruba', 'HPE Juniper' => 'hpe-juniper',
        'HP' => 'hp', 'QNAP' => 'qnap', 'Ruckus' => 'ruckus', 'Cisco' => 'cisco', 'Fortinet' => 'fortinet',
        'TP-Link' => 'tp-link', 'Huawei' => 'huawei', 'Zabbix' => 'zabbix', 'Grafana' => 'grafana',
        'Grandstream' => 'grandstream', 'Yeastar' => 'yeastar', 'Microsoft' => 'microsoft', 'AWS' => 'aws',
        'Red Hat' => 'red-hat', 'Panduit' => 'panduit', 'Siemon' => 'siemon', 'Data WiFi' => 'datawifi',
        'SocialWiFi' => 'socialwifi', 'V-Count' => 'v-count', 'Ahua' => 'ahua', 'Rosslare' => 'rosslare',
        '4lotrack' => '4lotrack',
    );
}

/**
 * Proyectos referenciales: título, archivo en assets/images/projects y descripción.
 *
 * @return array<int, array{title: string, file: string, excerpt: string}>
 */
function dimsop_seed_projects() {
    return array(
        array( 'title' => 'UCM Chile',                 'file' => 'ucm-chile',                 'excerpt' => 'Implementación y soporte de CISCO Contact Center para la Unidad Coronaria Móvil en Chile.' ),
        array( 'title' => 'Policlínica Metropolitana', 'file' => 'policlinica-metropolitana', 'excerpt' => 'Gestión de red de datos y seguridad CISCO para la Policlínica Metropolitana.' ),
        array( 'title' => 'Oleoducto de Colombia',     'file' => 'oleoducto-colombia',        'excerpt' => 'Despliegue de plataforma MMS y Webex CISCO para Oleoducto de Colombia.' ),
        array( 'title' => 'ITS InfoCom',               'file' => 'its-infocom',               'excerpt' => 'Rediseño de infraestructura de red y WiFi para ITS InfoCom en Colombia.' ),
        array( 'title' => 'Avianca',                   'file' => 'avianca',                   'excerpt' => 'Modernización de redes de datos y WiFi para Avianca en Perú y Chile.' ),
        array( 'title' => 'Mi Tickera',                'file' => 'mi-tickera',                'excerpt' => 'Despliegue de infraestructura de red de datos y telefonía IP.' ),
        array( 'title' => 'TODO 1',                    'file' => 'todo1',                     'excerpt' => 'Migración de telefonía IP CISCO para TODO 1, filial del Banco Mercantil.' ),
        array( 'title' => 'Uniseguros',                'file' => 'uniseguros',                'excerpt' => 'Soporte y desarrollo de scripts para CISCO Contact Center.' ),
        // En Odoo estos solo mostraban el nombre; la descripción queda vacía para completarla en el panel.
        array( 'title' => 'Bancamiga',  'file' => 'BANCAMIGA',  'excerpt' => '' ),
        array( 'title' => 'Banplus',    'file' => 'BANPLUS',    'excerpt' => '' ),
        array( 'title' => 'Calox',      'file' => 'CALOX',      'excerpt' => '' ),
        array( 'title' => 'Cinex',      'file' => 'CINEX',      'excerpt' => '' ),
        array( 'title' => 'Digitel',    'file' => 'DIGITEL',    'excerpt' => '' ),
        array( 'title' => 'Farmatodo',  'file' => 'FARMATODO',  'excerpt' => '' ),
        array( 'title' => 'Leti',       'file' => 'LETI',       'excerpt' => '' ),
        array( 'title' => 'Repsol',     'file' => 'REPSOL',     'excerpt' => '' ),
        array( 'title' => 'SimpleTV',   'file' => 'SIMPLETV',   'excerpt' => '' ),
        array( 'title' => 'Telefónica', 'file' => 'TELEFONICA', 'excerpt' => '' ),
        array( 'title' => 'VNET',       'file' => 'VNET',       'excerpt' => '' ),
    );
}

/**
 * Busca una entrada por tipo y título exacto.
 */
function dimsop_find_post_by_title( $post_type, $title ) {
    $query = new WP_Query( array(
        'post_type'              => $post_type,
        'title'                  => $title,
        'post_status'            => 'any',
        'posts_per_page'         => 1,
        'fields'                 => 'ids',
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
    ) );
    return $query->posts ? (int) $query->posts[0] : 0;
}

/**
 * Copia una imagen del tema a la Biblioteca de medios (una sola vez por archivo).
 *
 * @param string $relative Ruta dentro de assets/ (p. ej. images/projects/avianca.webp).
 * @param string $alt      Texto alternativo.
 * @return int ID del adjunto o 0 si falla.
 */
function dimsop_import_theme_image( $relative, $alt ) {
    $existing = get_posts( array(
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'meta_key'       => '_dimsop_source_asset', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- importador de un solo uso.
        'meta_value'     => $relative,              // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
        'posts_per_page' => 1,
        'fields'         => 'ids',
    ) );
    if ( $existing ) {
        return (int) $existing[0];
    }

    $source = DIMSOP_THEME_DIR . '/assets/' . $relative;
    if ( ! file_exists( $source ) ) {
        return 0;
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $tmp = wp_tempnam( basename( $source ) );
    if ( ! $tmp || ! copy( $source, $tmp ) ) {
        return 0;
    }

    $attachment_id = media_handle_sideload( array( 'name' => basename( $source ), 'tmp_name' => $tmp ), 0, $alt );
    if ( is_wp_error( $attachment_id ) ) {
        wp_delete_file( $tmp );
        return 0;
    }

    update_post_meta( $attachment_id, '_dimsop_source_asset', $relative );
    update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );
    return (int) $attachment_id;
}

/**
 * Crea una entrada si no existe y devuelve si fue creada.
 *
 * @param array  $postarr Datos para wp_insert_post.
 * @param array  $meta    Metadatos a guardar.
 * @param string $image   Ruta del logo dentro de assets/ ('' si no lleva).
 */
function dimsop_import_post( $postarr, $meta = array(), $image = '' ) {
    if ( dimsop_find_post_by_title( $postarr['post_type'], $postarr['post_title'] ) ) {
        return false;
    }

    $post_id = wp_insert_post( wp_slash( $postarr + array( 'post_status' => 'publish' ) ), true );
    if ( is_wp_error( $post_id ) ) {
        return false;
    }

    foreach ( $meta as $key => $value ) {
        update_post_meta( $post_id, $key, $value );
    }

    if ( $image ) {
        $attachment_id = dimsop_import_theme_image( $image, $postarr['post_title'] );
        if ( $attachment_id ) {
            set_post_thumbnail( $post_id, $attachment_id );
        }
    }
    return true;
}

/**
 * Texto de la política de privacidad en bloques, en el idioma activo
 * (se llama con switch_to_locale() para generar la versión en inglés).
 *
 * @return string
 */
function dimsop_privacy_page_content() {
    $contact = sanitize_email( dimsop_get_option( 'contact_email', 'mercadeo@dimsop.com' ) );
    $careers = sanitize_email( dimsop_get_option( 'careers_email', 'talento@dimsop.com' ) );

    $paragraph = function ( $text ) {
        return "<!-- wp:paragraph -->\n<p>" . $text . "</p>\n<!-- /wp:paragraph -->\n\n";
    };
    $heading = function ( $text ) {
        return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">" . esc_html( $text ) . "</h2>\n<!-- /wp:heading -->\n\n";
    };
    $list = function ( $items ) {
        $html = "<!-- wp:list -->\n<ul class=\"wp-block-list\">";
        foreach ( $items as $item ) {
            $html .= "<!-- wp:list-item -->\n<li>" . $item . "</li>\n<!-- /wp:list-item -->";
        }
        return $html . "</ul>\n<!-- /wp:list -->\n\n";
    };

    $privacy  = $paragraph( esc_html__( 'Dimsop (Soluciones TIC de Vanguardia) respeta tu privacidad y se compromete a proteger los datos personales que compartes a través de este sitio web. Esta política describe qué datos recopilamos, cómo los usamos y los derechos que te asisten.', 'dimsop' ) );
    $privacy .= $heading( __( '1. Datos que recopilamos', 'dimsop' ) );
    $privacy .= $paragraph( esc_html__( 'Solo recopilamos los datos que nos das voluntariamente en nuestros formularios:', 'dimsop' ) );
    $privacy .= $list( array(
        esc_html__( 'Formulario de contacto: nombre y apellido, correo electrónico, teléfono, empresa, cargo, servicio de interés y tu mensaje.', 'dimsop' ),
        esc_html__( 'Postulación "Forma parte de nuestro equipo": nombre y apellido, correo electrónico, teléfono, profesión, ciudad de residencia y tu CV.', 'dimsop' ),
    ) );
    $privacy .= $paragraph( esc_html__( 'Por seguridad también registramos la dirección IP desde la que se envía cada formulario, para prevenir el envío masivo de mensajes no deseados.', 'dimsop' ) );
    $privacy .= $heading( __( '2. Uso de la información', 'dimsop' ) );
    $privacy .= $paragraph( esc_html__( 'Usamos tus datos únicamente para atender tu solicitud y contactarte, o para evaluar tu postulación en nuestros procesos de selección. No vendemos ni cedemos tu información personal a terceros con fines comerciales.', 'dimsop' ) );
    $privacy .= $heading( __( '3. Conservación y protección', 'dimsop' ) );
    $privacy .= $paragraph( esc_html__( 'Los datos se guardan en sistemas protegidos, con acceso restringido a personal autorizado. Los CV se almacenan en una ubicación privada y solo los administradores pueden descargarlos. Conservamos la información solo el tiempo necesario para las finalidades descritas o para cumplir obligaciones legales.', 'dimsop' ) );
    $privacy .= $heading( __( '4. Cookies y almacenamiento local', 'dimsop' ) );
    $privacy .= $paragraph( esc_html__( 'Este sitio guarda en tu navegador tu preferencia de modo claro u oscuro. No la usamos para identificarte ni la compartimos.', 'dimsop' ) );
    $privacy .= $heading( __( '5. Tus derechos', 'dimsop' ) );
    $privacy .= $paragraph( esc_html__( 'Puedes solicitar en cualquier momento el acceso, la rectificación o la eliminación de tus datos personales escribiéndonos a los correos indicados abajo.', 'dimsop' ) );
    $privacy .= $heading( __( '6. Contacto', 'dimsop' ) );
    $privacy .= $paragraph( sprintf(
        /* translators: 1: correo de contacto, 2: correo de talento */
        esc_html__( 'Para consultas sobre esta política escríbenos a %1$s. Para temas relacionados con tu postulación, a %2$s.', 'dimsop' ),
        '<a href="mailto:' . esc_attr( $contact ) . '">' . esc_html( $contact ) . '</a>',
        '<a href="mailto:' . esc_attr( $careers ) . '">' . esc_html( $careers ) . '</a>'
    ) );

    return $privacy;
}

/**
 * Páginas del sitio: se crean solo si no existe ya una con ese slug.
 *
 * La política de privacidad parte del texto genérico de Odoo, ampliado con la
 * postulación con CV. Es un borrador: el texto legal definitivo lo entrega el cliente.
 *
 * @return int Cantidad de páginas creadas.
 */
function dimsop_seed_pages() {
    $privacy = dimsop_privacy_page_content();

    $pages = array(
        array(
            'post_name'    => 'politica-de-privacidad',
            'post_title'   => __( 'Política de privacidad', 'dimsop' ),
            'post_excerpt' => __( 'Cómo recopilamos, usamos y protegemos tus datos personales.', 'dimsop' ),
            'post_content' => $privacy,
            'template'     => '',
        ),
        array(
            'post_name'    => 'catalogo',
            'post_title'   => __( 'Catálogo', 'dimsop' ),
            'post_excerpt' => '',
            'post_content' => '',
            'template'     => 'page-templates/template-catalogo.php',
        ),
    );

    $created = 0;
    foreach ( $pages as $page ) {
        if ( get_page_by_path( $page['post_name'], OBJECT, 'page' ) ) {
            continue;
        }
        $page_id = wp_insert_post( wp_slash( array(
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'post_name'    => $page['post_name'],
            'post_title'   => $page['post_title'],
            'post_excerpt' => $page['post_excerpt'],
            'post_content' => $page['post_content'],
        ) ), true );

        if ( ! is_wp_error( $page_id ) ) {
            if ( $page['template'] ) {
                update_post_meta( $page_id, '_wp_page_template', $page['template'] );
            }
            if ( 'catalogo' === $page['post_name'] ) {
                // Provisional: fuera del sitemap de Yoast hasta que tenga contenido (la plantilla ya pone noindex).
                update_post_meta( $page_id, '_yoast_wpseo_meta-robots-noindex', '1' );
            }
            if ( 'politica-de-privacidad' === $page['post_name'] ) {
                update_option( 'wp_page_for_privacy_policy', $page_id ); // La enlaza WordPress (registro, comentarios).
            }
            $created++;
        }
    }

    return $created;
}

/**
 * Versión en inglés de las unidades de negocio, por título en español.
 *
 * @return array<string, array{title: string, excerpt: string}>
 */
function dimsop_seed_units_en() {
    return array(
        'Soluciones en la Nube'                          => array( 'title' => 'Cloud Solutions',                               'excerpt' => 'Hybrid, private and public architectures with maximum scalability.' ),
        'Productos y Soluciones de Redes'                => array( 'title' => 'Networking Products and Solutions',            'excerpt' => 'High-performance connectivity and corporate infrastructure.' ),
        'Ciberseguridad Avanzada'                        => array( 'title' => 'Advanced Cybersecurity',                        'excerpt' => 'Perimeter protection, threat detection and asset hardening.' ),
        'Servicios Gestionados'                          => array( 'title' => 'Managed Services',                              'excerpt' => '24/7 monitoring and end-to-end management of your IT infrastructure.' ),
        'Soluciones FTTX / ISP'                          => array( 'title' => 'FTTX / ISP Solutions',                          'excerpt' => 'Deployment and equipment for large-scale fiber-optic networks.' ),
        'Comunicaciones Unificadas'                      => array( 'title' => 'Unified Communications',                        'excerpt' => 'Voice, video and business collaboration, fully integrated.' ),
        'Unidad de Desarrollo e Inteligencia Artificial' => array( 'title' => 'Development and Artificial Intelligence Unit', 'excerpt' => 'Software development, automation and AI-powered solutions.' ),
    );
}

/**
 * Crea la versión en inglés del contenido y la enlaza con la española en Polylang:
 * las 7 unidades de negocio y las páginas Privacy policy (/en/privacy/) y Catalog (/en/catalog/).
 *
 * Se puede ejecutar varias veces: solo crea lo que aún no tiene traducción.
 * El contenido existente sin idioma se marca como español (idioma por defecto).
 *
 * @return int Cantidad de traducciones creadas.
 */
function dimsop_seed_translations() {
    if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_save_post_translations' ) ) {
        return 0;
    }
    $created = 0;

    // Crea la traducción $en_post de $es_id si no existe y enlaza ambas.
    $translate = function ( $es_id, $en_post, $meta = array() ) use ( &$created ) {
        if ( ! pll_get_post_language( $es_id ) ) {
            pll_set_post_language( $es_id, 'es' );
        }
        if ( pll_get_post( $es_id, 'en' ) ) {
            return;
        }
        $en_id = wp_insert_post( wp_slash( $en_post + array( 'post_status' => 'publish' ) ), true );
        if ( is_wp_error( $en_id ) ) {
            return;
        }
        foreach ( $meta as $key => $value ) {
            update_post_meta( $en_id, $key, $value );
        }
        pll_set_post_language( $en_id, 'en' );
        $translations       = pll_get_post_translations( $es_id );
        $translations['es'] = $es_id;
        $translations['en'] = $en_id;
        pll_save_post_translations( $translations );
        $created++;
    };

    // Unidades de negocio
    $units_en = dimsop_seed_units_en();
    $units    = get_posts( array(
        'post_type'   => 'dimsop_unit',
        'post_status' => 'publish',
        'numberposts' => -1,
        'lang'        => '', // todas, también las que aún no tienen idioma
    ) );
    foreach ( $units as $unit ) {
        $lang = pll_get_post_language( $unit->ID );
        if ( 'en' === $lang ) {
            continue;
        }
        if ( ! $lang ) {
            pll_set_post_language( $unit->ID, 'es' );
        }
        if ( ! isset( $units_en[ $unit->post_title ] ) ) {
            continue; // unidad creada a mano: se traduce desde el panel
        }
        $translate(
            $unit->ID,
            array(
                'post_type'    => 'dimsop_unit',
                'post_title'   => $units_en[ $unit->post_title ]['title'],
                'post_excerpt' => $units_en[ $unit->post_title ]['excerpt'],
                'menu_order'   => $unit->menu_order,
            ),
            array( '_dimsop_unit_icon' => get_post_meta( $unit->ID, '_dimsop_unit_icon', true ) )
        );
    }

    // Páginas: el texto en inglés sale de las traducciones del tema (en_US.mo)
    $privacy = get_page_by_path( 'politica-de-privacidad', OBJECT, 'page' );
    $catalog = get_page_by_path( 'catalogo', OBJECT, 'page' );

    $switched = switch_to_locale( 'en_US' );
    if ( $privacy ) {
        $translate( $privacy->ID, array(
            'post_type'    => 'page',
            'post_name'    => 'privacy', // 'privacy-policy' lo ocupa el borrador que crea WordPress
            'post_title'   => __( 'Política de privacidad', 'dimsop' ),
            'post_excerpt' => __( 'Cómo recopilamos, usamos y protegemos tus datos personales.', 'dimsop' ),
            'post_content' => dimsop_privacy_page_content(),
        ) );
    }
    if ( $catalog ) {
        $translate(
            $catalog->ID,
            array(
                'post_type'  => 'page',
                'post_name'  => 'catalog',
                'post_title' => __( 'Catálogo', 'dimsop' ),
            ),
            array(
                '_wp_page_template'                => 'page-templates/template-catalogo.php',
                '_yoast_wpseo_meta-robots-noindex' => '1', // provisional, igual que la española
            )
        );
    }
    if ( $switched ) {
        restore_previous_locale();
    }

    return $created;
}

/**
 * Ejecuta la importación completa.
 *
 * @return array{units: int, partners: int, projects: int, pages: int, translations: int} Cantidad creada por tipo.
 */
function dimsop_import_initial_content() {
    $created = array( 'units' => 0, 'partners' => 0, 'projects' => 0, 'pages' => dimsop_seed_pages() );

    foreach ( dimsop_seed_units() as $i => $unit ) {
        $created['units'] += (int) dimsop_import_post(
            array( 'post_type' => 'dimsop_unit', 'post_title' => $unit['title'], 'post_excerpt' => $unit['excerpt'], 'menu_order' => $i + 1 ),
            array( '_dimsop_unit_icon' => $unit['icon'] )
        );
    }

    $i = 0;
    foreach ( dimsop_seed_partners() as $title => $file ) {
        $created['partners'] += (int) dimsop_import_post(
            array( 'post_type' => 'dimsop_partner', 'post_title' => $title, 'menu_order' => ++$i ),
            array(),
            'images/logos/aliados/' . $file . '.webp'
        );
    }

    foreach ( dimsop_seed_projects() as $i => $project ) {
        $created['projects'] += (int) dimsop_import_post(
            array( 'post_type' => 'dimsop_project', 'post_title' => $project['title'], 'post_excerpt' => $project['excerpt'], 'menu_order' => $i + 1 ),
            array( '_dimsop_client_name' => $project['title'] ),
            'images/projects/' . $project['file'] . '.webp'
        );
    }

    // Al final: las traducciones parten del contenido en español ya creado.
    $created['translations'] = dimsop_seed_translations();

    return $created;
}

/**
 * Manejador del botón "Importar contenido inicial" (admin-post).
 */
function dimsop_handle_import_content() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'No tienes permisos para realizar esta acción.', 'dimsop' ), 403 );
    }
    check_admin_referer( 'dimsop_import_content' );

    // La importación de imágenes puede tardar unos segundos.
    if ( function_exists( 'set_time_limit' ) ) {
        set_time_limit( 120 );
    }

    $created = dimsop_import_initial_content();
    $message = sprintf(
        /* translators: 1: unidades, 2: aliados, 3: proyectos, 4: páginas, 5: traducciones al inglés */
        __( 'Importación terminada. Creados: %1$d unidades, %2$d aliados, %3$d proyectos, %4$d páginas y %5$d traducciones al inglés (lo que ya existía no se tocó).', 'dimsop' ),
        $created['units'],
        $created['partners'],
        $created['projects'],
        $created['pages'],
        $created['translations']
    );

    wp_safe_redirect( add_query_arg( 'dimsop_imported', rawurlencode( $message ), admin_url( 'themes.php?page=dimsop-options' ) ) );
    exit;
}
add_action( 'admin_post_dimsop_import_content', 'dimsop_handle_import_content' );
