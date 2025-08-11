<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Iniciar Sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen">
    <form id="login-form" class="bg-gray-800 p-8 rounded-xl shadow-xl w-full max-w-md space-y-6">
        <h2 class="text-3xl font-bold text-yellow-400 text-center mb-6">Iniciar Sesión</h2>
        <input type="email" id="login-email" class="w-full px-4 py-2 rounded bg-gray-700 text-gray-200" placeholder="Email" required>
        <input type="password" id="login-password" class="w-full px-4 py-2 rounded bg-gray-700 text-gray-200" placeholder="Contraseña" required>
        <button type="submit" class="w-full bg-yellow-400 text-black py-2 rounded font-bold">Entrar</button>
        <div id="login-error" class="text-red-400 text-sm"></div>
        <div class="text-center mt-4">
            <a href="{{ route('register') }}" class="text-yellow-400 hover:underline">¿No tienes cuenta? Regístrate</a>
        </div>
    </form>

    <script>
        async function fetchJsonSafe(url, options = {}) {
            const res = await fetch(url, options);
            const ct = res.headers.get('content-type') || '';
            if (ct.includes('application/json')) {
                const body = await res.json();
                if (!res.ok) throw body;
                return body;
            } else {
                const text = await res.text();
                if (!res.ok) throw { message: text || 'Error desconocido' };
                return text;
            }
        }

        document.getElementById('login-form').onsubmit = async function(e) {
            e.preventDefault();
            const errEl = document.getElementById('login-error');
            errEl.textContent = '';

            const email = document.getElementById('login-email').value.trim();
            const password = document.getElementById('login-password').value.trim();
            if (!email || !password) {
                errEl.textContent = 'Completa email y contraseña.';
                return;
            }

            try {
                // petición al endpoint de tu API
                const payload = { email, password };
                const res = await fetch('/api/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });

                // manejo de respuesta
                const data = await (async () => {
                    const ct = res.headers.get('content-type') || '';
                    if (ct.includes('application/json')) return res.json();
                    const txt = await res.text();
                    throw { message: txt || 'Respuesta inesperada del servidor' };
                })();

                // si la petición no fue ok, mostramos error
                if (!res.ok) {
                    // data puede ser {error:..., message:...}
                    errEl.textContent = data.message || data.error || 'Credenciales inválidas';
                    return;
                }

                // Esperamos que la API devuelva { token: '...', user: {...} } idealmente.
                const token = data.token || data.access_token || null;
                let user = data.user || null;

                if (!token) {
                    // si no hay token, asumimos fallo
                    errEl.textContent = data.message || 'No se recibió token del servidor';
                    return;
                }

                // Guardar token en localStorage
                localStorage.setItem('token', token);

                // Si no venía user, pedimos /api/me con el token
                if (!user) {
                    try {
                        const me = await fetchJsonSafe('/api/me', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'Authorization': 'Bearer ' + token
                            }
                        });
                        // algunos /api/me devuelven user directamente, otros envuelven -> intentar normalizar
                        user = (me && me.data) ? me.data : me;
                    } catch (meErr) {
                        // si falla /api/me, no rompas todo: simplemente guardamos token y redirigimos.
                        console.warn('No se pudo obtener /api/me después del login:', meErr);
                    }
                }

                if (user) {
                    try {
                        localStorage.setItem('user', JSON.stringify(user));
                        if (user.id) localStorage.setItem('user_id', user.id);
                    } catch (e) {
                        console.warn('No se pudo serializar user en localStorage', e);
                    }
                }

                // Redirección: si hay param redirect en querystring, ir ahí; si no, a home
                const params = new URLSearchParams(window.location.search);
                const redirect = params.get('redirect') || '/';
                window.location.href = redirect;

            } catch (err) {
                console.error('Login error', err);
                // err puede ser objeto JSON con message
                const msg = (err && (err.message || err.error || (err[0] && err[0].message))) || 'Error de conexión';
                document.getElementById('login-error').textContent = msg;
            }
        };
    </script>
</body>
</html>
