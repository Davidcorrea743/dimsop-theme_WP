<?php
/**
 * Sección: ¿Quiénes Somos? (Misión, Visión y Valores)
 *
 * @package Dimsop
 */

defined( 'ABSPATH' ) || exit;
?>

<section id="nosotros" class="dimsop-section">
    <div class="container">
        <!-- Visión General -->
        <h2 class="dimsop-h2 text-center mb-5"><?php esc_html_e( '¿Quiénes Somos?', 'dimsop' ); ?></h2>
        <p class="dimsop-lead dimsop-lead-nosotros text-center mb-5">
            <?php esc_html_e( 'En Dimsop impulsamos la rentabilidad y la continuidad operativa de nuestros clientes, respaldados por personal altamente calificado e innovadores de la región.', 'dimsop' ); ?>
        </p>

        <!-- Misión y Visión -->
        <div class="row g-4 mb-5">
            <div class="col-lg-6">
                <div class="dimsop-card h-100">
                    <h3 class="dimsop-h3"><?php esc_html_e( 'Misión', 'dimsop' ); ?></h3>
                    <p>
                        <?php esc_html_e( 'Apoyar a nuestros clientes y aliados de negocios en las soluciones de innovación tecnológicas legadas y emergentes, para maximizar la rentabilidad de sus negocios en una relación de ganancia mutua, contando con personal altamente calificado e innovadores de la región.', 'dimsop' ); ?>
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="dimsop-card h-100">
                    <h3 class="dimsop-h3"><?php esc_html_e( 'Visión', 'dimsop' ); ?></h3>
                    <p>
                        <?php esc_html_e( 'Ser líder en el desarrollo de soluciones TIC´s basadas en las tecnologías de innovación emergentes como internet of things (IoT), la visión computarizada (digitalización), la realidad virtual, la inteligencia artificial y las criptotecnologías, entre otras.', 'dimsop' ); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Valores Corporativos -->
        <h3 class="dimsop-h3 text-center mb-2"><?php esc_html_e( 'Nuestros Valores', 'dimsop' ); ?></h3>
        <p class="text-center mb-4"><?php esc_html_e( 'Los principios que guían cada proyecto que entregamos.', 'dimsop' ); ?></p>
        <div class="row g-3 justify-content-center">
            <div class="col-6 col-md-4 col-lg">
                <div class="dimsop-value"><i class="fa fa-eye dimsop-value-icon" aria-hidden="true"></i><span><?php esc_html_e( 'Transparencia', 'dimsop' ); ?></span></div>
            </div>
            <div class="col-6 col-md-4 col-lg">
                <div class="dimsop-value"><i class="fa fa-users dimsop-value-icon" aria-hidden="true"></i><span><?php esc_html_e( 'Trabajos en equipos virtuales', 'dimsop' ); ?></span></div>
            </div>
            <div class="col-6 col-md-4 col-lg">
                <div class="dimsop-value"><i class="fa fa-rocket dimsop-value-icon" aria-hidden="true"></i><span><?php esc_html_e( 'Uso de tecnologías disruptivas', 'dimsop' ); ?></span></div>
            </div>
            <div class="col-6 col-md-4 col-lg">
                <div class="dimsop-value"><i class="fa fa-leaf dimsop-value-icon" aria-hidden="true"></i><span><?php esc_html_e( 'Innovación sostenible (verde)', 'dimsop' ); ?></span></div>
            </div>
            <div class="col-6 col-md-4 col-lg">
                <div class="dimsop-value"><i class="fa fa-line-chart dimsop-value-icon" aria-hidden="true"></i><span><?php esc_html_e( 'Rentabilidad', 'dimsop' ); ?></span></div>
            </div>
        </div>
    </div>
</section>
