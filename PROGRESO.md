# Control de avance: migración Dimsop (Odoo → WordPress)

**Día actual:** 11 de 20 (Semana 3) · Día 10 cerrado el 2026-11-09
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
| 5 | Blog, contacto y revisión responsive | ✅ | Blog: 3 últimas entradas en tarjetas (`section-blog.php`, `_blog.scss`). Contacto: formulario conectado al handler por AJAX, con avisos de éxito y error (`section-contacto.php`, `_contacto.scss`). Misión y Visión con tarjetas UI e insignias en Nosotros y Metodología. Revisado a 1400, 991, 767 y 375 px. |
| 6 | SEO, respaldo y correcciones de base | ✅ | Yoast SEO y UpdraftPlus instalados (sin programar respaldos aún). Prospectos solo para administradores y editores. IP del límite de envíos sin fiarse de cabeceras (`DIMSOP_TRUSTED_PROXIES`). Versión del tema leída de `style.css`. Modo oscuro por defecto simplificado. Comentarios de Odoo eliminados. WordPress en español, zona horaria de Caracas, nombre del sitio "Dimsop" y archivos de autor desactivados. |
| 7 | Animaciones (GSAP, Lenis), carruseles, contadores, globo 3D | ✅ | `dimsop-motion.js` + `_motion.scss`: Lenis sincronizado con ScrollTrigger; banner con título palabra por palabra, parallax del video y cuadrícula de puntos; títulos de sección palabra por palabra; tarjetas con desvanecido, subida y desenfoque escalonados; contadores en rodillos; botones magnéticos (solo ratón); carruseles que frenan al pasar el ratón. Globo 3D (Globe.gl 2.42.0) con carga diferida, pausa fuera de pantalla y datos propios en `assets/data` y `assets/images/globe`. Todo estático con movimiento reducido. |
| 8 | Formulario de contacto (AJAX, SMTP, CRM) | ✅ | Campo "Otro" que aparece solo con ese servicio y es obligatorio entonces (también en el servidor); se guarda en el prospecto, sale en el panel y en el correo ("Otro: motivo"). Validación en el navegador con mensajes bajo cada campo, `aria-invalid`/`aria-describedby` y foco en el primer error. Avisos del servidor en texto plano. El envío AJAX y los avisos ya venían del Día 5. CRM: los prospectos se quedan solo en WordPress. El SMTP corporativo queda para staging (abajo). |
| 9 | Postulación con CV | ✅ | Ventana "Forma parte de nuestro equipo" (`template-parts/modals/modal-careers.php`, `_careers.scss`) que abren el header, el menú móvil y el footer. `inc/forms/careers-handler.php`: CV obligatorio, solo PDF/DOC/DOCX de hasta 5 MB comprobando extensión y contenido real; guardado con nombre aleatorio en una carpeta privada fuera de la web pública; aviso a talento@ con el CV adjunto; límite de 3 envíos en 10 minutos. Panel: estado Recibida / En revisión / Contratada. Al borrar una postulación se borra su CV. La validación y el envío AJAX ahora son un componente común (`bindAjaxForm`) para contacto y postulación. |
| 10 | Páginas interiores (page, 404, búsqueda, privacidad) | ✅ | Cabecera común con fondo de ondas (recorte sin texto de "elementos webinar (3)", WebP de 61 KB). `404.php` (404 grande, inicio, contacto, buscador y accesos), `page.php` (texto legible), `search.php` (tarjetas, paginación, estado vacío; solo entradas y páginas), `searchform.php`. Catálogo provisional (`template-catalogo.php`): "Próximamente", botón de contacto y 6 tarjetas skeleton, con noindex y fuera del sitemap. Política de privacidad creada desde el texto de Odoo, ampliada con la postulación y el almacenamiento local. Las páginas se crean con el botón de importar de Opciones Dimsop. |
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
| 2026-11-05 | 5 | Editores y autores podían ver las Postulaciones | El tipo de contenido usaba los permisos normales de entradas | Solo administradores (`manage_options`), también por URL directa |
| 2026-11-05 | 5 | Aliados y Proyectos sin logo mostraban un recuadro vacío | La plantilla imprimía la caja aunque no hubiera imagen | Aliado: chip con el nombre. Proyecto: tarjeta solo con el nombre |
| 2026-11-05 | 5 | El video del banner se veía recortado en móvil | Con `cover` en vertical solo se ve una franja del video | Bajo 992 px, imagen `corporativa_nueva` completa con el texto debajo; el video queda solo en escritorio y no se descarga en móvil |
| 2026-11-06 | 6 | El límite de envíos se podía saltar cambiando la cabecera X-Forwarded-For | Se confiaba en cabeceras que escribe el visitante | Se usa REMOTE_ADDR; la cabecera solo se lee desde proxies declarados en `DIMSOP_TRUSTED_PROXIES` |
| 2026-11-06 | 6 | Autores y colaboradores podían ver los Prospectos | Usaba los permisos normales de entradas | Solo administradores y editores (`edit_others_posts`), también por URL directa |
| 2026-11-06 | 6 | Yoast publicaba los autores en el sitemap, y el tema oculta los usuarios | Yoast activa los archivos de autor por defecto | Archivos de autor desactivados en Yoast |
| 2026-11-07 | 7 | El globo 3D no se dibujaba | Globe.gl 2.33.1 no tiene `htmlElementVisibilityModifier`, que usaba el código de Odoo | Versión 2.42.0 y la llamada pasa a ser opcional; si el globo falla queda el mapa y un aviso en consola |
| 2026-11-09 | 8 | Las animaciones solo se veían la primera vez | Cada revelado usaba `once: true` | `onEachEntry()`: se reproducen cada vez que el bloque entra, bajando o subiendo, y se rearman solo cuando salió del todo de la pantalla. El banner sigue siendo una entrada única |
| 2026-11-09 | 8 | Los avisos del formulario podían mostrar `&quot;` en vez de comillas | El servidor escapaba el texto como HTML y el JS lo inserta como texto | Las respuestas AJAX van en texto plano |
| 2026-11-09 | 8 | El nombre aceptaba números y símbolos | Solo se comprobaba que no estuviera vacío | Solo letras (con tildes, ñ, ü) y espacios, en el navegador y en el servidor (`nombre_invalido`) |
| 2026-11-09 | 9 | La ruta del CV se guardaba rota en Windows | WordPress quita las barras invertidas al guardar metadatos | Se guarda la ruta relativa a la carpeta privada (también sirve al cambiar de servidor) |
| 2026-11-09 | 9 | El aviso de postulación enviada nunca se veía en Odoo | Error del sitio original | Aviso dentro de la ventana, por AJAX; sin JS la ventana se reabre con el aviso |
| 2026-11-09 | 10 | Los títulos de Yoast del 404 y la búsqueda salían en inglés | Yoast se instaló antes de pasar WordPress al español | Traducidos en Yoast (en la base de datos; viaja a staging con ella) |
| 2026-11-09 | 11 | Los botones del Catálogo y del 404 se cruzaban al pasar el ratón | El efecto magnético movía cada botón hasta el 30 % de su ancho (unos 35 px) y la separación era de 8 px | Movimiento con tope de 6 px en horizontal y 4 px en vertical (también en el banner) y separación de 16 px |
| 2026-11-09 | 11 | El footer no convencía: copia del de Odoo | Mapas de Google claros sobre fondo oscuro (≈1 MB y cookies cada uno), fondo igual al de la página en modo oscuro, teléfono repetido, 1,6 pantallas en móvil, sin llamada final | Rediseño: columnas Marca (lema, ISO "rumbo a", correo) / Navegación / Soluciones (7 unidades) / Sedes en tarjetas con "Cómo llegar" y mapa solo al pulsar "Ver mapa"; barra legal con RIF, privacidad, redes y "Volver arriba"; azul corporativo fijo con línea en degradado; mapas editables en Opciones Dimsop; logo recortado (8 KB) |
| 2026-11-09 | 11 | Al recalcular ScrollTrigger (abrir el mapa, cambiar el tamaño de la ventana, girar el móvil) con la página desplazada, los bloques animados desaparecían: el footer quedaba como una página azul vacía | `html` tiene `scroll-behavior: smooth` y la regla que lo anulaba con Lenis apuntaba a `.lenis.lenis-smooth`, pero Lenis 1.1 solo añade `.lenis`; el salto al inicio que hace ScrollTrigger para medir dejaba de ser instantáneo y medía todo mal | `html.lenis { scroll-behavior: auto !important }` (`_motion.scss`). Además `onEachEntry()` ignora los callbacks durante el recálculo y sincroniza cada bloque al terminar |
| 2026-11-09 | 11 | "Cómo llegar" abría Google Maps sin destino o en vista desde el espacio | El enlace copiado de Odoo no llevaba el identificador del lugar; luego la ruta sin ubicación del visitante abría el mundo entero | "Cómo llegar" abre la ficha de Google Maps que indicó David (editable en Opciones Dimsop); "Ver mapa" usa las coordenadas de cada sede con marcador (`dimsop_map_links()`, sin clave de API) |
| 2026-11-09 | 11 | La franja "¿Tienes un proyecto en mente?" rompía la armonía | Decisión de diseño revisada por David | Eliminada del footer |
| 2026-11-09 | 11 | A 1024 px el botón de WhatsApp tapaba "Volver arriba" y "Estados Unidos · Miami" se partía en tres líneas | La barra legal no reservaba sitio al botón flotante; la ciudad iba en la misma línea | Margen derecho de 80 px en la barra legal desde 768 px; la ciudad va siempre en su propia línea bajo el país |
| 2026-11-09 | 11 | En móvil, abrir el menú desde media página congelaba el sitio (había que cerrar el navegador) | Tres causas juntas: el panel formaba parte del header y lo hacía crecer, así que el navegador desplazaba la página; `body { overflow-x: hidden }` convertía el body en contenedor de scroll cuando Lenis ponía `overflow: hidden` en `html`, y el header sticky quedaba fuera de pantalla con el scroll bloqueado y sin forma de cerrar el menú | Panel flotante bajo el header (`position: absolute`, scroll propio y fondo opaco); `overflow-x: clip` en el body; al abrir el menú el header siempre queda visible |
| 2026-11-09 | 11 | Los enlaces del menú móvil no llevaban a su sección | Al cerrar el menú, Lenis se reactivaba un instante después y su `start()` cancelaba el desplazamiento recién pedido | `setOpen(false)` reactiva Lenis en el acto (salvo con un modal abierto) y el desplazamiento a anclas usa `force: true` |
| 2026-11-04 | 5 | Al enviar el formulario la página recargaba y bajaba desde el inicio | Envío por POST con redirección | `initContactForm()` envía por AJAX (adelantado del Día 8); aviso junto al botón que desaparece al minuto |
| 2026-11-04 | 5 | Correo, teléfono y WhatsApp repetidos en Contacto | Ya están en el footer y el botón flotante | Se elimina la franja; queda solo el formulario, con los opcionales marcados |
| 2026-11-04 | 5 | Los errores del formulario no mostraban aviso | `?error=` es una variable reservada de WordPress | El handler redirige con `?form_error=` |
| 2026-11-04 | 5 | El enlace Blog del menú y del footer daba 404 | `/blog` no existe hasta el Día 13 | Apunta a `/#blog`; volver a cambiarlo el Día 13 |
| 2026-11-04 | 5 | Tarjetas de Unidades y Sedes alineadas a la izquierda, a diferencia del resto del sitio | Diseño inicial del Día 3 | Icono y texto centrados en todas las vistas; en Sedes el punto de color va junto a cada título, como leyenda del mapa |
| 2026-11-03 | 5 | Cambio de favicon pedido por David | Usaba `logo_Dimsop_D_Degradado` | `favicon-32/180/512.png` generados desde `logo_header.png`, sin márgenes y con fondo blanco |

## Decisiones tomadas

- Se construye sobre el tema existente, sin empezar de cero.
- Estilo visual: shadcn sin React (PHP + Bootstrap 5 + SCSS); animación "notable pero elegante" con GSAP y Lenis (Día 7).
- Las tarjetas con spotlight y la insignia con punto pulsante se usan en producción (unidades, proyectos, blog, valores).
- La Guía de componentes (`/guia-componentes/`) es solo de referencia: se borra o pasa a borrador el Día 19.
- Los commits los hace David; Claude solo entrega la descripción.
- Banner: video desde 992 px; por debajo, imagen completa con el texto debajo (el corte es el mismo del menú hamburguesa).
- Blog en portada: cuadrícula con las 3 últimas entradas, se actualiza sola al publicar (en Odoo el carrusel también mostraba solo 3).
- Valores corporativos: tarjetas con icono y frase breve (las frases son propuesta, validar con el cliente).
- Prospectos: solo administradores y editores. Postulaciones: solo administradores.
- CRM: los prospectos se quedan solo en WordPress (Prospectos CRM) y llegan por correo a mercadeo; no se integran con Odoo (decisión de David, 2026-11-09).
- Seguridad del formulario (revisada el 2026-11-09 con ataques de prueba): sin SQL escrito a mano (todo pasa por las funciones de WordPress, que usan consultas preparadas); los datos se limpian al entrar y se escapan al mostrarse en el panel y el correo; el JS inserta los avisos como texto. Además: nonce, honeypot y límite de envíos.
- Nombre y apellido: solo letras y espacios (decisión de David). Rechaza también guiones y apóstrofos (Ana-María, O'Brien).
- Postulaciones: el CV es obligatorio (en Odoo era opcional). El aviso a talento@ lleva el CV adjunto, porque las postulaciones solo las ven administradores en el panel.
- CV guardados en `app/dimsop-private/cv/` (junto a la carpeta pública, no dentro). Se puede cambiar con `define( 'DIMSOP_CV_DIR', '/ruta/privada' );` en `wp-config.php`.
- 404 y cabeceras de páginas: se usa solo el fondo de "elementos webinar (3)"; su texto ("El servicio no estará disponible temporalmente") no corresponde a un 404 y no se podría traducir (decisión de David).
- Modo oscuro por defecto, igual que el sitio original; el modo claro solo si el visitante lo elige.
- Respaldos con UpdraftPlus (decisión de Claude, gratuito y sin coste de servicio; se puede cambiar antes del Día 19).
- Animaciones de sección: se repiten al volver a entrar (contadores, títulos, tarjetas, ISO, globo); las tarjetas entran desde abajo al bajar y desde arriba al subir. El banner solo se anima al cargar.
- Animación: con "reducir movimiento" no hay Lenis, revelados ni giro del globo. En pantallas táctiles no hay botones magnéticos ni desenfoque, y el dedo desplaza la página en vez de girar el globo.
- Globo: datos de países y textura servidos desde el tema (no desde GitHub, como en Odoo). Globe.gl se descarga de jsDelivr solo al acercarse a Sedes (~1,7 MB).

## Pendientes y decisiones abiertas

- [ ] **Antes de staging (Día 16):** si el hosting va detrás de Cloudflare o un balanceador, definir `DIMSOP_TRUSTED_PROXIES` en `wp-config.php`. Sin eso todos los visitantes comparten la IP del proxy y el límite de 5 envíos en 10 minutos se vuelve global.
- [ ] UpdraftPlus: programar los respaldos y elegir dónde se guardan (Día 19). Hoy no hace ninguno.
- [ ] Staging (Día 16): confirmar que la carpeta privada de CV queda fuera de la web pública. Si el hosting no deja escribir ahí, el tema usa `wp-content/uploads/dimsop-private/cv` y en nginx hay que bloquearla con `location ^~ /wp-content/uploads/dimsop-private/ { deny all; }`. Incluir esa carpeta en los respaldos.
- [ ] Revisar la cabecera HSTS del tema con `preload` e `includeSubDomains` antes de publicar (Día 19): afecta a todos los subdominios de dimsop.com.
- [ ] Meta descripción de la portada y Open Graph en Yoast (Día 15). Sacar del sitemap `sample-page` y `guia-componentes` al borrarlas.
- [ ] Día 15: medir con Lighthouse el peso de Globe.gl en móvil; si penaliza, cargar el globo solo desde 768 px.
- [ ] Desactivar WPForms Lite (no se usa).
- [ ] Borrar los PNG/JPG originales de `assets/images` (ya existen los WebP).
- [ ] Catálogo: página provisional con skeleton. Cuando el cliente entregue el contenido, cambiar la plantilla y quitar el noindex (meta de Yoast "Permitir a los motores de búsqueda…").
- [ ] Borrar los 3 prospectos de prueba ("Prueba ... Día 5"), las 5 entradas de prueba (meta `_dimsop_test_post`, slug `prueba-*`) y "Hello world!" antes de publicar.
- [ ] Unidades: la cuadrícula solo cuadra con 7; con otra cantidad deja huecos, y la novena no aparece.
- [ ] Entrada del blog sin plantilla propia (portada, botones de compartir): previsto para el Día 13.
- [ ] SMTP corporativo: configurar WP Mail SMTP con la cuenta real en staging (Día 16). En local los correos van a Mailpit.
- [ ] Hosting de staging y producción, y acceso al DNS (Días 16, 19, 20).
- [ ] Texto legal definitivo de la política de privacidad (lo entrega el cliente): hoy está el borrador basado en Odoo, en Páginas › Política de privacidad.

## Referencia rápida

Compilar el SCSS (desde la carpeta `wordpress`):

```bash
ELECTRON_RUN_AS_NODE=1 "$LOCALAPPDATA/Programs/Local/Local.exe" node_modules/sass/sass.js --style=expanded --no-source-map dimsop-theme/assets/scss/dimsop-theme.scss dimsop-theme/assets/css/dimsop-theme.css
```
