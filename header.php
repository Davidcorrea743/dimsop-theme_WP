<?php
/**
 * Cabecera estándar del Tema Dimsop
 *
 * @package Dimsop
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> data-theme="dark">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class( 'dimsop-body' ); ?>>
<?php wp_body_open(); ?>

<!-- Header corporativo Dimsop -->
<header id="dimsop_header" class="dimsop-header">
    <!-- Barra de progreso de lectura (Scroll progress bar) -->
    <div class="dimsop-scroll-progress" id="dimsop-scroll-progress"></div>

    <div class="dimsop-header-main container d-flex align-items-center justify-content-between py-2">
        <!-- Logo de marca -->
        <a class="dimsop-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo( 'name' ); ?>">
            <?php
            if ( has_custom_logo() ) {
                the_custom_logo();
            } else {
                ?>
                <img src="<?php echo esc_url( dimsop_asset_url( 'images/logos/Logo_completo_dimsop.webp' ) ); ?>"
                     alt="<?php bloginfo( 'name' ); ?>" class="img-fluid" width="220" height="55">
                <?php
            }
            ?>
        </a>

        <!-- Navegación desktop -->
        <nav class="dimsop-nav d-none d-lg-flex" aria-label="<?php esc_attr_e( 'Navegación principal', 'dimsop' ); ?>">
            <?php dimsop_primary_menu( 'nav' ); ?>
        </nav>

        <!-- Barra de herramientas (Search, DarkMode, CTA, Idioma) -->
        <div class="dimsop-tools d-flex align-items-center">
            <!-- Disparador del buscador modal -->
            <button class="dimsop-search-trigger me-1" type="button"
                    id="dimsop-search-open" aria-label="<?php esc_attr_e( 'Buscar', 'dimsop' ); ?>">
                <i class="fa fa-search" aria-hidden="true"></i>
            </button>

            <!-- Toggle de Dark Mode -->
            <button id="dimsop-dark-toggle" class="dimsop-theme-toggle ms-1"
                    type="button" title="<?php esc_attr_e( 'Cambiar modo claro/oscuro', 'dimsop' ); ?>"
                    aria-label="<?php esc_attr_e( 'Activar modo claro', 'dimsop' ); ?>"
                    data-label-light="<?php esc_attr_e( 'Activar modo claro', 'dimsop' ); ?>"
                    data-label-dark="<?php esc_attr_e( 'Activar modo oscuro', 'dimsop' ); ?>">
                <i class="fa fa-sun-o" aria-hidden="true"></i>
            </button>

            <!-- CTA: Trabaja con nosotros (Abre modal de carreras) -->
            <a href="#carreras" class="dimsop-btn-cta ms-1 d-none d-lg-inline-flex align-items-center"
               data-bs-toggle="modal" data-bs-target="#dimsop-carreras-modal">
                <i class="fa fa-users me-1" aria-hidden="true"></i> <?php esc_html_e( 'Trabaja con nosotros', 'dimsop' ); ?>
            </a>

            <!-- Selector de Idioma (ES / EN) preparado para Polylang -->
            <div class="dropdown dimsop-lang-dropdown ms-1">
                <?php
                $current_lang = 'es';
                if ( function_exists( 'pll_current_language' ) ) {
                    $current_lang = pll_current_language( 'slug' );
                }
                ?>
                <button class="dimsop-lang-btn" type="button" id="dimsop-lang-toggle"
                        data-bs-toggle="dropdown" aria-expanded="false" aria-label="<?php esc_attr_e( 'Seleccionar Idioma', 'dimsop' ); ?>">
                    <span class="dimsop-lang-code"><?php echo esc_html( strtoupper( $current_lang ) ); ?></span>
                    <i class="fa fa-caret-down" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dimsop-lang-toggle">
                    <?php
                    if ( function_exists( 'pll_the_languages' ) ) {
                        pll_the_languages( array( 'dropdown' => 0, 'show_flags' => 0, 'show_names' => 1, 'display_names_as' => 'slug' ) );
                    } else {
                        ?>
                        <li><a class="dropdown-item dimsop-lang-option <?php echo ( $current_lang === 'es' ) ? 'active' : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>">ES</a></li>
                        <li><a class="dropdown-item dimsop-lang-option <?php echo ( $current_lang === 'en' ) ? 'active' : ''; ?>" href="<?php echo esc_url( home_url( '/en/' ) ); ?>">EN</a></li>
                        <?php
                    }
                    ?>
                </ul>
            </div>

            <!-- Botón hamburguesa para móvil -->
            <button class="dimsop-burger d-lg-none ms-2" type="button"
                    id="dimsop-burger-toggle"
                    aria-expanded="false" aria-controls="dimsop-mobile-nav"
                    aria-label="<?php esc_attr_e( 'Abrir menú de navegación', 'dimsop' ); ?>">
                <span class="dimsop-burger-line"></span>
                <span class="dimsop-burger-line"></span>
                <span class="dimsop-burger-line"></span>
            </button>
        </div>
    </div>

    <!-- Panel de navegación móvil -->
    <div class="d-lg-none dimsop-mobile-panel" id="dimsop-mobile-nav" aria-hidden="true">
        <nav aria-label="<?php esc_attr_e( 'Navegación móvil', 'dimsop' ); ?>">
            <?php
            $dimsop_mobile_cta = sprintf(
                '<li class="nav-item mt-2"><a class="nav-link dimsop-btn-cta-mobile" href="#carreras" data-bs-toggle="modal" data-bs-target="#dimsop-carreras-modal"><i class="fa fa-users me-1" aria-hidden="true"></i> %s</a></li>',
                esc_html__( 'Trabaja con nosotros', 'dimsop' )
            );
            dimsop_primary_menu( 'nav flex-column', $dimsop_mobile_cta );
            ?>
        </nav>
    </div>

    <!-- Buscador overlay en pantalla completa -->
    <div class="dimsop-search-overlay" id="dimsop-search-overlay" role="dialog" aria-label="<?php esc_attr_e( 'Buscar', 'dimsop' ); ?>" aria-hidden="true">
        <div class="dimsop-search-overlay-backdrop" id="dimsop-search-close"></div>
        <div class="dimsop-search-overlay-content">
            <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search" class="dimsop-search-overlay-form">
                <i class="fa fa-search dimsop-search-overlay-icon" aria-hidden="true"></i>
                <input type="text" class="dimsop-search-overlay-input" name="s"
                       placeholder="<?php esc_attr_e( 'Buscar en Dimsop...', 'dimsop' ); ?>"
                       aria-label="<?php esc_attr_e( 'Buscar', 'dimsop' ); ?>" id="dimsop-search-input" autocomplete="off" />
                <button type="button" class="dimsop-search-overlay-close" id="dimsop-search-close-btn" aria-label="<?php esc_attr_e( 'Cerrar', 'dimsop' ); ?>">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </div>
</header>
