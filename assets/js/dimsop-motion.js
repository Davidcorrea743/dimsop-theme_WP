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
                return !! ( node.closest && node.closest( '#dimsop-search-overlay, #dimsop-mobile-nav, [data-lenis-prevent]' ) );
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
            var locked = body.classList.contains( 'dimsop-menu-open' ) || 'hidden' === body.style.overflow;
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
     * Encabezados de sección
     * ------------------------------------------------------------------ */
    function initSectionHeads( gsap ) {
        toArray( document.querySelectorAll( '.dimsop-section h2.dimsop-h2' ) ).forEach( function ( title ) {
            var badge = title.previousElementSibling;
            var lead = title.nextElementSibling;
            badge = badge && badge.classList.contains( 'dimsop-badge' ) ? badge : null;
            lead = lead && 'P' === lead.tagName ? lead : null;

            var words = splitWords( title );
            var tl = gsap.timeline( { scrollTrigger: { trigger: title, start: 'top 88%', once: true } } );

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
            '#contacto form'
        ].join( ',' );
        var items = toArray( document.querySelectorAll( selector ) );
        if ( ! items.length ) {
            return;
        }
        // El desenfoque cuesta en móviles: solo con ratón.
        var blur = finePointer ? 'blur(6px)' : 'blur(0px)';

        gsap.set( items, { autoAlpha: 0, y: 36, filter: blur } );
        ScrollTrigger.batch( items, {
            start: 'top 90%',
            once: true,
            onEnter: function ( batch ) {
                gsap.to( batch, {
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
        } );

        var iso = document.querySelector( '.dimsop-iso' );
        if ( iso ) {
            gsap.from( iso, {
                x: -60,
                autoAlpha: 0,
                duration: 0.8,
                ease: 'power3.out',
                clearProps: 'transform,opacity,visibility',
                scrollTrigger: { trigger: iso, start: 'top 95%', once: true }
            } );
        }

        var globe = document.getElementById( 'dimsop-globe-wrap' );
        if ( globe ) {
            gsap.fromTo( globe,
                { clipPath: 'inset(10% 18% 10% 18% round 1.5rem)', autoAlpha: 0 },
                {
                    clipPath: 'inset(0% 0% 0% 0% round 0rem)',
                    autoAlpha: 1,
                    duration: 1.2,
                    ease: 'power3.out',
                    clearProps: 'clipPath,opacity,visibility',
                    scrollTrigger: { trigger: globe, start: 'top 85%', once: true }
                }
            );
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

            ScrollTrigger.create( {
                trigger: el,
                start: 'top 85%',
                once: true,
                onEnter: function () {
                    strips.forEach( function ( item, index ) {
                        gsap.to( item.strip, {
                            yPercent: -( item.stop / 20 ) * 100,
                            duration: 1.6 + index * 0.25,
                            ease: 'power3.out'
                        } );
                    } );
                }
            } );
        } );
    }

    /* ------------------------------------------------------------------
     * Botones magnéticos (solo ratón). Usa la propiedad CSS `translate`
     * para no pisar el `transform` del :active de los botones.
     * ------------------------------------------------------------------ */
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

            btn.addEventListener( 'pointermove', function ( e ) {
                var rect = btn.getBoundingClientRect();
                moveTo(
                    ( e.clientX - ( rect.left + rect.width / 2 ) ) * 0.3,
                    ( e.clientY - ( rect.top + rect.height / 2 ) ) * 0.4
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

        var lazy = new window.IntersectionObserver( function ( entries ) {
            if ( ! entries[ 0 ].isIntersecting ) {
                return;
            }
            lazy.disconnect();
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
                buildGlobe( wrap, container, settings, results[ 1 ] );
            } ).catch( function ( error ) {
                // Sin red o sin WebGL utilizable: el mapa estático sigue visible.
                if ( window.console ) {
                    window.console.warn( 'Dimsop: el globo 3D no se pudo cargar.', error );
                }
            } );
        }, { rootMargin: '300px 0px' } );

        lazy.observe( wrap );
    }

    function buildGlobe( wrap, container, settings, countries ) {
        if ( 'function' !== typeof window.Globe ) {
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

        var world = new window.Globe( container, { animateIn: ! reduceMotion } )
            .width( container.clientWidth )
            .height( container.clientHeight )
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

        var controls = world.controls();
        controls.enableZoom = false; // la rueda sigue moviendo la página
        controls.autoRotate = ! reduceMotion;
        controls.autoRotateSpeed = 0.8;
        if ( ! finePointer ) {
            // En pantallas táctiles el dedo debe desplazar la página, no girar el globo.
            controls.enabled = false;
            var canvas = container.querySelector( 'canvas' );
            if ( canvas ) {
                canvas.style.touchAction = 'pan-y';
            }
        }

        wrap.classList.add( 'is-globe-ready' );

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
                world.width( container.clientWidth ).height( container.clientHeight );
            } ).observe( container );
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
            initSectionHeads( gsap );
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
