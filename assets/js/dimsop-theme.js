/**
 * Dimsop Theme - Script Principal
 *
 * @package Dimsop
 * @version 1.0.0
 *
 * Incluye: Dark Mode, header (ocultar/mostrar, progreso, enlace activo,
 * desplazamiento suave), menú móvil y buscador overlay.
 * Pendiente: contadores GSAP, reveals, Globe.gl, marquee y envío AJAX de formularios.
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
            if ( ! icon ) {
                return;
            }
            icon.classList.remove( 'fa-sun-o', 'fa-moon-o' );
            icon.classList.add( currentTheme() === 'dark' ? 'fa-sun-o' : 'fa-moon-o' );
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
            var top = target.getBoundingClientRect().top + window.pageYOffset - headerOffset();
            window.scrollTo( { top: top, behavior: 'smooth' } );
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

    function init() {
        initDarkMode();
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
