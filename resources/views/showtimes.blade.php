<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Horarios de la Película</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900 text-gray-200">
    <div class="max-w-2xl mx-auto py-16 px-4">
        <h1 class="text-4xl font-black text-yellow-400 mb-8 text-center">Horarios de la Película</h1>
        <div id="showtimes-list" class="space-y-6"></div>
        <div id="no-showtimes" class="text-center text-gray-400 text-xl mt-12 hidden">
            No hay horarios disponibles para esta película.
        </div>
        <div class="mt-8 text-center">
            <a href="/" class="text-yellow-400 hover:underline">Volver al inicio</a>
        </div>
    </div>
    <script>
        
        function getMovieId() {
            const params = new URLSearchParams(window.location.search);
            return params.get('movie_id');
        }

        async function fetchShowtimes() {
            const movieId = getMovieId();
            const res = await fetch('/api/showtimes');
            const showtimes = await res.json();
            const filtered = showtimes.filter(s => s.movie_id == movieId);

            const list = document.getElementById('showtimes-list');
            list.innerHTML = '';

            if (filtered.length === 0) {
                document.getElementById('no-showtimes').classList.remove('hidden');
                return;
            }

            document.getElementById('no-showtimes').classList.add('hidden');
            filtered.forEach(s => {
                const date = new Date(s.starts_at);
                const formatted = date.toLocaleString('es-ES', {
                    day: '2-digit', month: '2-digit', year: 'numeric',
                    hour: '2-digit', minute: '2-digit'
                });
                const div = document.createElement('div');
                div.className = "bg-gray-800 rounded-xl p-6 flex items-center justify-between shadow-lg";
                div.innerHTML = `
                    <div>
                        <div class="text-lg font-bold text-yellow-400">Sala #${s.hall_id}</div>
                        <div class="text-gray-300">Inicio: ${formatted}</div>
                    </div>
                    <a href="#" class="bg-yellow-400 text-black px-6 py-2 rounded-xl font-bold hover:bg-yellow-500 transition">Reservar</a>
                `;
                list.appendChild(div);
            });
        }

        document.addEventListener('DOMContentLoaded', fetchShowtimes);
    </script>
</body>
</html>