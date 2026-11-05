<?php
/**
 * Sección: Banner Principal
 *
 * Escritorio (992 px o más): video de fondo con el texto encima.
 * Móvil y tablet: imagen corporativa completa, sin recortar, con el texto debajo.
 * El media de cada <source> evita descargar el recurso de la otra vista.
 *
 * @package Dimsop
 */

defined( 'ABSPATH' ) || exit;

$dimsop_banner_srcset = sprintf(
    '%1$s 800w, %2$s 1280w, %3$s 1920w',
    esc_url( dimsop_asset_url( 'images/corporativa_nueva-800.webp' ) ),
    esc_url( dimsop_asset_url( 'images/corporativa_nueva-1280.webp' ) ),
    esc_url( dimsop_asset_url( 'images/corporativa_nueva-1920.webp' ) )
);
?>

<section id="banner" class="dimsop-banner text-white">
    <picture class="dimsop-banner-picture">
        <source media="(min-width: 992px)" srcset="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==">
        <img class="dimsop-banner-image"
             src="<?php echo esc_url( dimsop_asset_url( 'images/corporativa_nueva-800.webp' ) ); ?>"
             srcset="<?php echo $dimsop_banner_srcset; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- URLs escapadas arriba. ?>"
             sizes="(max-width: 991.98px) 100vw, 1px"
             width="1920" height="908" fetchpriority="high"
             alt="<?php esc_attr_e( 'Dimsop Technology', 'dimsop' ); ?>" />
    </picture>
    <video class="dimsop-banner-video" autoplay muted loop playsinline
           preload="metadata"
           poster="<?php echo esc_url( dimsop_asset_url( 'video/video-hero-poster.jpg' ) ); ?>">
        <source media="(min-width: 992px)" src="<?php echo esc_url( dimsop_asset_url( 'video/video-hero.mp4' ) ); ?>" type="video/mp4" />
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
