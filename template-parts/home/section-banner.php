<?php
/**
 * Sección: Banner Principal (Video Hero)
 *
 * @package Dimsop
 */

defined( 'ABSPATH' ) || exit;
?>

<section id="banner" class="dimsop-banner d-flex align-items-center text-white">
    <video class="dimsop-banner-video" autoplay muted loop playsinline
           preload="metadata"
           poster="<?php echo esc_url( dimsop_asset_url( 'video/video-hero-poster.jpg' ) ); ?>">
        <source src="<?php echo esc_url( dimsop_asset_url( 'video/video-hero.mp4' ) ); ?>" type="video/mp4" />
    </video>
    <div class="dimsop-banner-overlay"></div>
    <div class="container text-center position-relative">
        <h1 class="dimsop-h1 mb-4">
            <?php esc_html_e( 'Soluciones TIC de Vanguardia para la continuidad de tu negocio.', 'dimsop' ); ?>
        </h1>
        <p class="dimsop-banner-lead mx-auto mb-4">
            <?php esc_html_e( 'Impulsamos la transformación digital con tecnología de vanguardia y soluciones legadas para la continuidad de tu negocio.', 'dimsop' ); ?>
        </p>
    </div>
</section>
