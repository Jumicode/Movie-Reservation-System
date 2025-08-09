<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Iniciar sesión</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen">
  <form method="POST" action="{{ route('login.attempt') }}" class="bg-gray-800 p-8 rounded-xl shadow-xl w-full max-w-md space-y-6">
    @csrf
    <h2 class="text-3xl font-bold text-yellow-400 text-center mb-6">Iniciar Sesión</h2>

    @if ($errors->any())
      <div class="text-red-400 text-sm">
        {{ $errors->first() }}
      </div>
    @endif

    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 rounded bg-gray-700 text-gray-200" placeholder="Email" required>
    <input type="password" name="password" class="w-full px-4 py-2 rounded bg-gray-700 text-gray-200" placeholder="Contraseña" required>

    <label class="flex items-center text-sm text-gray-400">
      <input type="checkbox" name="remember" class="mr-2"> Recuérdame
    </label>

    <button type="submit" class="w-full bg-yellow-400 text-black py-2 rounded font-bold">Entrar</button>

    <div class="text-center mt-4">
      <a href="#" class="text-yellow-400 hover:underline">¿No tienes cuenta? Regístrate</a>
    </div>
  </form>
</body>
</html>

<script>
document.getElementById('login-form').onsubmit = async function(e) {
    e.preventDefault();
    document.getElementById('login-error').textContent = '';
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;

    try {
        const res = await fetch('/api/login', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
            body: JSON.stringify({email, password})
        });

        const data = await res.json().catch(() => null);

        if (!res.ok) {
            const msg = data?.message || data?.error || 'Credenciales inválidas';
            document.getElementById('login-error').textContent = msg;
            return;
        }

        // Guardar token si viene
        if (data?.token) {
            localStorage.setItem('token', data.token);
        }

        // Guardar user_id si viene en la respuesta
        if (data?.user?.id) {
            localStorage.setItem('user_id', String(data.user.id));
        } else if (data?.id) {
            localStorage.setItem('user_id', String(data.id));
        } else {
            // fallback: intentar /api/me usando el token
            const token = data?.token || localStorage.getItem('token');
            if (token) {
                try {
                    const meRes = await fetch('/api/me', {
                        headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }
                    });
                    if (meRes.ok) {
                        const meJson = await meRes.json();
                        if (meJson?.id) localStorage.setItem('user_id', String(meJson.id));
                        else if (meJson?.user?.id) localStorage.setItem('user_id', String(meJson.user.id));
                    }
                } catch (err) {
                    console.warn('No se pudo obtener /api/me', err);
                }
            }
        }

        // Redirigir al home
        window.location.href = '/';
    } catch (err) {
        console.error(err);
        document.getElementById('login-error').textContent = 'Error de conexión';
    }
};
</script>


</body>
</html>