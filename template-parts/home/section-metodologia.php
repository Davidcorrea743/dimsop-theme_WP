<?php
/**
 * Sección: Metodología de Trabajo (5 fases interactivas)
 *
 * Desktop: imagen base con iconos superpuestos (hover para revelar).
 * Mobile: imagen estática prearmada.
 *
 * @package Dimsop
 */

defined( 'ABSPATH' ) || exit;
?>

<section id="metodologia" class="dimsop-section">
    <div class="container">
        <h2 class="dimsop-h2 text-center mb-2"><?php esc_html_e( 'Metodología de Trabajo', 'dimsop' ); ?></h2>
        <p class="text-center mb-5 mx-auto dimsop-metodologia-text" style="max-width: 780px;">
            <?php esc_html_e( 'Nuestro proceso garantiza soluciones efectivas, desde la identificación de necesidades hasta la entrega de valor tangible para tu negocio.', 'dimsop' ); ?>
        </p>

        <!-- Desktop: imagen base + iconos interactivos -->
        <div class="dimsop-metodologia-container d-none d-md-block">
            <img src="<?php echo esc_url( dimsop_asset_url( 'images/methodology/metdologia_nueva.webp' ) ); ?>"
                 alt="<?php esc_attr_e( 'Metodología de trabajo Dimsop', 'dimsop' ); ?>" class="dimsop-metodologia-base" />

            <div id="metod-icon-cliente" class="dimsop-metodologia-icon" data-area="cliente">
                <div class="dimsop-metodologia-icon-circle">
                    <img src="<?php echo esc_url( dimsop_asset_url( 'images/methodology/Icono_Necesidades_del_Cliente.webp' ) ); ?>" alt="<?php esc_attr_e( 'Necesidades del Cliente', 'dimsop' ); ?>" />
                </div>
                <span class="dimsop-metodologia-icon-label"><?php esc_html_e( 'Necesidades del Cliente', 'dimsop' ); ?></span>
            </div>

            <div id="metod-icon-base" class="dimsop-metodologia-icon" data-area="base">
                <div class="dimsop-metodologia-icon-circle">
                    <img src="<?php echo esc_url( dimsop_asset_url( 'images/methodology/Icono_Base_Instalada.webp' ) ); ?>" alt="<?php esc_attr_e( 'Base Instalada', 'dimsop' ); ?>" />
                </div>
                <span class="dimsop-metodologia-icon-label"><?php esc_html_e( 'Base Instalada', 'dimsop' ); ?></span>
            </div>

            <div id="metod-icon-valor" class="dimsop-metodologia-icon" data-area="valor">
                <div class="dimsop-metodologia-icon-circle">
                    <img src="<?php echo esc_url( dimsop_asset_url( 'images/methodology/Icono_Propuesta_de_Valor.webp' ) ); ?>" alt="<?php esc_attr_e( 'Propuesta de Valor', 'dimsop' ); ?>" />
                </div>
                <span class="dimsop-metodologia-icon-label"><?php esc_html_e( 'Propuesta de Valor', 'dimsop' ); ?></span>
            </div>

            <div id="metod-icon-equipos" class="dimsop-metodologia-icon" data-area="equipos">
                <div class="dimsop-metodologia-icon-circle">
                    <img src="<?php echo esc_url( dimsop_asset_url( 'images/methodology/Icono_Propuesta_Equipos.webp' ) ); ?>" alt="<?php esc_attr_e( 'Propuesta de Equipos y Servicios', 'dimsop' ); ?>" />
                </div>
                <span class="dimsop-metodologia-icon-label"><?php esc_html_e( 'Propuesta de Equipos y Servicios', 'dimsop' ); ?></span>
            </div>

            <div id="metod-icon-diseno" class="dimsop-metodologia-icon" data-area="diseno">
                <div class="dimsop-metodologia-icon-circle">
                    <img src="<?php echo esc_url( dimsop_asset_url( 'images/methodology/Icono_Disenio_de_Solucion.webp' ) ); ?>" alt="<?php esc_attr_e( 'Diseño de Solución', 'dimsop' ); ?>" />
                </div>
                <span class="dimsop-metodologia-icon-label"><?php esc_html_e( 'Diseño de Solución', 'dimsop' ); ?></span>
            </div>
        </div>

        <!-- Mobile: imagen estática con todos los iconos -->
        <div class="d-md-none">
            <img src="<?php echo esc_url( dimsop_asset_url( 'images/methodology/metodologia_crop.webp' ) ); ?>"
                 alt="<?php esc_attr_e( 'Metodología de trabajo Dimsop', 'dimsop' ); ?>" class="img-fluid" />
        </div>
    </div>
</section>
