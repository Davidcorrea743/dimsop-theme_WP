# Control de avance: migración Dimsop (Odoo → WordPress)

**Día actual:** 5 de 20 (Semana 1) · Día 4 cerrado el 2026-11-03
**Sitio local:** http://dimsop-theme.localhost/ · Mailpit: http://localhost:10000
**Plan completo:** `C:\Users\jdcor\.claude\plans\te-explico-un-poco-graceful-clock.md`

Estados: ✅ hecho · 🟡 en curso · ⬜ pendiente

## Calendario

| Día | Tema | Estado | Qué se hizo |
|---|---|---|---|
| 1 | Puesta a punto y navegación | ✅ | Walker de menú (`inc/navigation.php`), header que se oculta/reaparece, barra de progreso, enlace activo, desplazamiento suave, menú hamburguesa y buscador overlay. SMTP a Mailpit (puerto 10001). |
| 2 | Opciones y contenido | ✅ | Apariencia › Opciones Dimsop, importador (7 unidades, 25 aliados, 19 proyectos), imágenes a WebP (17,4 → 0,7 MB), favicon, tokens y componentes UI estilo shadcn (`_ui.scss`), Guía de componentes. |
| 3 | Unidades y cobertura | ✅ | Corrección del botón de tema. Unidades en cuadrícula bento (`section-unidades.php`, `_unidades.scss`) con tarjetas spotlight e insignia pulsante. "Nuestras Sedes" (`section-cobertura.php`, `_cobertura.scss`) con leyenda en tarjetas, puntos pulsantes y mapa WebP de respaldo. |
| 4 | Aliados, contadores y proyectos | ✅ | Ajustes de Nosotros (títulos centrados, valores en tarjetas). Aliados: carrusel en bucle de 25 logos en chips blancos. Contadores en tarjetas "cristal" con valor final visible sin JS. Proyectos: carrusel de 19 tarjetas con logo y nombre de la empresa. Controles en ambos carruseles. Nuevo `_proyectos.scss`. |
| 5 | Blog, contacto y revisión responsive | ⬜ | |
| 6 | SEO, respaldo y correcciones de base | ⬜ | |
| 7 | Animaciones (GSAP, Lenis), carruseles, contadores, globo 3D | ⬜ | |
| 8 | Formulario de contacto (AJAX, SMTP, CRM) | ⬜ | |
| 9 | Postulación con CV | ⬜ | |
| 10 | Páginas interiores (page, 404, búsqueda, privacidad) | ⬜ | |
| 11 | Idiomas, parte 1 (Polylang, .pot/.po) | ⬜ | |
| 12 | Idiomas, parte 2 (contenido, hreflang) | ⬜ | |
| 13 | Blog (listado, entrada, compartir en LinkedIn) | ⬜ | |
| 14 | QA visual y navegadores | ⬜ | |
| 15 | Rendimiento, SEO y accesibilidad | ⬜ | |
| 16 | Staging | ⬜ | |
| 17 | Validación y seguridad | ⬜ | |
| 18 | Correcciones y contenido final | ⬜ | |
| 19 | Preparación del lanzamiento | ⬜ | |
| 20 | Publicación | ⬜ | |

## Registro de fallos corregidos

Cada día empieza corrigiendo los fallos reportados; cada corrección se anota aquí.

| Fecha | Día | Problema | Causa | Solución |
|---|---|---|---|---|
| 2026-11-03 | 1 | Los correos no llegaban a Mailpit | WP Mail SMTP en el puerto 1025 | Puerto 10001 (lo cambió David) |
| 2026-11-03 | 2 | Botón "Secundario" invisible en modo oscuro | `--dimsop-primary` coincide con el fondo oscuro | Azul corporativo #004587 en oscuro (`_ui.scss`) |
| 2026-11-03 | 3 | El sol del botón de tema no se veía en modo claro | Usaba `btn-outline-light` (blanco sobre header blanco) | Clase propia `dimsop-theme-toggle`: azul en claro, verde en oscuro, anillo de foco; `aria-label` cambia según el modo |
| 2026-11-03 | 4 | Logos de aliados invisibles en modo oscuro | Logos oscuros con filtro gris sobre fondo azul | Chips blancos en ambos modos (como en Odoo) |
| 2026-11-03 | 4 | Contadores de Odoo mostraban "+0" sin JS | El valor solo lo escribía el JS | Se imprime el valor final; el Día 7 se anima desde 0 |
| 2026-11-03 | 5 | Los carruseles no se podían mover a mano | Solo tenían la animación CSS | `initMarquees()`: botones, arrastre con ratón o dedo, inercia y rueda horizontal en Aliados y Proyectos (adelantado del Día 7) |
| 2026-11-03 | 5 | Proyectos mostraba la descripción de cada trabajo | Decisión de diseño revisada por David | Solo logo y nombre centrado; los extractos siguen guardados en el panel pero no se muestran |
| 2026-11-04 | 5 | Tarjetas de Unidades y Sedes alineadas a la izquierda, a diferencia del resto del sitio | Diseño inicial del Día 3 | Icono y texto centrados en todas las vistas; en Sedes el punto de color pasa arriba del título |
| 2026-11-03 | 5 | Cambio de favicon pedido por David | Usaba `logo_Dimsop_D_Degradado` | `favicon-32/180/512.png` generados desde `logo_header.png`, sin márgenes y con fondo blanco |

## Decisiones tomadas

- Se construye sobre el tema existente, sin empezar de cero.
- Estilo visual: shadcn sin React (PHP + Bootstrap 5 + SCSS); animación "notable pero elegante" con GSAP y Lenis (Día 7).
- Las tarjetas con spotlight y la insignia con punto pulsante se usan en producción (unidades, proyectos, blog, valores).
- La Guía de componentes (`/guia-componentes/`) es solo de referencia: se borra o pasa a borrador el Día 19.
- Los commits los hace David; Claude solo entrega la descripción.
- Valores corporativos: tarjetas con icono y frase breve (las frases son propuesta, validar con el cliente).

## Pendientes y decisiones abiertas

- [ ] Zona horaria (hoy UTC) e idioma de WordPress (hoy inglés): Ajustes › Generales.
- [ ] Desactivar WPForms Lite (no se usa).
- [ ] Borrar los PNG/JPG originales de `assets/images` (ya existen los WebP).
- [ ] CRM: ¿Odoo por XML-RPC/webhook o solo leads en WordPress? (Día 8)
- [ ] Hosting de staging y producción, y acceso al DNS (Días 16, 19, 20).
- [ ] Página Catálogo y texto legal de la política de privacidad (los entrega el cliente).

## Referencia rápida

Compilar el SCSS (desde la carpeta `wordpress`):

```bash
ELECTRON_RUN_AS_NODE=1 "$LOCALAPPDATA/Programs/Local/Local.exe" node_modules/sass/sass.js --style=expanded --no-source-map dimsop-theme/assets/scss/dimsop-theme.scss dimsop-theme/assets/css/dimsop-theme.css
```
