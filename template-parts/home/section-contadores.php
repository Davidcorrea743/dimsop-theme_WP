<?php
/**
 * Sección: Ecosistema Dimsop (contadores de impacto)
 *
 * Las cifras se imprimen ya con su valor final para que se vean sin JS;
 * el Día 7 dimsop-motion.js las anima desde 0 usando data-target.
 *
 * @package Dimsop
 */

defined( 'ABSPATH' ) || exit;

$dimsop_counters = array(
    array( 'value' => 150, 'icon' => 'fa-check-circle', 'label' => __( 'Proyectos implementados', 'dimsop' ) ),
    array( 'value' => 120, 'icon' => 'fa-handshake-o', 'label' => __( 'Clientes satisfechos', 'dimsop' ) ),
    array( 'value' => 9,   'icon' => 'fa-globe',        'label' => __( 'Países con presencia', 'dimsop' ) ),
);
?>

<section id="contadores" class="dimsop-section dimsop-section-dark dimsop-contadores">
    <div class="container">
        <div class="dimsop-section-head text-center mb-5">
            <span class="dimsop-badge mb-3"><span class="dimsop-badge-dot"></span> <?php esc_html_e( 'Impacto en cifras', 'dimsop' ); ?></span>
            <h2 class="dimsop-h2 dimsop-h2-light mb-0"><?php esc_html_e( 'Ecosistema Dimsop', 'dimsop' ); ?></h2>
        </div>

        <div class="row g-4 justify-content-center">
            <?php foreach ( $dimsop_counters as $dimsop_counter ) : ?>
                <div class="col-md-4">
                    <div class="dimsop-ui-card dimsop-counter-card text-center">
                        <span class="dimsop-ui-card-icon"><i class="fa <?php echo esc_attr( $dimsop_counter['icon'] ); ?>" aria-hidden="true"></i></span>
                        <span class="dimsop-counter-num" data-target="<?php echo esc_attr( $dimsop_counter['value'] ); ?>">+<?php echo esc_html( $dimsop_counter['value'] ); ?></span>
                        <p class="dimsop-counter-label mb-0"><?php echo esc_html( $dimsop_counter['label'] ); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
