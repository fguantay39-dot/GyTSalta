// ==========================================
// RELOJ EN TIEMPO REAL
// ==========================================

function updateClock() {
    const clockElement = document.getElementById('clock');

    if (clockElement) {
        const now = new Date();
        clockElement.textContent = now.toLocaleString('es-AR');
    }
}

setInterval(updateClock, 1000);
updateClock();


// ==========================================
// SITIO WEB
// ==========================================

document.addEventListener('DOMContentLoaded', () => {

    // Mensaje de bienvenida
    alert("¡Bienvenido al sitio informativo de Gimnasia y Tiro!");

    // Elementos del menú
    const openMenuBtn = document.getElementById('openMenuBtn');
    const closeMenuBtn = document.getElementById('closeMenuBtn');
    const sidebarMenu = document.getElementById('sidebarMenu');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    const menuLinks = document.querySelectorAll('.menu-link');

    const heroSection = document.getElementById('inicio');

    const contentSections = document.querySelectorAll(
        '.content-section, .noticias-container'
    );


    // ==========================================
    // ABRIR MENÚ
    // ==========================================

    function openMenu() {
        if (sidebarMenu) {
            sidebarMenu.classList.add('active');
        }

        if (sidebarOverlay) {
            sidebarOverlay.classList.add('active');
        }
    }


    // ==========================================
    // CERRAR MENÚ
    // ==========================================

    function closeMenu() {
        if (sidebarMenu) {
            sidebarMenu.classList.remove('active');
        }

        if (sidebarOverlay) {
            sidebarOverlay.classList.remove('active');
        }
    }


    // ==========================================
    // MOSTRAR SECCIÓN
    // ==========================================

    function showSection(sectionId) {

        const selectedSection = document.getElementById(sectionId);

        if (!selectedSection) {
            return;
        }


        // Ocultar todas las secciones
        contentSections.forEach(section => {
            section.classList.remove('active');
        });


        // Mostrar inicio
        if (sectionId === 'inicio') {

            if (heroSection) {
                heroSection.classList.remove('hidden');
            }

        } else {

            if (heroSection) {
                heroSection.classList.add('hidden');
            }

            selectedSection.classList.add('active');
        }


        // Volver arriba
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }


    // ==========================================
    // BOTÓN ABRIR MENÚ
    // ==========================================

    if (openMenuBtn) {
        openMenuBtn.addEventListener('click', openMenu);
    }


    // ==========================================
    // BOTÓN CERRAR MENÚ
    // ==========================================

    if (closeMenuBtn) {
        closeMenuBtn.addEventListener('click', closeMenu);
    }


    // ==========================================
    // CLIC EN FONDO OSCURO
    // ==========================================

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeMenu);
    }


    // ==========================================
    // ENLACES DEL MENÚ
    // ==========================================

    menuLinks.forEach(link => {

        link.addEventListener('click', event => {

            event.preventDefault();

            const href = link.getAttribute('href');

            if (!href) {
                return;
            }

            const sectionId = href.substring(1);

            showSection(sectionId);

            closeMenu();

            history.pushState(
                null,
                '',
                `#${sectionId}`
            );
        });

    });


    // ==========================================
    // LOGO
    // ==========================================

    const brandLogo = document.querySelector('.brand-logo');

    if (brandLogo) {

        brandLogo.addEventListener('click', event => {

            event.preventDefault();

            showSection('inicio');

            history.pushState(
                null,
                '',
                '#inicio'
            );

        });

    }


    // ==========================================
    // BOTÓN PRINCIPAL
    // ==========================================

    const btnPrimary = document.querySelector('.btn-primary');

    if (btnPrimary) {

        btnPrimary.addEventListener('click', event => {

            event.preventDefault();

            openMenu();

        });

    }


    // ==========================================
    // SECCIÓN INICIAL
    // ==========================================

    const initialSection = window.location.hash
        ? window.location.hash.substring(1)
        : 'inicio';

    showSection(initialSection);


    // ==========================================
    // FORMULARIO DE CONTACTO
    // ==========================================

    const formulario = document.getElementById('formularioContacto');

    if (formulario) {

        formulario.addEventListener('submit', function(event) {

            event.preventDefault();


            // Obtener valores
            const nombre = document.getElementById('nombre').value.trim();
            const email = document.getElementById('email').value.trim();
            const mensaje = document.getElementById('mensaje').value.trim();


            // Mensajes
            const errorNombre = document.getElementById('errorNombre');
            const errorEmail = document.getElementById('errorEmail');
            const errorMensaje = document.getElementById('errorMensaje');
            const mensajeExito = document.getElementById('mensajeExito');


            // Limpiar mensajes anteriores
            errorNombre.textContent = '';
            errorEmail.textContent = '';
            errorMensaje.textContent = '';
            mensajeExito.textContent = '';


            let formularioValido = true;


            // Validar nombre
            if (nombre === '') {

                errorNombre.textContent =
                    'Por favor, ingresá tu nombre.';

                formularioValido = false;
            }


            // Validar email
            const formatoEmail =
                /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (email === '') {

                errorEmail.textContent =
                    'Por favor, ingresá tu correo.';

                formularioValido = false;

            } else if (!formatoEmail.test(email)) {

                errorEmail.textContent =
                    'Ingresá un correo electrónico válido.';

                formularioValido = false;
            }


            // Validar mensaje
            if (mensaje === '') {

                errorMensaje.textContent =
                    'Por favor, escribí un mensaje.';

                formularioValido = false;
            }


            // Si todo está correcto
            if (formularioValido) {

                mensajeExito.textContent =
                    '¡Formulario enviado correctamente!';

                formulario.reset();
            }

        });

    }

    const authSection = document.getElementById('acceso');
    const openAuthBtn = document.getElementById('openAuthBtn');

    if (authSection && !authSection.dataset.initialized) {
        authSection.dataset.initialized = 'true';

        const authTabs = authSection.querySelectorAll('[data-auth-mode]');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const loginFeedback = document.getElementById('loginFeedback');
        const registerFeedback = document.getElementById('registerFeedback');
        let currentUser = null;

        async function sendAuthRequest(action, data) {
            let response;

            try {
                response = await fetch(`auth.php?action=${action}`, {
                    method: data ? 'POST' : 'GET',
                    headers: data ? { 'Content-Type': 'application/json' } : {},
                    credentials: 'same-origin',
                    body: data ? JSON.stringify(data) : undefined
                });
            } catch {
                throw new Error(
                    'No se pudo conectar con PHP. Abrí el sitio desde Apache y verificá que XAMPP esté activo.'
                );
            }

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'No se pudo completar la solicitud.');
            }

            return result;
        }

        function setCurrentUser(user) {
            currentUser = user;
            openAuthBtn.textContent = user ? 'Salir' : 'Ingresar';
        }

        function enterSite(user) {
            setCurrentUser(user);
            showSection('inicio');
            history.pushState(null, '', '#inicio');
        }

        authTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const showRegister = tab.dataset.authMode === 'register';

                loginForm.hidden = showRegister;
                registerForm.hidden = !showRegister;
                loginFeedback.textContent = '';
                registerFeedback.textContent = '';

                authTabs.forEach(authTab => {
                    const selected = authTab === tab;
                    authTab.classList.toggle('active', selected);
                    authTab.setAttribute('aria-selected', String(selected));
                });
            });
        });

        loginForm.addEventListener('submit', async event => {
            event.preventDefault();

            try {
                const result = await sendAuthRequest('login', {
                    email: loginForm.elements.email.value.trim(),
                    password: loginForm.elements.password.value
                });
                enterSite(result.user);
                loginForm.reset();
            } catch (error) {
                loginFeedback.textContent = error.message;
            }
        });

        registerForm.addEventListener('submit', async event => {
            event.preventDefault();
            const password = registerForm.elements.password.value;
            const confirmPassword = registerForm.elements.confirmPassword.value;

            if (password !== confirmPassword) {
                registerFeedback.textContent = 'Las contraseñas no coinciden.';
                return;
            }

            try {
                const result = await sendAuthRequest('register', {
                    name: registerForm.elements.name.value.trim(),
                    email: registerForm.elements.email.value.trim(),
                    password
                });
                enterSite(result.user);
                registerForm.reset();
            } catch (error) {
                registerFeedback.textContent = error.message;
            }
        });

        if (openAuthBtn) {
            openAuthBtn.addEventListener('click', async () => {
                if (currentUser) {
                    try {
                        await sendAuthRequest('logout', {});
                        setCurrentUser(null);
                        loginFeedback.textContent = 'Sesión cerrada correctamente.';
                    } catch (error) {
                        loginFeedback.textContent = error.message;
                    }
                    return;
                }

                showSection('acceso');
                history.pushState(null, '', '#acceso');
            });
        }

        sendAuthRequest('me')
            .then(result => setCurrentUser(result.user))
            .catch(() => {});
    }

});
function ampliarImagen(imagen) {
    const visor = document.getElementById("visorImagen");
    const imagenAmpliada = document.getElementById("imagenAmpliada");

    imagenAmpliada.src = imagen.src;
    visor.style.display = "flex";
}

function cerrarImagen() {
    const visor = document.getElementById("visorImagen");
    visor.style.display = "none";
}
const darkModeBtn = document.getElementById("darkModeBtn");

darkModeBtn.addEventListener("click", () => {
    document.body.classList.toggle("dark-mode");

    if (document.body.classList.contains("dark-mode")) {
        darkModeBtn.textContent = "☀️ Modo claro";
    } else {
        darkModeBtn.textContent = "🌙 Modo oscuro";
    }
});