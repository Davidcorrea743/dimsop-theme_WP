/**
 * Dimsop Theme - Script Principal
 *
 * @package Dimsop
 * @version 1.0.0
 *
 * Incluye: Dark Mode, header (ocultar/mostrar, progreso, enlace activo,
 * desplazamiento suave), menú móvil, buscador overlay, spotlight de tarjetas y carruseles.
 * Pendiente: contadores GSAP, reveals, Globe.gl y envío AJAX de formularios.
 */
( function () {
    'use strict';

    var settings = window.dimsopSettings || {};

    /**
     * Dark Mode: alterna data-theme en <html> y persiste en localStorage.
     * Sincroniza el icono del botón (sol en oscuro / luna en claro).
     */
    function initDarkMode() {
        var toggle = document.getElementById( 'dimsop-dark-toggle' );
        if ( ! toggle ) {
            return;
        }

        var root = document.documentElement;
        var icon = toggle.querySelector( 'i' );

        function currentTheme() {
            return root.getAttribute( 'data-theme' ) === 'light' ? 'light' : 'dark';
        }

        function syncIcon() {
            var dark = currentTheme() === 'dark';
            var label = toggle.getAttribute( dark ? 'data-label-light' : 'data-label-dark' );
            if ( label ) {
                toggle.setAttribute( 'aria-label', label );
            }
            if ( ! icon ) {
                return;
            }
            icon.classList.remove( 'fa-sun-o', 'fa-moon-o' );
            icon.classList.add( dark ? 'fa-sun-o' : 'fa-moon-o' );
        }

        toggle.addEventListener( 'click', function () {
            var next = currentTheme() === 'dark' ? 'light' : 'dark';
            root.setAttribute( 'data-theme', next );
            try {
                localStorage.setItem( 'dimsop-theme', next );
            } catch ( e ) {
                /* localStorage no disponible */
            }
            syncIcon();
        } );

        syncIcon();
    }

    /**
     * Header: se oculta al bajar y reaparece al subir, más la barra de progreso de lectura.
     */
    function initHeaderScroll() {
        var header = document.getElementById( 'dimsop_header' );
        if ( ! header ) {
            return;
        }

        var progressBar = document.getElementById( 'dimsop-scroll-progress' );
        var threshold = 80;
        var lastScroll = 0;
        var ticking = false;

        function update() {
            var current = window.pageYOffset || document.documentElement.scrollTop;

            if ( current > threshold && ! document.body.classList.contains( 'dimsop-menu-open' ) ) {
                var goingDown = current > lastScroll;
                header.classList.toggle( 'dimsop-header-hidden', goingDown );
                header.classList.toggle( 'dimsop-header-visible', ! goingDown );
            } else {
                header.classList.remove( 'dimsop-header-hidden', 'dimsop-header-visible' );
            }
            lastScroll = current;

            if ( progressBar ) {
                var docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                progressBar.style.width = ( docHeight > 0 ? ( current / docHeight ) * 100 : 0 ) + '%';
            }
            ticking = false;
        }

        window.addEventListener( 'scroll', function () {
            if ( ! ticking ) {
                window.requestAnimationFrame( update );
                ticking = true;
            }
        }, { passive: true } );

        update();
    }

    /**
     * Devuelve el elemento destino si el enlace apunta a un ancla de esta misma página.
     */
    function samePageTarget( link ) {
        if ( ! link.hash || link.hash.length < 2 ) {
            return null;
        }
        if ( link.pathname.replace( /\/$/, '' ) !== window.location.pathname.replace( /\/$/, '' ) ||
            link.host !== window.location.host ) {
            return null;
        }
        try {
            return document.querySelector( link.hash );
        } catch ( e ) {
            return null;
        }
    }

    function headerOffset() {
        var header = document.getElementById( 'dimsop_header' );
        return header ? header.offsetHeight : 70;
    }

    /**
     * Desplazamiento suave a las anclas de la página, compensando la altura del header.
     */
    function initSmoothScroll() {
        document.addEventListener( 'click', function ( e ) {
            var link = e.target.closest( 'a[href*="#"]' );
            if ( ! link || link.hasAttribute( 'data-bs-toggle' ) ) {
                return;
            }
            var target = samePageTarget( link );
            if ( ! target ) {
                return;
            }
            e.preventDefault();
            if ( window.dimsopLenis ) {
                // Con Lenis activo (dimsop-motion.js) el desplazamiento lo hace él para no pelear con su inercia.
                window.dimsopLenis.scrollTo( target, { offset: -headerOffset() } );
            } else {
                var top = target.getBoundingClientRect().top + window.pageYOffset - headerOffset();
                window.scrollTo( { top: top, behavior: 'smooth' } );
            }
            if ( window.history && window.history.replaceState ) {
                window.history.replaceState( null, '', link.hash );
            }
        } );
    }

    /**
     * Resalta en el menú la sección visible de la página de inicio.
     */
    function initActiveNav() {
        var links = Array.prototype.slice.call( document.querySelectorAll( '.dimsop-nav .nav-link' ) );
        var homeLinks = links.filter( function ( link ) {
            return samePageTarget( link );
        } );
        if ( ! homeLinks.length ) {
            return;
        }

        var homeLink = links.filter( function ( link ) {
            return ! link.hash && link.pathname.replace( /\/$/, '' ) === window.location.pathname.replace( /\/$/, '' );
        } )[ 0 ] || null;

        var ticking = false;

        function update() {
            var scrollPos = ( window.pageYOffset || document.documentElement.scrollTop ) + headerOffset() + 50;
            var active = homeLink;

            homeLinks.forEach( function ( link ) {
                var target = samePageTarget( link );
                if ( target && target.getBoundingClientRect().top + window.pageYOffset <= scrollPos ) {
                    active = link;
                }
            } );

            links.forEach( function ( link ) {
                link.classList.toggle( 'dimsop-nav-active', link === active );
            } );
            ticking = false;
        }

        window.addEventListener( 'scroll', function () {
            if ( ! ticking ) {
                window.requestAnimationFrame( update );
                ticking = true;
            }
        }, { passive: true } );

        update();
    }

    /**
     * Menú móvil: abre/cierra el panel, se cierra al elegir un enlace, al pulsar fuera o con Esc.
     */
    function initMobileMenu() {
        var burger = document.getElementById( 'dimsop-burger-toggle' );
        var panel = document.getElementById( 'dimsop-mobile-nav' );
        if ( ! burger || ! panel ) {
            return;
        }

        function setOpen( open ) {
            panel.classList.toggle( 'dimsop-mobile-open', open );
            burger.classList.toggle( 'dimsop-burger-active', open );
            document.body.classList.toggle( 'dimsop-menu-open', open );
            burger.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
            panel.setAttribute( 'aria-hidden', open ? 'false' : 'true' );
        }

        burger.addEventListener( 'click', function () {
            setOpen( ! panel.classList.contains( 'dimsop-mobile-open' ) );
        } );

        panel.addEventListener( 'click', function ( e ) {
            if ( e.target.closest( '.nav-link' ) ) {
                setOpen( false );
            }
        } );

        document.addEventListener( 'click', function ( e ) {
            if ( ! burger.contains( e.target ) && ! panel.contains( e.target ) ) {
                setOpen( false );
            }
        } );

        document.addEventListener( 'keydown', function ( e ) {
            if ( 'Escape' === e.key && panel.classList.contains( 'dimsop-mobile-open' ) ) {
                setOpen( false );
                burger.focus();
            }
        } );

        // Al pasar a escritorio el panel deja de tener sentido.
        window.addEventListener( 'resize', function () {
            if ( window.innerWidth >= 992 ) {
                setOpen( false );
            }
        } );
    }

    /**
     * Buscador overlay a pantalla completa.
     */
    function initSearchOverlay() {
        var overlay = document.getElementById( 'dimsop-search-overlay' );
        var openBtn = document.getElementById( 'dimsop-search-open' );
        if ( ! overlay || ! openBtn ) {
            return;
        }

        var input = document.getElementById( 'dimsop-search-input' );
        var closers = [
            document.getElementById( 'dimsop-search-close' ),
            document.getElementById( 'dimsop-search-close-btn' )
        ];

        function setOpen( open ) {
            overlay.classList.toggle( 'dimsop-search-active', open );
            overlay.setAttribute( 'aria-hidden', open ? 'false' : 'true' );
            document.body.classList.toggle( 'dimsop-search-open', open );
            if ( open && input ) {
                window.setTimeout( function () {
                    input.focus();
                }, 50 );
            } else if ( ! open ) {
                openBtn.focus();
            }
        }

        openBtn.addEventListener( 'click', function () {
            setOpen( true );
        } );

        closers.forEach( function ( el ) {
            if ( el ) {
                el.addEventListener( 'click', function () {
                    setOpen( false );
                } );
            }
        } );

        document.addEventListener( 'keydown', function ( e ) {
            if ( 'Escape' === e.key && overlay.classList.contains( 'dimsop-search-active' ) ) {
                setOpen( false );
            }
        } );
    }

    /**
     * Spotlight de tarjetas: pasa la posición del cursor a --mx/--my.
     * Solo en dispositivos con ratón; en táctil la tarjeta queda estática.
     */
    function initSpotlight() {
        if ( ! window.matchMedia || ! window.matchMedia( '(hover: hover) and (pointer: fine)' ).matches ) {
            return;
        }

        document.addEventListener( 'pointermove', function ( e ) {
            var card = e.target.closest && e.target.closest( '.dimsop-ui-card' );
            if ( ! card ) {
                return;
            }
            var rect = card.getBoundingClientRect();
            card.style.setProperty( '--mx', ( e.clientX - rect.left ) + 'px' );
            card.style.setProperty( '--my', ( e.clientY - rect.top ) + 'px' );
        }, { passive: true } );
    }

    /**
     * Carruseles en bucle: avance automático, botones, arrastre con inercia y
     * rueda horizontal. Sustituye a la animación CSS, que queda como respaldo sin JS.
     * Con movimiento reducido no hay avance automático ni inercia.
     */
    function initMarquees() {
        var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

        Array.prototype.forEach.call( document.querySelectorAll( '[data-marquee]' ), function ( root ) {
            var viewport = root.querySelector( '.dimsop-marquee-container' );
            var track = root.querySelector( '.dimsop-marquee-track' );
            if ( ! viewport || ! track ) {
                return;
            }

            var speed = parseFloat( root.getAttribute( 'data-speed' ) ) || 50; // px por segundo
            var currentSpeed = reduceMotion ? 0 : speed; // se acerca a speed o a 0 poco a poco
            var x = 0;
            var loopWidth = 0;
            var velocity = 0;
            var glide = 0; // distancia pendiente de los botones
            var hovering = false;
            var focused = false;
            var dragging = false;
            var resumeAt = 0;
            var lastFrame = 0;
            var lastPointerX = 0;
            var lastPointerTime = 0;
            var rafId = null;

            function measure() {
                loopWidth = track.scrollWidth / 2;
            }

            function render() {
                if ( loopWidth > 0 ) {
                    x = ( ( x % loopWidth ) + loopWidth ) % loopWidth;
                }
                track.style.transform = 'translate3d(' + ( -x ) + 'px, 0, 0)';
            }

            function frame( time ) {
                var dt = Math.min( ( time - lastFrame ) / 1000, 0.05 );
                lastFrame = time;

                if ( ! dragging ) {
                    if ( glide ) {
                        var step = reduceMotion ? glide : glide * Math.min( 1, dt * 8 );
                        if ( Math.abs( glide - step ) < 0.5 ) {
                            step = glide;
                        }
                        x += step;
                        glide -= step;
                    } else if ( Math.abs( velocity ) > 5 ) {
                        x += velocity * dt;
                        velocity *= Math.pow( 0.05, dt );
                    } else {
                        velocity = 0;
                        // Al pasar el ratón frena en ~1 s en vez de pararse en seco, y al salir acelera igual.
                        var targetSpeed = ( reduceMotion || hovering || focused || time < resumeAt ) ? 0 : speed;
                        currentSpeed += ( targetSpeed - currentSpeed ) * Math.min( 1, dt * 3 );
                        if ( 0 === targetSpeed && currentSpeed < 0.5 ) {
                            currentSpeed = 0;
                        }
                        x += currentSpeed * dt;
                    }
                }

                render();
                rafId = window.requestAnimationFrame( frame );
            }

            function start() {
                if ( null === rafId ) {
                    lastFrame = window.performance.now();
                    rafId = window.requestAnimationFrame( frame );
                }
            }

            function stop() {
                if ( null !== rafId ) {
                    window.cancelAnimationFrame( rafId );
                    rafId = null;
                }
            }

            function nudge( direction ) {
                var item = track.firstElementChild;
                var itemWidth = item ? item.offsetWidth : 0;
                velocity = 0;
                currentSpeed = 0;
                glide += direction * Math.max( viewport.clientWidth * 0.6, itemWidth );
                resumeAt = window.performance.now() + 2000;
            }

            function endDrag() {
                if ( ! dragging ) {
                    return;
                }
                dragging = false;
                root.classList.remove( 'is-dragging' );
                var now = window.performance.now();
                // Sin inercia si el puntero se quedó quieto antes de soltar.
                if ( reduceMotion || now - lastPointerTime > 100 ) {
                    velocity = 0;
                }
                velocity = Math.max( -3000, Math.min( 3000, velocity ) );
                resumeAt = now + 1500;
            }

            Array.prototype.forEach.call( root.querySelectorAll( '.dimsop-marquee-btn' ), function ( btn ) {
                btn.hidden = false;
                btn.addEventListener( 'click', function () {
                    nudge( btn.classList.contains( 'dimsop-marquee-btn--prev' ) ? -1 : 1 );
                } );
            } );

            viewport.addEventListener( 'pointerdown', function ( e ) {
                if ( 'mouse' === e.pointerType && 0 !== e.button ) {
                    return;
                }
                dragging = true;
                glide = 0;
                velocity = 0;
                currentSpeed = 0;
                lastPointerX = e.clientX;
                lastPointerTime = window.performance.now();
                root.classList.add( 'is-dragging' );
                viewport.setPointerCapture( e.pointerId );
            } );

            viewport.addEventListener( 'pointermove', function ( e ) {
                if ( ! dragging ) {
                    return;
                }
                var now = window.performance.now();
                var dx = e.clientX - lastPointerX;
                var elapsed = Math.max( ( now - lastPointerTime ) / 1000, 0.001 );
                x -= dx;
                velocity = 0.8 * ( -dx / elapsed ) + 0.2 * velocity;
                lastPointerX = e.clientX;
                lastPointerTime = now;
            } );

            viewport.addEventListener( 'pointerup', endDrag );
            viewport.addEventListener( 'pointercancel', endDrag );

            // Evita el arrastre nativo de las imágenes.
            track.addEventListener( 'dragstart', function ( e ) {
                e.preventDefault();
            } );

            // Solo gestos horizontales (o Mayús + rueda): la rueda vertical sigue moviendo la página.
            viewport.addEventListener( 'wheel', function ( e ) {
                var delta = Math.abs( e.deltaX ) > Math.abs( e.deltaY ) ? e.deltaX : ( e.shiftKey ? e.deltaY : 0 );
                if ( ! delta ) {
                    return;
                }
                e.preventDefault();
                glide = 0;
                velocity = 0;
                x += delta;
                resumeAt = window.performance.now() + 1500;
            }, { passive: false } );

            root.addEventListener( 'pointerenter', function ( e ) {
                hovering = 'mouse' === e.pointerType;
            } );
            root.addEventListener( 'pointerleave', function () {
                hovering = false;
            } );

            // Pausa al navegar con teclado (un clic con ratón no cuenta como foco visible).
            root.addEventListener( 'focusin', function ( e ) {
                focused = e.target.matches( ':focus-visible' );
            } );
            root.addEventListener( 'focusout', function () {
                focused = false;
            } );

            root.classList.add( 'is-enhanced' );
            measure();
            render();

            if ( window.ResizeObserver ) {
                new window.ResizeObserver( measure ).observe( track );
            } else {
                window.addEventListener( 'resize', measure );
            }

            // Fuera de pantalla no gasta animación.
            if ( window.IntersectionObserver ) {
                new window.IntersectionObserver( function ( entries ) {
                    if ( entries[ 0 ].isIntersecting ) {
                        start();
                    } else {
                        stop();
                    }
                } ).observe( root );
            } else {
                start();
            }
        } );
    }

    /**
     * Formulario de contacto: envía por AJAX y muestra el aviso junto al botón, sin recargar.
     * El aviso desaparece solo tras un minuto. Sin JS, el formulario envía por POST.
     */
    function initContactForm() {
        var form = document.querySelector( '#contacto .dimsop-form' );
        var feedback = document.getElementById( 'dimsop-contact-feedback' );
        if ( ! form || ! feedback || ! settings.ajaxUrl || ! window.fetch ) {
            return;
        }

        var i18n = settings.i18n || {};
        var submit = form.querySelector( '[type="submit"]' );
        var label = submit.querySelector( '.dimsop-button-label' );
        var icon = submit.querySelector( '.fa' );
        var labelText = label.textContent;
        var iconClass = icon.className;
        var hideDelay = 60000;
        var hideTimer = null;

        function hideLater( alert ) {
            window.clearTimeout( hideTimer );
            hideTimer = window.setTimeout( function () {
                alert.classList.add( 'is-hiding' );
                window.setTimeout( function () {
                    alert.remove();
                }, 400 );
            }, hideDelay );
        }

        function showAlert( success, message ) {
            var alert = document.createElement( 'div' );
            var alertIcon = document.createElement( 'i' );
            var text = document.createElement( 'span' );

            alert.className = 'dimsop-alert dimsop-alert--' + ( success ? 'success' : 'error' );
            alertIcon.className = 'fa ' + ( success ? 'fa-check-circle' : 'fa-exclamation-circle' );
            alertIcon.setAttribute( 'aria-hidden', 'true' );
            text.textContent = message;
            alert.appendChild( alertIcon );
            alert.appendChild( text );

            feedback.textContent = '';
            feedback.appendChild( alert );
            alert.scrollIntoView( { block: 'nearest' } );
            hideLater( alert );
        }

        function setLoading( loading ) {
            submit.disabled = loading;
            label.textContent = loading ? ( i18n.submitting || labelText ) : labelText;
            icon.className = loading ? 'fa fa-circle-o-notch fa-spin' : iconClass;
        }

        // Aviso del envío sin JS: se limpia la URL para que no reaparezca al recargar.
        var serverAlert = feedback.querySelector( '.dimsop-alert' );
        if ( serverAlert ) {
            hideLater( serverAlert );
            if ( window.history && window.history.replaceState ) {
                window.history.replaceState( null, '', window.location.pathname + window.location.hash );
            }
        }

        form.addEventListener( 'submit', function ( e ) {
            e.preventDefault();
            setLoading( true );

            // new FormData incluye action, nonce y honeypot; el campo "action" tapa a form.action.
            window.fetch( settings.ajaxUrl, { method: 'POST', body: new FormData( form ), credentials: 'same-origin' } )
                .then( function ( response ) {
                    return response.json();
                } )
                .then( function ( result ) {
                    var message = result.data && result.data.message ? result.data.message : i18n.errorGeneric;
                    showAlert( !! result.success, message );
                    if ( result.success ) {
                        form.reset();
                    }
                } )
                .catch( function () {
                    showAlert( false, i18n.errorGeneric );
                } )
                .then( function () {
                    setLoading( false );
                } );
        } );
    }

    /**
     * Banner: al cruzar los 992 px sin recargar (p. ej. al girar una tablet) se vuelve a
     * elegir el recurso de esa vista: el navegador solo lo hace por sí mismo al cargar.
     */
    function initBannerMedia() {
        var video = document.querySelector( '.dimsop-banner-video' );
        var image = document.querySelector( '.dimsop-banner-image' );
        if ( ! video || ! image || ! window.matchMedia ) {
            return;
        }

        var desktop = window.matchMedia( '(min-width: 992px)' );
        if ( ! desktop.addEventListener ) {
            return;
        }

        desktop.addEventListener( 'change', function ( e ) {
            if ( e.matches ) {
                if ( ! video.currentSrc ) {
                    video.load();
                }
            } else {
                image.srcset = image.getAttribute( 'srcset' );
            }
        } );
    }

    function init() {
        initDarkMode();
        initBannerMedia();
        initSpotlight();
        initMarquees();
        initContactForm();
        initHeaderScroll();
        initSmoothScroll();
        initActiveNav();
        initMobileMenu();
        initSearchOverlay();
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }

    /* Expuesto para depuración desde consola */
    window.dimsopTheme = { settings: settings, version: '1.0.0' };
} )();
