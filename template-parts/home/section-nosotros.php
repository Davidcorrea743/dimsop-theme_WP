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
                    <h3 class="dimsop-h3 text-center"><?php esc_html_e( 'Misión', 'dimsop' ); ?></h3>
                    <p>
                        <?php esc_html_e( 'Apoyar a nuestros clientes y aliados de negocios en las soluciones de innovación tecnológicas legadas y emergentes, para maximizar la rentabilidad de sus negocios en una relación de ganancia mutua, contando con personal altamente calificado e innovadores de la región.', 'dimsop' ); ?>
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="dimsop-card h-100">
                    <h3 class="dimsop-h3 text-center"><?php esc_html_e( 'Visión', 'dimsop' ); ?></h3>
                    <p>
                        <?php esc_html_e( 'Ser líder en el desarrollo de soluciones TIC´s basadas en las tecnologías de innovación emergentes como internet of things (IoT), la visión computarizada (digitalización), la realidad virtual, la inteligencia artificial y las criptotecnologías, entre otras.', 'dimsop' ); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Valores Corporativos -->
        <h3 class="dimsop-h3 text-center mb-2"><?php esc_html_e( 'Nuestros Valores', 'dimsop' ); ?></h3>
        <p class="text-center mb-4"><?php esc_html_e( 'Los principios que guían cada proyecto que entregamos.', 'dimsop' ); ?></p>
        <?php
        // Icono => [valor, frase breve]. Las frases son propuesta: validarlas con el cliente.
        $dimsop_values = array(
            'fa-eye'        => array( __( 'Transparencia', 'dimsop' ), __( 'Comunicación clara y honesta en cada etapa del proyecto.', 'dimsop' ) ),
            'fa-users'      => array( __( 'Trabajo en equipos virtuales', 'dimsop' ), __( 'Talento conectado entre países, sin fronteras.', 'dimsop' ) ),
            'fa-rocket'     => array( __( 'Tecnologías disruptivas', 'dimsop' ), __( 'Adoptamos lo emergente para adelantarnos al cambio.', 'dimsop' ) ),
            'fa-leaf'       => array( __( 'Innovación sostenible', 'dimsop' ), __( 'Soluciones eficientes que cuidan el entorno.', 'dimsop' ) ),
            'fa-line-chart' => array( __( 'Rentabilidad', 'dimsop' ), __( 'Crecimiento con ganancia mutua para clientes y aliados.', 'dimsop' ) ),
        );
        ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-3 justify-content-center">
            <?php foreach ( $dimsop_values as $dimsop_icon => $dimsop_value ) : ?>
                <div class="col">
                    <article class="dimsop-ui-card dimsop-value-card">
                        <span class="dimsop-ui-card-icon"><i class="fa <?php echo esc_attr( $dimsop_icon ); ?>" aria-hidden="true"></i></span>
                        <h4 class="dimsop-ui-card-title"><?php echo esc_html( $dimsop_value[0] ); ?></h4>
                        <p class="dimsop-ui-card-text"><?php echo esc_html( $dimsop_value[1] ); ?></p>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
