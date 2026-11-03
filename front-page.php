<?php
/**
 * Plantilla de Página de Inicio (Front Page)
 *
 * Arquitectura modular: carga ordenada de cada una de las 11 secciones
 *
 * @package Dimsop
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main dimsop-homepage">
    <?php
    // Sección 2: Banner Principal (Video Hero)
    get_template_part( 'template-parts/home/section', 'banner' );

    // Sección 3: Cintillo de Calidad (ISO 9001)
    get_template_part( 'template-parts/home/section', 'iso' );

    // Sección 4: ¿Quiénes Somos? (Misión, Visión y Valores)
    get_template_part( 'template-parts/home/section', 'nosotros' );

    // Sección 5: Metodología de Trabajo (5 fases)
    get_template_part( 'template-parts/home/section', 'metodologia' );

    // Sección 6: Unidades de Negocio (8 unidades TIC)
    get_template_part( 'template-parts/home/section', 'unidades' );

    // Sección 7: Cobertura Regional
    get_template_part( 'template-parts/home/section', 'cobertura' );

    // Sección 8: Nuestros Aliados (Marquee infinito)
    get_template_part( 'template-parts/home/section', 'aliados' );

    // Sección 9: Impacto en Cifras (Contadores animados)
    get_template_part( 'template-parts/home/section', 'contadores' );

    // Sección 10: Proyectos Referenciales (Grid + Modales)
    get_template_part( 'template-parts/home/section', 'proyectos' );

    // Sección 11: Blog / Actualidad (Últimos artículos)
    get_template_part( 'template-parts/home/section', 'blog' );

    // Sección 12: Contacto / Conversemos
    get_template_part( 'template-parts/home/section', 'contacto' );
    ?>
</main>

<?php
get_footer();
