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
        document.getElementById('login-form').onsubmit = async function(e) {
            e.preventDefault();
            document.getElementById('login-error').textContent = '';
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            try {
                const res = await fetch('/api/login', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({email, password})
                });
                const data = await res.json();
                if (res.ok && data.token) {
                    localStorage.setItem('token', data.token);
                    window.location.href = '/'; // Redirige al home
                } else {
                    document.getElementById('login-error').textContent = data.error || 'Credenciales inválidas';
                }
            } catch (err) {
                document.getElementById('login-error').textContent = 'Error de conexión';
            }
        };
    </script>
</body>
</html>