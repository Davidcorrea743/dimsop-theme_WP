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

    /* ==================================================================
     * Formularios (contacto y postulación)
     * Validación en el navegador con mensajes por campo y envío por AJAX
     * con aviso junto al botón. El servidor vuelve a validar todo.
     * ================================================================== */

    // Letras de cualquier idioma (tildes, ñ, ü) y espacios; igual que la regla del servidor.
    var NAME_PATTERN = ( function () {
        try {
            return new RegExp( '^[\\p{L}\\p{M}]+(?:\\s+[\\p{L}\\p{M}]+)*$', 'u' );
        } catch ( e ) {
            return /^[A-Za-zÀ-ÖØ-öø-ÿ]+(?:\s+[A-Za-zÀ-ÖØ-öø-ÿ]+)*$/; // navegadores sin \p{}
        }
    }() );
    var EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

    /** Mensajes comunes: nombre, correo y teléfono. Devuelve '' si el campo es válido. */
    function commonFieldMessage( kind, value, i18n ) {
        if ( 'name' === kind ) {
            if ( ! value ) {
                return i18n.nameRequired;
            }
            return NAME_PATTERN.test( value ) ? '' : i18n.nameInvalid;
        }
        if ( 'email' === kind ) {
            if ( ! value ) {
                return i18n.emailRequired;
            }
            return EMAIL_PATTERN.test( value ) ? '' : i18n.emailInvalid;
        }
        if ( 'phone' === kind && value &&
            ( ! /^[0-9+()\-.\s]+$/.test( value ) || value.replace( /\D/g, '' ).length < 7 ) ) {
            return i18n.phoneInvalid;
        }
        return '';
    }

    function describe( input, id, add ) {
        var ids = ( input.getAttribute( 'aria-describedby' ) || '' ).split( ' ' ).filter( function ( item ) {
            return item && item !== id;
        } );
        if ( add ) {
            ids.push( id );
        }
        if ( ids.length ) {
            input.setAttribute( 'aria-describedby', ids.join( ' ' ) );
        } else {
            input.removeAttribute( 'aria-describedby' );
        }
    }

    /** Muestra (o quita, con message vacío) el error bajo un campo, enlazado con aria. */
    function setFieldError( input, message ) {
        var id = input.id + '-error';
        var error = document.getElementById( id );

        if ( ! message ) {
            input.removeAttribute( 'aria-invalid' );
            describe( input, id, false );
            if ( error ) {
                error.remove();
            }
            return;
        }

        if ( ! error ) {
            error = document.createElement( 'p' );
            error.id = id;
            error.className = 'dimsop-field-error mt-1 mb-0';
            input.closest( '.dimsop-field' ).appendChild( error );
        }
        error.textContent = '';
        var errorIcon = document.createElement( 'i' );
        errorIcon.className = 'fa fa-exclamation-circle';
        errorIcon.setAttribute( 'aria-hidden', 'true' );
        error.appendChild( errorIcon );
        error.appendChild( document.createTextNode( ' ' + message ) );
        input.setAttribute( 'aria-invalid', 'true' );
        describe( input, id, true );
    }

    /**
     * Conecta un formulario: validación, envío por AJAX, estado de carga y aviso.
     *
     * @param {Object}   options
     * @param {Element}  options.form
     * @param {Element}  options.feedback Contenedor aria-live del aviso.
     * @param {Element[]} options.inputs  Campos que se validan, en orden.
     * @param {Function} options.message  ( input ) => mensaje de error o ''.
     * @param {Function} [options.onSuccess] Tras un envío correcto (el formulario ya se vació).
     * @return {Object|null} { check, reset }
     */
    function bindAjaxForm( options ) {
        var form = options.form;
        var feedback = options.feedback;
        if ( ! form || ! feedback || ! settings.ajaxUrl || ! window.fetch || ! window.FormData ) {
            return null;
        }

        var i18n = settings.i18n || {};
        var inputs = options.inputs.filter( Boolean );
        var submit = form.querySelector( '[type="submit"]' );
        var label = submit.querySelector( '.dimsop-button-label' );
        var icon = submit.querySelector( '.fa' );
        var labelText = label.textContent;
        var iconClass = icon.className;
        var hideDelay = 60000;
        var hideTimer = null;
        var attempted = false; // antes del primer envío no se marcan campos vacíos al salir de ellos

        // Los mensajes propios sustituyen a los globos nativos del navegador.
        form.setAttribute( 'novalidate', '' );

        function check( input ) {
            var message = options.message( input ) || '';
            setFieldError( input, message );
            return ! message;
        }

        function firstInvalid() {
            var first = null;
            inputs.forEach( function ( input ) {
                if ( ! check( input ) && ! first ) {
                    first = input;
                }
            } );
            return first;
        }

        inputs.forEach( function ( input ) {
            var live = function () {
                if ( attempted || input.hasAttribute( 'aria-invalid' ) ) {
                    check( input );
                }
            };
            input.addEventListener( 'input', live );
            input.addEventListener( 'change', live );
            input.addEventListener( 'blur', function () {
                if ( attempted || ( 'file' !== input.type && input.value.trim() ) ) {
                    check( input );
                }
            } );
        } );

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
            form.setAttribute( 'aria-busy', loading ? 'true' : 'false' );
            label.textContent = loading ? ( i18n.submitting || labelText ) : labelText;
            icon.className = loading ? 'fa fa-circle-o-notch fa-spin' : iconClass;
        }

        function reset() {
            form.reset();
            attempted = false;
            inputs.forEach( function ( input ) {
                setFieldError( input, '' );
            } );
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
            attempted = true;

            var invalid = firstInvalid();
            if ( invalid ) {
                showAlert( false, i18n.fixErrors );
                invalid.focus();
                return;
            }

            setLoading( true );

            // new FormData incluye action, nonce, honeypot y archivos; el campo "action" tapa a form.action.
            window.fetch( settings.ajaxUrl, { method: 'POST', body: new FormData( form ), credentials: 'same-origin' } )
                .then( function ( response ) {
                    return response.json();
                } )
                .then( function ( result ) {
                    var message = result.data && result.data.message ? result.data.message : i18n.errorGeneric;
                    showAlert( !! result.success, message );
                    if ( result.success ) {
                        reset();
                        if ( options.onSuccess ) {
                            options.onSuccess();
                        }
                    }
                } )
                .catch( function () {
                    showAlert( false, i18n.errorGeneric );
                } )
                .then( function () {
                    setLoading( false );
                } );
        } );

        return { check: check, reset: reset };
    }

    /**
     * Formulario de contacto, con el campo "Otro" que solo aparece al elegir ese servicio.
     */
    function initContactForm() {
        var form = document.querySelector( '#contacto .dimsop-form' );
        if ( ! form ) {
            return;
        }
        var i18n = settings.i18n || {};
        var fields = form.elements;
        var otherWrap = document.getElementById( 'dimsop-f-other-wrap' );
        var other = fields.other_reason;

        function syncOther() {
            if ( ! otherWrap || ! fields.service ) {
                return;
            }
            var show = 'other' === fields.service.value;
            otherWrap.hidden = ! show;
            if ( other ) {
                other.required = show;
                if ( ! show ) {
                    other.value = '';
                    setFieldError( other, '' );
                }
            }
        }

        var bound = bindAjaxForm( {
            form: form,
            feedback: document.getElementById( 'dimsop-contact-feedback' ),
            inputs: [ fields.name, fields.email, fields.phone, other ],
            message: function ( input ) {
                var value = input.value.trim();
                if ( input === other ) {
                    return otherWrap && ! otherWrap.hidden && ! value ? i18n.otherRequired : '';
                }
                return commonFieldMessage( input.name, value, i18n );
            },
            onSuccess: syncOther
        } );

        if ( ! bound ) {
            return;
        }
        if ( fields.service ) {
            fields.service.addEventListener( 'change', function () {
                syncOther();
                if ( 'other' === fields.service.value && other ) {
                    other.focus();
                }
            } );
        }
        syncOther();
    }

    /**
     * Ventana "Forma parte de nuestro equipo": postulación con CV.
     */
    function initCareersForm() {
        var modalEl = document.getElementById( 'dimsop-carreras-modal' );
        var form = document.getElementById( 'dimsop-carreras-form' );
        if ( ! modalEl || ! form ) {
            return;
        }
        var i18n = settings.i18n || {};
        var fields = form.elements;
        var cv = fields.cv;
        var fileWrap = cv ? cv.closest( '.dimsop-file' ) : null;
        var fileName = fileWrap ? fileWrap.querySelector( '.dimsop-file-name' ) : null;
        var maxBytes = 5 * 1024 * 1024;

        function cvMessage() {
            var file = cv.files && cv.files[ 0 ];
            if ( ! file ) {
                return i18n.cvRequired;
            }
            if ( ! /\.(pdf|docx?)$/i.test( file.name ) ) {
                return i18n.invalidFileType;
            }
            if ( file.size > maxBytes ) {
                return i18n.fileTooLarge;
            }
            return file.size ? '' : i18n.fileEmpty;
        }

        function syncFileName() {
            if ( ! fileName ) {
                return;
            }
            var file = cv.files && cv.files[ 0 ];
            fileName.textContent = file ? file.name : fileName.getAttribute( 'data-empty' );
            fileWrap.classList.toggle( 'has-file', !! file );
        }

        var bound = bindAjaxForm( {
            form: form,
            feedback: document.getElementById( 'dimsop-careers-feedback' ),
            inputs: [ fields.name, fields.email, fields.phone, cv ],
            message: function ( input ) {
                return input === cv ? cvMessage() : commonFieldMessage( input.name, input.value.trim(), i18n );
            },
            onSuccess: syncFileName
        } );

        if ( cv ) {
            cv.addEventListener( 'change', syncFileName );
        }

        // Sin JS se vuelve con ?postulacion= o ?postulacion_error=: se reabre la ventana con el aviso.
        if ( bound && window.bootstrap && window.bootstrap.Modal &&
            document.querySelector( '#dimsop-careers-feedback .dimsop-alert' ) ) {
            window.bootstrap.Modal.getOrCreateInstance( modalEl ).show();
        }

        // Al abrir, el foco va al primer campo.
        modalEl.addEventListener( 'shown.bs.modal', function () {
            if ( fields.name ) {
                fields.name.focus();
            }
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

    /**
     * Footer: el mapa de cada sede se crea solo cuando el visitante pulsa "Ver mapa".
     * Así la página no descarga Google Maps ni sus cookies si nadie lo pide.
     */
    function initFooterMaps() {
        var toggles = document.querySelectorAll( '.dimsop-footer-map-toggle' );
        Array.prototype.forEach.call( toggles, function ( toggle ) {
            var wrap = document.getElementById( toggle.getAttribute( 'aria-controls' ) );
            if ( ! wrap || ! wrap.getAttribute( 'data-src' ) ) {
                return;
            }
            toggle.hidden = false;
            toggle.addEventListener( 'click', function () {
                var open = 'true' !== toggle.getAttribute( 'aria-expanded' );
                if ( open && ! wrap.querySelector( 'iframe' ) ) {
                    var frame = document.createElement( 'iframe' );
                    frame.src = wrap.getAttribute( 'data-src' );
                    frame.title = wrap.getAttribute( 'data-title' ) || '';
                    frame.referrerPolicy = 'no-referrer-when-downgrade';
                    frame.setAttribute( 'allowfullscreen', '' );
                    wrap.appendChild( frame );
                }
                wrap.hidden = ! open;
                toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
                // La altura de la página cambió: que ScrollTrigger recalcule.
                if ( window.ScrollTrigger ) {
                    window.ScrollTrigger.refresh();
                }
            } );
        } );
    }

    /**
     * "Volver arriba": desplazamiento suave hasta el inicio, sin dejar #page-top en la URL.
     * Corta la propagación para que el manejador general de anclas (en document) no actúe.
     */
    function initBackToTop() {
        var button = document.querySelector( '.dimsop-back-to-top' );
        if ( ! button ) {
            return;
        }
        button.addEventListener( 'click', function ( e ) {
            e.preventDefault();
            e.stopPropagation();
            var reduce = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
            if ( window.dimsopLenis ) {
                window.dimsopLenis.scrollTo( 0, { immediate: reduce } );
            } else {
                window.scrollTo( { top: 0, behavior: reduce ? 'auto' : 'smooth' } );
            }
            // El foco vuelve al principio para quien navega con teclado.
            var skip = document.querySelector( '.dimsop-logo' );
            if ( skip ) {
                skip.focus( { preventScroll: true } );
            }
        } );
    }

    function init() {
        initFooterMaps();
        initBackToTop();
        initDarkMode();
        initBannerMedia();
        initSpotlight();
        initMarquees();
        initContactForm();
        initCareersForm();
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
