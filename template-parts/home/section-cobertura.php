<?php
/**
 * Sección: Nuestras Sedes (cobertura regional)
 *
 * Leyenda de sedes y cobertura, más el mapa estático de respaldo. El globo 3D
 * (Globe.gl) se monta sobre #dimsop-globe el Día 7 y reemplaza la imagen.
 *
 * @package Dimsop
 */

defined( 'ABSPATH' ) || exit;
?>

<section id="cobertura" class="dimsop-section dimsop-section-alt dimsop-cobertura">
    <div class="container">
        <div class="dimsop-section-head text-center mb-5">
            <span class="dimsop-badge mb-3"><span class="dimsop-badge-dot"></span> <?php esc_html_e( 'Presencia regional', 'dimsop' ); ?></span>
            <h2 class="dimsop-h2 mb-2"><?php esc_html_e( 'Nuestras Sedes', 'dimsop' ); ?></h2>
            <p class="dimsop-section-lead mx-auto">
                <?php esc_html_e( 'En Dimsop Technology expandimos constantemente nuestra presencia para conectar a los negocios con soluciones TIC de vanguardia, garantizando continuidad operativa y rentabilidad donde sea que se encuentren.', 'dimsop' ); ?>
            </p>
        </div>

        <div class="row g-4 align-items-center">
            <div class="col-lg-5">
                <p class="dimsop-cobertura-text mb-4"><?php esc_html_e( 'En el mapa de nuestras sedes puedes ver nuestro alcance:', 'dimsop' ); ?></p>

                <article class="dimsop-ui-card dimsop-cobertura-item mb-3">
                    <span class="dimsop-cobertura-pin dimsop-cobertura-pin--blue" aria-hidden="true"></span>
                    <div>
                        <h3 class="dimsop-cobertura-title"><?php esc_html_e( 'Sedes físicas', 'dimsop' ); ?></h3>
                        <p class="dimsop-ui-card-text">
                            <?php
                            printf(
                                /* translators: 1: sede principal, 2: sede de operaciones */
                                esc_html__( 'Nuestra sede principal está en %1$s y trabaja en sinergia con nuestra sede de operaciones en %2$s.', 'dimsop' ),
                                '<strong class="dimsop-cobertura-strong-blue">' . esc_html__( 'Caracas, Venezuela', 'dimsop' ) . '</strong>',
                                '<strong class="dimsop-cobertura-strong-blue">' . esc_html__( 'Miami, Estados Unidos', 'dimsop' ) . '</strong>'
                            );
                            ?>
                        </p>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <span class="dimsop-badge dimsop-badge--primary"><?php esc_html_e( 'Caracas', 'dimsop' ); ?></span>
                            <span class="dimsop-badge dimsop-badge--primary"><?php esc_html_e( 'Miami', 'dimsop' ); ?></span>
                        </div>
                    </div>
                </article>

                <article class="dimsop-ui-card dimsop-cobertura-item">
                    <span class="dimsop-cobertura-pin dimsop-cobertura-pin--green" aria-hidden="true"></span>
                    <div>
                        <h3 class="dimsop-cobertura-title"><?php esc_html_e( 'Cobertura global', 'dimsop' ); ?></h3>
                        <p class="dimsop-ui-card-text">
                            <?php
                            printf(
                                /* translators: %s: región de cobertura */
                                esc_html__( 'Ejecutamos proyectos integrales y despliegues a lo largo de %s.', 'dimsop' ),
                                '<strong class="dimsop-cobertura-strong-green">' . esc_html__( 'Latinoamérica y España', 'dimsop' ) . '</strong>'
                            );
                            ?>
                        </p>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <span class="dimsop-badge"><?php esc_html_e( 'Latinoamérica', 'dimsop' ); ?></span>
                            <span class="dimsop-badge"><?php esc_html_e( 'España', 'dimsop' ); ?></span>
                        </div>
                    </div>
                </article>
            </div>

            <div class="col-lg-7">
                <div id="dimsop-globe-wrap" class="dimsop-globe-wrap">
                    <img id="dimsop-globe-fallback" class="dimsop-globe-fallback"
                         src="<?php echo esc_url( dimsop_asset_url( 'images/banners/MAPA_clean.webp' ) ); ?>"
                         alt="<?php esc_attr_e( 'Mapa de cobertura Dimsop', 'dimsop' ); ?>" loading="lazy" decoding="async" />
                    <div id="dimsop-globe" class="dimsop-globe-container" aria-hidden="true"></div>
                </div>
            </div>
        </div>

        <p class="dimsop-section-lead mx-auto text-center mt-5 mb-0">
            <?php esc_html_e( 'Gracias a nuestro ecosistema global de aliados y a un equipo altamente calificado, llevamos innovación en nube, ciberseguridad, inteligencia artificial y redes al corazón de las empresas, sin importar sus fronteras geográficas.', 'dimsop' ); ?>
        </p>
    </div>
</section>
