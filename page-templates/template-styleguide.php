<?php
/**
 * Template Name: Guía de componentes
 *
 * Página interna para revisar los componentes UI del tema (botones, tarjetas,
 * insignias y campos) en modo claro y oscuro. No se indexa en buscadores.
 *
 * @package Dimsop
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'wp_robots', 'wp_robots_no_robots' );

get_header();

$dimsop_units = dimsop_get_units( 3 );
?>
<main id="main" class="dimsop-section py-5">
    <div class="container">
        <span class="dimsop-badge mb-3"><span class="dimsop-badge-dot"></span> <?php esc_html_e( 'Sistema UI', 'dimsop' ); ?></span>
        <h1 class="dimsop-h2 mb-2"><?php esc_html_e( 'Guía de componentes', 'dimsop' ); ?></h1>
        <p class="dimsop-lead mb-5"><?php esc_html_e( 'Prueba cada componente con el botón de modo claro/oscuro del header.', 'dimsop' ); ?></p>

        <h2 class="dimsop-h3 mb-3"><?php esc_html_e( 'Botones', 'dimsop' ); ?></h2>
        <div class="d-flex flex-wrap gap-3 align-items-center mb-5">
            <a href="#" class="dimsop-button"><?php esc_html_e( 'Primario', 'dimsop' ); ?> <i class="fa fa-arrow-right dimsop-button-arrow" aria-hidden="true"></i></a>
            <a href="#" class="dimsop-button dimsop-button--secondary"><?php esc_html_e( 'Secundario', 'dimsop' ); ?></a>
            <a href="#" class="dimsop-button dimsop-button--outline"><?php esc_html_e( 'Contorno', 'dimsop' ); ?></a>
            <a href="#" class="dimsop-button dimsop-button--ghost"><?php esc_html_e( 'Fantasma', 'dimsop' ); ?></a>
            <a href="#" class="dimsop-button dimsop-button--sm"><?php esc_html_e( 'Pequeño', 'dimsop' ); ?></a>
            <a href="#" class="dimsop-button dimsop-button--lg"><?php esc_html_e( 'Grande', 'dimsop' ); ?></a>
            <button type="button" class="dimsop-button" disabled><?php esc_html_e( 'Deshabilitado', 'dimsop' ); ?></button>
        </div>

        <h2 class="dimsop-h3 mb-3"><?php esc_html_e( 'Insignias', 'dimsop' ); ?></h2>
        <div class="d-flex flex-wrap gap-2 mb-5">
            <span class="dimsop-badge"><?php esc_html_e( 'Ciberseguridad', 'dimsop' ); ?></span>
            <span class="dimsop-badge dimsop-badge--primary"><?php esc_html_e( 'Cloud', 'dimsop' ); ?></span>
            <span class="dimsop-badge dimsop-badge--outline"><?php esc_html_e( 'IoT', 'dimsop' ); ?></span>
        </div>

        <h2 class="dimsop-h3 mb-3"><?php esc_html_e( 'Tarjetas (pasa el ratón por encima)', 'dimsop' ); ?></h2>
        <div class="row g-4 mb-5">
            <?php if ( $dimsop_units->have_posts() ) : ?>
                <?php
                $dimsop_i = 0;
                while ( $dimsop_units->have_posts() ) :
                    $dimsop_units->the_post();
                    $dimsop_icon = get_post_meta( get_the_ID(), '_dimsop_unit_icon', true ) ?: 'fa-cube';
                    ?>
                    <div class="col-md-4">
                        <article class="dimsop-ui-card<?php echo 2 === $dimsop_i ? ' dimsop-ui-card--featured' : ''; ?>">
                            <span class="dimsop-ui-card-icon"><i class="fa <?php echo esc_attr( $dimsop_icon ); ?>" aria-hidden="true"></i></span>
                            <h3 class="dimsop-ui-card-title"><?php the_title(); ?></h3>
                            <p class="dimsop-ui-card-text"><?php echo esc_html( get_the_excerpt() ); ?></p>
                        </article>
                    </div>
                    <?php
                    $dimsop_i++;
                endwhile;
                wp_reset_postdata();
                ?>
            <?php else : ?>
                <div class="col-12">
                    <p class="dimsop-field-hint"><?php esc_html_e( 'Aún no hay unidades: usa "Importar contenido inicial" en Apariencia › Opciones Dimsop.', 'dimsop' ); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <h2 class="dimsop-h3 mb-3"><?php esc_html_e( 'Campos de formulario', 'dimsop' ); ?></h2>
        <form class="row g-3 mb-5" onsubmit="return false;" style="max-width: 720px;">
            <div class="col-md-6 dimsop-field">
                <label class="dimsop-label" for="sg-name"><?php esc_html_e( 'Nombre', 'dimsop' ); ?></label>
                <input class="dimsop-input" id="sg-name" type="text" placeholder="<?php esc_attr_e( 'Tu nombre', 'dimsop' ); ?>">
                <span class="dimsop-field-hint"><?php esc_html_e( 'Texto de ayuda.', 'dimsop' ); ?></span>
            </div>
            <div class="col-md-6 dimsop-field">
                <label class="dimsop-label" for="sg-email"><?php esc_html_e( 'Correo', 'dimsop' ); ?></label>
                <input class="dimsop-input" id="sg-email" type="email" value="correo-invalido" aria-invalid="true">
                <span class="dimsop-field-error"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> <?php esc_html_e( 'Introduce un correo válido.', 'dimsop' ); ?></span>
            </div>
            <div class="col-md-6 dimsop-field">
                <label class="dimsop-label" for="sg-service"><?php esc_html_e( 'Servicio de interés', 'dimsop' ); ?></label>
                <select class="dimsop-input" id="sg-service">
                    <?php foreach ( dimsop_get_service_options() as $dimsop_value => $dimsop_label ) : ?>
                        <option value="<?php echo esc_attr( $dimsop_value ); ?>"><?php echo esc_html( $dimsop_label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 dimsop-field">
                <label class="dimsop-label" for="sg-msg"><?php esc_html_e( 'Mensaje', 'dimsop' ); ?></label>
                <textarea class="dimsop-input" id="sg-msg" placeholder="<?php esc_attr_e( 'Cuéntanos sobre tu proyecto', 'dimsop' ); ?>"></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="dimsop-button"><?php esc_html_e( 'Enviar', 'dimsop' ); ?> <i class="fa fa-paper-plane dimsop-button-arrow" aria-hidden="true"></i></button>
            </div>
        </form>
    </div>
</main>
<?php
get_footer();
