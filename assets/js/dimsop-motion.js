/**
 * Dimsop Theme - Capa de animación
 *
 * GSAP + ScrollTrigger + Lenis. Todo es progresivo: sin JS, sin GSAP o con
 * "reducir movimiento" el contenido se ve completo y estático.
 *
 * - Lenis: desplazamiento con inercia ligera, sincronizado con ScrollTrigger.
 * - Banner: título palabra por palabra, texto con desenfoque y parallax del video.
 * - Secciones: títulos palabra por palabra; tarjetas con desvanecido, subida y desenfoque.
 * - Contadores: cifras en rodillos que giran hasta su valor.
 * - Botones de acción: efecto magnético (solo ratón).
 * - Globo 3D (Globe.gl) en Sedes, descargado solo al acercarse a la sección.
 *
 * @package Dimsop
 */
( function () {
    'use strict';

    var root = document.documentElement;
    var config = window.dimsopMotion || {};
    var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
    var finePointer = window.matchMedia && window.matchMedia( '(hover: hover) and (pointer: fine)' ).matches;

    /** Muestra lo que el <head> ocultó (texto del banner). */
    function markReady() {
        window.dimsopMotionReady = true;
        root.classList.add( 'dimsop-motion-ready' );
    }

    function toArray( list ) {
        return Array.prototype.slice.call( list );
    }

    /* ------------------------------------------------------------------
     * Lenis
     * ------------------------------------------------------------------ */
    function initLenis() {
        if ( ! window.Lenis ) {
            return;
        }

        var lenis = new window.Lenis( {
            duration: 1.1,
            smoothWheel: true,
            // Dentro del buscador y del menú móvil el desplazamiento es el nativo.
            prevent: function ( node ) {
                return !! ( node.closest && node.closest( '#dimsop-search-overlay, #dimsop-mobile-nav, .modal, [data-lenis-prevent]' ) );
            }
        } );

        lenis.on( 'scroll', window.ScrollTrigger.update );
        window.gsap.ticker.add( function ( time ) {
            lenis.raf( time * 1000 );
        } );
        window.gsap.ticker.lagSmoothing( 0 );

        // Con el menú móvil o el buscador abiertos la página no debe moverse.
        var body = document.body;
        new window.MutationObserver( function () {
            var locked = body.classList.contains( 'dimsop-menu-open' ) || body.classList.contains( 'modal-open' ) || 'hidden' === body.style.overflow;
            if ( locked ) {
                lenis.stop();
            } else {
                lenis.start();
            }
        } ).observe( body, { attributes: true, attributeFilter: [ 'class', 'style' ] } );

        window.dimsopLenis = lenis;
    }

    /* ------------------------------------------------------------------
     * Títulos palabra por palabra
     * ------------------------------------------------------------------ */

    /**
     * Envuelve cada palabra en una máscara. El lector de pantalla lee el
     * aria-label del título completo, no las palabras sueltas.
     *
     * @return {Element[]|null} Palabras animables, o null si el título tiene marcado propio.
     */
    function splitWords( el ) {
        if ( ! el || el.children.length || el.hasAttribute( 'data-split' ) ) {
            return null;
        }
        var text = el.textContent.replace( /\s+/g, ' ' ).trim();
        if ( ! text ) {
            return null;
        }
        var parts = text.split( ' ' );
        var words = [];

        el.setAttribute( 'aria-label', text );
        el.setAttribute( 'data-split', '' );
        el.textContent = '';

        parts.forEach( function ( word, i ) {
            var mask = document.createElement( 'span' );
            var inner = document.createElement( 'span' );
            mask.className = 'dimsop-word';
            mask.setAttribute( 'aria-hidden', 'true' );
            inner.className = 'dimsop-word-inner';
            inner.textContent = word;
            mask.appendChild( inner );
            el.appendChild( mask );
            if ( i < parts.length - 1 ) {
                el.appendChild( document.createTextNode( ' ' ) );
            }
            words.push( inner );
        } );

        return words;
    }

    /* ------------------------------------------------------------------
     * Banner
     * ------------------------------------------------------------------ */
    function initBanner( gsap ) {
        var banner = document.getElementById( 'banner' );
        if ( ! banner ) {
            return;
        }
        var title = banner.querySelector( '.dimsop-h1' );
        var lead = banner.querySelector( '.dimsop-banner-lead' );
        var words = splitWords( title );
        var intro = gsap.timeline( { delay: 0.15 } );

        if ( words ) {
            intro.from( words, { yPercent: 115, duration: 0.95, ease: 'power4.out', stagger: 0.055 } );
        } else if ( title ) {
            intro.from( title, { y: 30, autoAlpha: 0, duration: 0.9, ease: 'power3.out' } );
        }
        if ( lead ) {
            intro.from( lead, {
                y: 24,
                autoAlpha: 0,
                filter: 'blur(8px)',
                duration: 0.9,
                ease: 'power3.out',
                clearProps: 'filter'
            }, '-=0.55' );
        }

        // Parallax solo en escritorio, donde hay video detrás del texto.
        gsap.matchMedia().add( '(min-width: 992px)', function () {
            var trigger = { trigger: banner, start: 'top top', end: 'bottom top', scrub: true };
            gsap.to( banner.querySelector( '.dimsop-banner-video' ), { yPercent: 18, ease: 'none', scrollTrigger: trigger } );
            gsap.to( banner.querySelector( '.container' ), { y: -60, opacity: 0.35, ease: 'none', scrollTrigger: trigger } );
        } );
    }

    /* ------------------------------------------------------------------
     * Repetición al volver a entrar
     *
     * La animación se reproduce cada vez que el bloque entra en pantalla,
     * bajando o subiendo. Solo se rearma (vuelve a su estado inicial) cuando
     * el bloque salió del todo de la pantalla, así nunca se ve desaparecer.
     *
     * Mientras ScrollTrigger recalcula (cambio de tamaño de ventana, giro del
     * móvil, contenido que crece como el mapa del footer) sus callbacks pueden
     * dispararse con posiciones intermedias: se ignoran y, al terminar, cada
     * bloque se sincroniza con la posición real.
     * ------------------------------------------------------------------ */
    var entries = [];
    var refreshing = false;
    var refreshListening = false;

    function syncEntries() {
        // Tras el evento ScrollTrigger aún restaura el scroll: se espera un turno.
        window.setTimeout( function () {
            refreshing = false;
            entries.forEach( function ( entry ) {
                entry.sync();
            } );
        }, 0 );
    }

    function onEachEntry( ScrollTrigger, trigger, play, reset, start ) {
        var armed = true;

        if ( ! refreshListening ) {
            refreshListening = true;
            ScrollTrigger.addEventListener( 'refreshInit', function () {
                refreshing = true;
            } );
            ScrollTrigger.addEventListener( 'refresh', syncEntries );
        }

        var playTrigger = ScrollTrigger.create( {
            trigger: trigger,
            start: start || 'top 88%',
            end: 'bottom 12%',
            onEnter: function () {
                if ( armed && ! refreshing ) {
                    armed = false;
                    play( 1 );
                }
            },
            onEnterBack: function () {
                if ( armed && ! refreshing ) {
                    armed = false;
                    play( -1 );
                }
            }
        } );

        var resetTrigger = ScrollTrigger.create( {
            trigger: trigger,
            start: 'top bottom',
            end: 'bottom top',
            onLeave: function () {
                if ( ! refreshing ) {
                    armed = true;
                    reset( -1 ); // quedó por encima: al volver entrará desde arriba
                }
            },
            onLeaveBack: function () {
                if ( ! refreshing ) {
                    armed = true;
                    reset( 1 ); // quedó por debajo: entrará desde abajo
                }
            }
        } );

        // Se compara con la posición real del scroll (isActive aún puede reflejar
        // las posiciones intermedias del recálculo).
        entries.push( {
            sync: function () {
                var y = resetTrigger.scroll();
                var inPlay = y >= playTrigger.start && y <= playTrigger.end;
                var onScreen = y > resetTrigger.start && y < resetTrigger.end;
                if ( inPlay && armed ) {
                    // Está en pantalla y no se había mostrado: se muestra.
                    armed = false;
                    play( 1 );
                } else if ( ! onScreen && ! armed ) {
                    // Quedó fuera de la pantalla ya mostrado: se rearma.
                    armed = true;
                    reset( y >= resetTrigger.end ? -1 : 1 );
                }
            }
        } );
    }

    /** Atajo para una línea de tiempo en pausa: se reproduce al entrar y vuelve a 0 al salir. */
    function replayTimeline( ScrollTrigger, trigger, timeline, start ) {
        onEachEntry( ScrollTrigger, trigger, function () {
            timeline.restart();
        }, function () {
            timeline.pause( 0 );
        }, start );
    }

    /* ------------------------------------------------------------------
     * Encabezados de sección
     * ------------------------------------------------------------------ */
    function initSectionHeads( gsap, ScrollTrigger ) {
        toArray( document.querySelectorAll( '.dimsop-section h2.dimsop-h2' ) ).forEach( function ( title ) {
            var badge = title.previousElementSibling;
            var lead = title.nextElementSibling;
            badge = badge && badge.classList.contains( 'dimsop-badge' ) ? badge : null;
            lead = lead && 'P' === lead.tagName ? lead : null;

            var words = splitWords( title );
            var tl = gsap.timeline( { paused: true } );

            if ( badge ) {
                tl.from( badge, { y: 12, autoAlpha: 0, duration: 0.5, ease: 'power2.out' } );
            }
            if ( words ) {
                tl.from( words, { yPercent: 115, duration: 0.8, ease: 'power4.out', stagger: 0.05 }, badge ? '-=0.25' : 0 );
            } else {
                tl.from( title, { y: 30, autoAlpha: 0, duration: 0.8, ease: 'power3.out' }, badge ? '-=0.25' : 0 );
            }
            if ( lead ) {
                tl.from( lead, {
                    y: 20,
                    autoAlpha: 0,
                    filter: 'blur(6px)',
                    duration: 0.8,
                    ease: 'power3.out',
                    clearProps: 'filter'
                }, '-=0.5' );
            }

            replayTimeline( ScrollTrigger, title, tl );
        } );
    }

    /* ------------------------------------------------------------------
     * Tarjetas y bloques: desvanecido + subida + desenfoque, en grupos escalonados
     * ------------------------------------------------------------------ */
    function initReveals( gsap, ScrollTrigger ) {
        var selector = [
            '.dimsop-section .dimsop-ui-card:not(.dimsop-project-card)',
            '.dimsop-section .dimsop-marquee',
            '.dimsop-metodologia-container',
            '#metodologia .d-md-none',
            '.dimsop-cobertura-text',
            '#contacto form',
            '.dimsop-footer-col'
        ].join( ',' );
        var items = toArray( document.querySelectorAll( selector ) );
        if ( ! items.length ) {
            return;
        }
        // El desenfoque cuesta en móviles: solo con ratón.
        var blur = finePointer ? 'blur(6px)' : 'blur(0px)';

        function hide( item, direction ) {
            gsap.killTweensOf( item );
            gsap.set( item, { autoAlpha: 0, y: 36 * direction, filter: blur } );
        }

        // Las tarjetas que entran a la vez se escalonan entre sí.
        var queue = [];
        var flushId = null;

        function flush() {
            flushId = null;
            gsap.to( queue.splice( 0 ), {
                autoAlpha: 1,
                y: 0,
                filter: 'blur(0px)',
                duration: 0.85,
                ease: 'power3.out',
                stagger: 0.09,
                overwrite: true,
                // Al terminar se devuelve el control al CSS (hover de las tarjetas).
                clearProps: 'transform,filter,opacity,visibility'
            } );
        }

        items.forEach( function ( item ) {
            hide( item, 1 );
            onEachEntry( ScrollTrigger, item, function () {
                queue.push( item );
                if ( null === flushId ) {
                    flushId = window.setTimeout( flush, 0 );
                }
            }, function ( direction ) {
                hide( item, direction );
            }, 'top 90%' );
        } );

        var iso = document.querySelector( '.dimsop-iso' );
        if ( iso ) {
            replayTimeline( ScrollTrigger, iso, gsap.timeline( { paused: true } ).from( iso, {
                x: -60,
                autoAlpha: 0,
                duration: 0.8,
                ease: 'power3.out',
                clearProps: 'transform,opacity,visibility'
            } ), 'top 95%' );
        }

        var globe = document.getElementById( 'dimsop-globe-wrap' );
        if ( globe ) {
            replayTimeline( ScrollTrigger, globe, gsap.timeline( { paused: true } ).fromTo( globe,
                { clipPath: 'inset(10% 18% 10% 18% round 1.5rem)', autoAlpha: 0 },
                {
                    clipPath: 'inset(0% 0% 0% 0% round 0rem)',
                    autoAlpha: 1,
                    duration: 1.2,
                    ease: 'power3.out',
                    clearProps: 'clipPath,opacity,visibility'
                }
            ), 'top 85%' );
        }
    }

    /* ------------------------------------------------------------------
     * Contadores en rodillos
     * ------------------------------------------------------------------ */
    function initCounters( gsap, ScrollTrigger ) {
        toArray( document.querySelectorAll( '.dimsop-counter-num[data-target]' ) ).forEach( function ( el ) {
            var target = parseInt( el.getAttribute( 'data-target' ), 10 );
            if ( isNaN( target ) || target < 0 ) {
                return;
            }
            var finalText = el.textContent.trim();
            var prefix = ( finalText.match( /^\D*/ ) || [ '' ] )[ 0 ];
            var strips = [];

            el.setAttribute( 'aria-label', finalText );
            el.classList.add( 'is-rolling' );
            el.textContent = '';

            if ( prefix ) {
                var sign = document.createElement( 'span' );
                sign.className = 'dimsop-roll-digit';
                sign.setAttribute( 'aria-hidden', 'true' );
                sign.textContent = prefix;
                el.appendChild( sign );
            }

            String( target ).split( '' ).forEach( function ( digit ) {
                var column = document.createElement( 'span' );
                var strip = document.createElement( 'span' );
                column.className = 'dimsop-roll';
                column.setAttribute( 'aria-hidden', 'true' );
                strip.className = 'dimsop-roll-strip';
                // Dos vueltas de 0 a 9 para que el rodillo gire aunque la cifra sea baja.
                for ( var i = 0; i < 20; i++ ) {
                    var cell = document.createElement( 'span' );
                    cell.className = 'dimsop-roll-digit';
                    cell.textContent = String( i % 10 );
                    strip.appendChild( cell );
                }
                column.appendChild( strip );
                el.appendChild( column );
                strips.push( { strip: strip, stop: 10 + Number( digit ) } );
            } );

            // Gira cada vez que la sección entra en pantalla; fuera de ella vuelve a 0.
            onEachEntry( ScrollTrigger, el, function () {
                strips.forEach( function ( item, index ) {
                    gsap.fromTo( item.strip, { yPercent: 0 }, {
                        yPercent: -( item.stop / 20 ) * 100,
                        duration: 1.6 + index * 0.25,
                        ease: 'power3.out',
                        overwrite: true
                    } );
                } );
            }, function () {
                strips.forEach( function ( item ) {
                    gsap.killTweensOf( item.strip );
                    gsap.set( item.strip, { yPercent: 0 } );
                } );
            }, 'top 85%' );
        } );
    }

    /* ------------------------------------------------------------------
     * Botones magnéticos (solo ratón). Usa la propiedad CSS `translate`
     * para no pisar el `transform` del :active de los botones.
     * ------------------------------------------------------------------ */
    var MAGNET_X = 6; // px máximos en horizontal
    var MAGNET_Y = 4; // px máximos en vertical

    function initMagnetic( gsap ) {
        if ( ! finePointer ) {
            return;
        }
        toArray( document.querySelectorAll( '.dimsop-btn-cta, .dimsop-button--lg' ) ).forEach( function ( btn ) {
            var pos = { x: 0, y: 0 };

            function moveTo( x, y ) {
                gsap.to( pos, {
                    x: x,
                    y: y,
                    duration: 0.45,
                    ease: 'power3.out',
                    overwrite: true,
                    onUpdate: function () {
                        btn.style.translate = pos.x.toFixed( 2 ) + 'px ' + pos.y.toFixed( 2 ) + 'px';
                    }
                } );
            }

            // Desplazamiento proporcional a la posición del cursor (-1 a 1) y con tope
            // en píxeles: así un botón ancho no invade al de al lado.
            btn.addEventListener( 'pointermove', function ( e ) {
                var rect = btn.getBoundingClientRect();
                var nx = ( e.clientX - ( rect.left + rect.width / 2 ) ) / ( rect.width / 2 );
                var ny = ( e.clientY - ( rect.top + rect.height / 2 ) ) / ( rect.height / 2 );
                moveTo(
                    Math.max( -1, Math.min( 1, nx ) ) * MAGNET_X,
                    Math.max( -1, Math.min( 1, ny ) ) * MAGNET_Y
                );
            } );
            btn.addEventListener( 'pointerleave', function () {
                moveTo( 0, 0 );
            } );
        } );
    }

    /* ------------------------------------------------------------------
     * Globo 3D
     * ------------------------------------------------------------------ */
    function supportsWebGL() {
        try {
            var canvas = document.createElement( 'canvas' );
            return !! ( window.WebGLRenderingContext && ( canvas.getContext( 'webgl2' ) || canvas.getContext( 'webgl' ) ) );
        } catch ( e ) {
            return false;
        }
    }

    function loadScript( src ) {
        return new Promise( function ( resolve, reject ) {
            var script = document.createElement( 'script' );
            script.src = src;
            script.async = true;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild( script );
        } );
    }

    function initGlobe() {
        var wrap = document.getElementById( 'dimsop-globe-wrap' );
        var container = document.getElementById( 'dimsop-globe' );
        var settings = config.globe;

        if ( ! wrap || ! container || ! settings || ! window.IntersectionObserver || ! supportsWebGL() ) {
            return; // Se queda el mapa estático.
        }

        // Indicador de carga: aparece solo si la descarga tarda (evita un destello si es rápida).
        var loader = wrap.querySelector( '.dimsop-globe-loader' );
        var loaderTimer = null;

        function showLoader() {
            loaderTimer = window.setTimeout( function () {
                if ( loader && ! wrap.classList.contains( 'is-globe-ready' ) ) {
                    loader.hidden = false;
                }
            }, 250 );
        }

        function hideLoader() {
            window.clearTimeout( loaderTimer );
            if ( ! loader || loader.hidden ) {
                return;
            }
            loader.classList.add( 'is-leaving' );
            window.setTimeout( function () {
                loader.hidden = true;
                loader.classList.remove( 'is-leaving' );
            }, 650 );
        }

        var lazy = new window.IntersectionObserver( function ( entries ) {
            if ( ! entries[ 0 ].isIntersecting ) {
                return;
            }
            lazy.disconnect();
            showLoader();
            // Algunos paquetes de Globe.gl consultan process.env.
            window.process = window.process || { env: { NODE_ENV: 'production' } };
            Promise.all( [
                loadScript( settings.script ),
                window.fetch( settings.countries ).then( function ( res ) {
                    if ( ! res.ok ) {
                        throw new Error( 'countries ' + res.status );
                    }
                    return res.json();
                } )
            ] ).then( function ( results ) {
                buildGlobe( wrap, container, settings, results[ 1 ], hideLoader );
            } ).catch( function ( error ) {
                hideLoader();
                // Sin red o sin WebGL utilizable: el mapa estático sigue visible.
                if ( window.console ) {
                    window.console.warn( 'Dimsop: el globo 3D no se pudo cargar.', error );
                }
            } );
        }, { rootMargin: '300px 0px' } );

        lazy.observe( wrap );
    }

    function buildGlobe( wrap, container, settings, countries, onReady ) {
        if ( 'function' !== typeof window.Globe ) {
            onReady();
            return;
        }
        var projects = settings.projects || {};
        var accent = 'rgba(77, 163, 47, 0.78)';

        function isProject( feature ) {
            return Object.prototype.hasOwnProperty.call( projects, feature.properties.ADMIN );
        }

        var labels = countries.features.filter( isProject ).map( function ( feature ) {
            var box = feature.bbox || [ 0, 0, 0, 0 ];
            return {
                type: 'country',
                name: projects[ feature.properties.ADMIN ],
                lat: ( box[ 1 ] + box[ 3 ] ) / 2,
                lng: ( box[ 0 ] + box[ 2 ] ) / 2
            };
        } );
        var hqs = ( settings.hqs || [] ).map( function ( hq ) {
            return { type: 'hq', name: hq.name, lat: hq.lat, lng: hq.lng };
        } );

        // Se mide la caja visible (wrap): el contenedor está oculto mientras no tiene canvas y
        // mide 0 × 0; con 0, Globe.gl usa el tamaño de la ventana y en móvil (vertical) el
        // globo quedaba dibujado por debajo de la caja, que se veía vacía.
        var world = new window.Globe( container, { animateIn: ! reduceMotion } )
            .width( wrap.clientWidth )
            .height( wrap.clientHeight )
            .backgroundColor( 'rgba(0,0,0,0)' )
            .globeImageUrl( settings.image )
            .showAtmosphere( true )
            .atmosphereColor( '#3f8fd6' )
            .atmosphereAltitude( 0.16 )
            .polygonsData( countries.features )
            .polygonCapColor( function ( d ) {
                return isProject( d ) ? accent : 'rgba(0,0,0,0)';
            } )
            .polygonSideColor( function () {
                return 'rgba(0, 0, 0, 0.35)';
            } )
            .polygonStrokeColor( function () {
                return 'rgba(255, 255, 255, 0.24)';
            } )
            .polygonAltitude( function ( d ) {
                return isProject( d ) ? 0.015 : 0.002;
            } )
            .htmlElementsData( hqs.concat( labels ) )
            .htmlElement( function ( d ) {
                var el = document.createElement( 'div' );
                if ( 'hq' === d.type ) {
                    el.className = 'dimsop-globe-hq';
                    el.innerHTML = '<svg viewBox="0 0 28 36" width="16" height="20" aria-hidden="true">' +
                        '<path fill="#0a5aa8" stroke="#fff" stroke-width="2" d="M14 1C21 1 27 6.3 27 12.8 27 23.5 14 35 14 35S1 23.6 1 12.8C1 6.3 7 1 14 1Z"/>' +
                        '<circle fill="#fff" cx="14" cy="13" r="5"/></svg>';
                    var label = document.createElement( 'span' );
                    label.textContent = d.name;
                    el.appendChild( label );
                } else {
                    el.className = 'dimsop-globe-label';
                    el.textContent = d.name;
                }
                return el;
            } )
            .pointOfView( { lat: 8, lng: -58, altitude: 1.75 } );

        // Oculta las etiquetas que quedan detrás del globo (Globe.gl 2.41 o superior).
        if ( 'function' === typeof world.htmlElementVisibilityModifier ) {
            world.htmlElementVisibilityModifier( function ( el, isVisible ) {
                el.style.opacity = isVisible ? 1 : 0;
            } );
        }

        window.dimsopGlobe = world; // referencia para depurar desde la consola

        var controls = world.controls();
        controls.enableZoom = false; // la rueda sigue moviendo la página
        controls.autoRotate = ! reduceMotion;
        controls.autoRotateSpeed = 0.8;
        if ( ! finePointer ) {
            // En pantallas táctiles el dedo debe desplazar la página, no girar el globo.
            // Los controles siguen activos: Globe.gl solo actualiza la cámara (y el giro
            // automático) si controls.enabled es true; desactivarlos dejaba la caja en negro.
            controls.enableRotate = false;
            controls.enablePan = false;
            var canvas = container.querySelector( 'canvas' );
            if ( canvas ) {
                canvas.style.touchAction = 'pan-y';
            }
        }

        // El mapa estático se sustituye cuando el globo ya tiene su textura (no una esfera vacía).
        var revealed = false;
        function reveal() {
            if ( revealed ) {
                return;
            }
            revealed = true;
            wrap.classList.add( 'is-globe-ready' );
            onReady();
        }
        if ( 'function' === typeof world.onGlobeReady ) {
            world.onGlobeReady( reveal );
        }
        window.setTimeout( reveal, 8000 ); // por si la textura no llega

        // No dibujar mientras la sección está fuera de pantalla.
        new window.IntersectionObserver( function ( entries ) {
            if ( entries[ 0 ].isIntersecting ) {
                world.resumeAnimation();
            } else {
                world.pauseAnimation();
            }
        } ).observe( wrap );

        if ( window.ResizeObserver ) {
            new window.ResizeObserver( function () {
                if ( wrap.clientWidth && wrap.clientHeight ) {
                    world.width( wrap.clientWidth ).height( wrap.clientHeight );
                }
            } ).observe( wrap );
        }
    }

    /* ------------------------------------------------------------------ */
    function init() {
        var gsap = window.gsap;
        var ScrollTrigger = window.ScrollTrigger;

        if ( reduceMotion || ! gsap || ! ScrollTrigger ) {
            markReady();
            initGlobe(); // no depende de GSAP; con movimiento reducido no gira
            return;
        }

        gsap.registerPlugin( ScrollTrigger );
        gsap.config( { nullTargetWarn: false } );

        try {
            initLenis();
            initBanner( gsap );
            initSectionHeads( gsap, ScrollTrigger );
            initReveals( gsap, ScrollTrigger );
            initCounters( gsap, ScrollTrigger );
            initMagnetic( gsap );
        } finally {
            // Pase lo que pase, el texto del banner no se queda oculto.
            markReady();
        }

        initGlobe();

        // Las imágenes diferidas cambian la altura de la página: recalcular al terminar la carga.
        // Si el navegador saltó a un ancla (#cobertura) o restauró la posición al recargar,
        // Lenis debe partir de ahí y no desde arriba.
        window.addEventListener( 'load', function () {
            ScrollTrigger.refresh();
            var lenis = window.dimsopLenis;
            if ( lenis && ! lenis.isScrolling && Math.abs( lenis.animatedScroll - window.pageYOffset ) > 1 ) {
                lenis.scrollTo( window.pageYOffset, { immediate: true, force: true } );
            }
        } );
    }

    if ( 'loading' === document.readyState ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }
} )();
