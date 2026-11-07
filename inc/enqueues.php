<?php
/**
 * Encolado de Scripts y Estilos para el Tema Dimsop
 *
 * @package Dimsop
 * @subpackage Enqueues
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Script en el <head> para Dark Mode antes del render del DOM
 *
 * Evita el parpadeo blanco (Flash of Unstyled Content - FOUC) al cargar la página.
 */
function dimsop_darkmode_head_script() {
    ?>
    <script>
        (function() {
            try {
                // Oscuro por defecto (branding, igual que el sitio original); claro solo si el visitante lo eligió.
                var savedTheme = localStorage.getItem('dimsop-theme');
                document.documentElement.setAttribute('data-theme', savedTheme === 'light' ? 'light' : 'dark');
            } catch (e) {}
            // Oculta el texto del banner hasta que dimsop-motion.js lo anime; si no llega en 3 s, se muestra igual.
            if (!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches)) {
                var root = document.documentElement;
                root.classList.add('dimsop-motion');
                setTimeout(function() {
                    if (!window.dimsopMotionReady) {
                        root.classList.remove('dimsop-motion');
                    }
                }, 3000);
            }
        })();
    </script>
    <?php
}
add_action( 'wp_head', 'dimsop_darkmode_head_script', 1 );

/**
 * Encolar recursos del Frontend
 */
function dimsop_enqueue_scripts() {
    // 1. Fuentes corporativas (Bebas Neue, Anton, Arimo)
    wp_enqueue_style(
        'dimsop-google-fonts',
        'https://fonts.googleapis.com/css2?family=Anton&family=Bebas+Neue&family=Arimo:wght@400;500;600;700&display=swap',
        array(),
        null
    );

    // 2. Font Awesome 4.7 / 6 para iconografía corporativa
    wp_enqueue_style(
        'dimsop-font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
        array(),
        '4.7.0'
    );

    // 3. Framework Grid y Utilidades (Bootstrap 5.3 CSS)
    wp_enqueue_style(
        'dimsop-bootstrap',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
        array(),
        '5.3.3'
    );

    // 4. Estilos principales del Theme
    $theme_css = '/assets/css/dimsop-theme.css';
    $theme_ver = file_exists( DIMSOP_THEME_DIR . $theme_css ) ? filemtime( DIMSOP_THEME_DIR . $theme_css ) : DIMSOP_THEME_VERSION;
    wp_enqueue_style(
        'dimsop-main-style',
        DIMSOP_THEME_URI . $theme_css,
        array( 'dimsop-bootstrap', 'dimsop-font-awesome' ),
        $theme_ver
    );

    // 5. Bootstrap 5 JS Bundle (incluye Popper para modales y dropdowns)
    wp_enqueue_script(
        'dimsop-bootstrap-js',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
        array(),
        '5.3.3',
        true
    );

    // 6. GSAP 3 y ScrollTrigger para animaciones de alto rendimiento
    wp_enqueue_script(
        'dimsop-gsap',
        'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js',
        array(),
        '3.12.5',
        true
    );

    wp_enqueue_script(
        'dimsop-gsap-scrolltrigger',
        'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js',
        array( 'dimsop-gsap' ),
        '3.12.5',
        true
    );

    // 7. Script Principal de Interacción del Theme
    $theme_js = '/assets/js/dimsop-theme.js';
    $js_ver   = file_exists( DIMSOP_THEME_DIR . $theme_js ) ? filemtime( DIMSOP_THEME_DIR . $theme_js ) : DIMSOP_THEME_VERSION;
    wp_enqueue_script(
        'dimsop-main-js',
        DIMSOP_THEME_URI . $theme_js,
        array( 'dimsop-bootstrap-js', 'dimsop-gsap-scrolltrigger' ),
        $js_ver,
        true
    );

    // 7b. Lenis (desplazamiento con inercia) y capa de animación (GSAP + ScrollTrigger)
    wp_enqueue_script(
        'dimsop-lenis',
        'https://cdn.jsdelivr.net/npm/lenis@1.1.13/dist/lenis.min.js',
        array(),
        '1.1.13',
        true
    );

    $motion_js  = '/assets/js/dimsop-motion.js';
    $motion_ver = file_exists( DIMSOP_THEME_DIR . $motion_js ) ? filemtime( DIMSOP_THEME_DIR . $motion_js ) : DIMSOP_THEME_VERSION;
    wp_enqueue_script(
        'dimsop-motion',
        DIMSOP_THEME_URI . $motion_js,
        array( 'dimsop-gsap-scrolltrigger', 'dimsop-lenis', 'dimsop-main-js' ),
        $motion_ver,
        true
    );

    // Globo 3D: Globe.gl se descarga solo al acercarse a la sección Sedes (ver dimsop-motion.js).
    wp_localize_script(
        'dimsop-motion',
        'dimsopMotion',
        array(
            'globe' => array(
                'script'    => 'https://cdn.jsdelivr.net/npm/globe.gl@2.42.0/dist/globe.gl.min.js',
                'image'     => dimsop_asset_url( 'images/globe/earth-dark.jpg' ),
                'countries' => dimsop_asset_url( 'data/countries-110m.json' ),
                // Clave: nombre del país en el GeoJSON (Natural Earth). Valor: etiqueta en pantalla.
                'projects'  => array(
                    'Mexico'     => __( 'México', 'dimsop' ),
                    'Costa Rica' => __( 'Costa Rica', 'dimsop' ),
                    'Colombia'   => __( 'Colombia', 'dimsop' ),
                    'Peru'       => __( 'Perú', 'dimsop' ),
                    'Chile'      => __( 'Chile', 'dimsop' ),
                    'Argentina'  => __( 'Argentina', 'dimsop' ),
                    'Spain'      => __( 'España', 'dimsop' ),
                ),
                'hqs'       => array(
                    array( 'name' => __( 'Caracas', 'dimsop' ), 'lat' => 10.4806, 'lng' => -66.9036 ),
                    array( 'name' => __( 'Miami', 'dimsop' ), 'lat' => 25.7617, 'lng' => -80.1918 ),
                ),
            ),
        )
    );

    // 8. Localización de datos dinámicos y tokens de seguridad (Nonces)
    wp_localize_script(
        'dimsop-main-js',
        'dimsopSettings',
        array(
            'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
            'nonce'           => wp_create_nonce( 'dimsop_public_nonce' ),
            'whatsappNumber'  => dimsop_get_option( 'whatsapp_number', '582128195400' ),
            'themeUri'        => DIMSOP_THEME_URI,
            'i18n'            => array(
                'submitting'      => esc_html__( 'Enviando...', 'dimsop' ),
                'fileTooLarge'    => esc_html__( 'El archivo excede el tamaño máximo permitido de 5MB.', 'dimsop' ),
                'invalidFileType' => esc_html__( 'Formato de archivo no válido. Solo se admiten archivos PDF, DOC o DOCX.', 'dimsop' ),
                'errorGeneric'    => esc_html__( 'Ocurrió un error. Por favor intente más tarde.', 'dimsop' ),
            ),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'dimsop_enqueue_scripts' );

/**
 * Añadir atributos 'defer' o 'async' a scripts externos para maximizar PageSpeed
 */
function dimsop_defer_scripts( $tag, $handle, $src ) {
    $defer_handles = array( 'dimsop-gsap', 'dimsop-gsap-scrolltrigger', 'dimsop-main-js', 'dimsop-lenis', 'dimsop-motion' );
    if ( in_array( $handle, $defer_handles, true ) ) {
        return str_replace( ' src', ' defer src', $tag );
    }
    return $tag;
}
add_filter( 'script_loader_tag', 'dimsop_defer_scripts', 10, 3 );
