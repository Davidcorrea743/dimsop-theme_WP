<?php
/**
 * Cabecera de las páginas interiores (404, Catálogo, páginas, búsqueda)
 *
 * Fondo de ondas y circuitos (recorte sin texto de "elementos webinar (3).png").
 *
 * Argumentos (get_template_part( 'template-parts/page-hero', null, $args )):
 * - badge   (string) Texto de la insignia con punto pulsante. Opcional.
 * - title   (string) Título (h1).
 * - kicker  (string) Texto grande decorativo sobre el título, p. ej. "404". Opcional.
 * - lead    (string) Párrafo bajo el título. Opcional.
 * - actions (string) HTML ya escapado con botones o buscador. Opcional.
 * - compact (bool)   Versión baja para páginas de texto.
 *
 * @package Dimsop
 */

defined( 'ABSPATH' ) || exit;

$dimsop_hero = wp_parse_args( $args ?? array(), array(
    'badge'   => '',
    'title'   => '',
    'kicker'  => '',
    'lead'    => '',
    'actions' => '',
    'compact' => false,
) );
?>

<section class="dimsop-page-hero<?php echo $dimsop_hero['compact'] ? ' dimsop-page-hero--compact' : ''; ?>">
    <picture class="dimsop-page-hero-bg" aria-hidden="true">
        <source media="(max-width: 767.98px)" srcset="<?php echo esc_url( dimsop_asset_url( 'images/backgrounds/dimsop-waves-960.webp' ) ); ?>">
        <img src="<?php echo esc_url( dimsop_asset_url( 'images/backgrounds/dimsop-waves-1920.webp' ) ); ?>" alt="" width="1920" height="520" fetchpriority="high">
    </picture>
    <div class="container position-relative text-center">
        <?php if ( $dimsop_hero['badge'] ) : ?>
            <span class="dimsop-badge dimsop-page-hero-badge mb-3"><span class="dimsop-badge-dot"></span> <?php echo esc_html( $dimsop_hero['badge'] ); ?></span>
        <?php endif; ?>
        <?php if ( $dimsop_hero['kicker'] ) : ?>
            <p class="dimsop-page-hero-kicker" aria-hidden="true"><?php echo esc_html( $dimsop_hero['kicker'] ); ?></p>
        <?php endif; ?>
        <h1 class="dimsop-page-hero-title"><?php echo esc_html( $dimsop_hero['title'] ); ?></h1>
        <?php if ( $dimsop_hero['lead'] ) : ?>
            <p class="dimsop-page-hero-lead mx-auto"><?php echo esc_html( $dimsop_hero['lead'] ); ?></p>
        <?php endif; ?>
        <?php if ( $dimsop_hero['actions'] ) : ?>
            <div class="dimsop-page-hero-actions">
                <?php echo $dimsop_hero['actions']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Lo escapa quien lo construye. ?>
            </div>
        <?php endif; ?>
    </div>
</section>
