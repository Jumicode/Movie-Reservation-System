<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Registrarse</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen">
    <form id="register-form" class="bg-gray-800 p-8 rounded-xl shadow-xl w-full max-w-md space-y-6">
        <h2 class="text-3xl font-bold text-yellow-400 text-center mb-6">Registrarse</h2>
        <input type="text" id="register-name" class="w-full px-4 py-2 rounded bg-gray-700 text-gray-200" placeholder="Nombre" required>
        <input type="email" id="register-email" class="w-full px-4 py-2 rounded bg-gray-700 text-gray-200" placeholder="Email" required>
        <input type="password" id="register-password" class="w-full px-4 py-2 rounded bg-gray-700 text-gray-200" placeholder="Contraseña" required>
        <input type="password" id="register-password-confirmation" class="w-full px-4 py-2 rounded bg-gray-700 text-gray-200" placeholder="Confirmar contraseña" required>
        <button type="submit" class="w-full bg-yellow-400 text-black py-2 rounded font-bold">Registrarse</button>
        <div id="register-error" class="text-red-400 text-sm"></div>
        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="text-yellow-400 hover:underline">¿Ya tienes cuenta? Inicia sesión</a>
        </div>
    </form>
    <script>
        document.getElementById('register-form').onsubmit = async function(e) {
            e.preventDefault();
            document.getElementById('register-error').textContent = '';
            const name = document.getElementById('register-name').value;
            const email = document.getElementById('register-email').value;
            const password = document.getElementById('register-password').value;
            const password_confirmation = document.getElementById('register-password-confirmation').value;
            try {
                const res = await fetch('/api/register', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({name, email, password, password_confirmation})
                });
                const data = await res.json();
                if (res.ok && data.token) {
                    localStorage.setItem('token', data.token);
                    window.location.href = '/'; // Redirige al home
                } else {
                    document.getElementById('register-error').textContent = (data.email || data.password || data.name || data.error || 'Error en el registro');
                }
            } catch (err) {
                document.getElementById('register-error').textContent = 'Error de conexión';
            }
        };
    </script>
</body>
</html>