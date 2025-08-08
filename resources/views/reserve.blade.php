<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Selecciona tus asientos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900 text-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Mapa de asientos -->
            <div class="lg:col-span-2">
                <div class="bg-gradient-to-b from-gray-800/50 to-gray-900/80 rounded-3xl p-8 shadow-2xl">
                    <div class="flex items-center justify-between mb-8">
                        <a href="/showtimes?movie_id={{ request('movie_id') }}" class="flex items-center space-x-2 text-gray-300 hover:text-yellow-400 transition-colors duration-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                            <span class="font-medium">Regresar</span>
                        </a>
                        <div class="flex items-center space-x-2 text-yellow-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 17l7-7 7 7" /></svg>
                            <span class="font-semibold" id="available-count"></span>
                        </div>
                    </div>
                    <h2 class="text-3xl font-black text-white mb-8">Selecciona tus <span class="text-yellow-400">asientos</span></h2>

                    <!-- Leyenda -->
                    <div class="flex flex-wrap justify-center gap-6 mb-8">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-gray-600 rounded-lg"></div>
                            <span class="text-gray-300 text-sm font-medium">Ocupados</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-green-500 rounded-lg"></div>
                            <span class="text-gray-300 text-sm font-medium">Disponible</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-lg"></div>
                            <span class="text-gray-300 text-sm font-medium">Seleccionados</span>
                        </div>
                    </div>

                    <!-- Pantalla -->
                    <div class="mb-12">
                        <div class="bg-gradient-to-r from-transparent via-gray-300 to-transparent h-1 rounded-full mb-4"></div>
                        <p class="text-center text-gray-400 text-sm font-medium">PANTALLA</p>
                    </div>

                    <!-- Mapa de asientos -->
                    <div id="seat-map" class="space-y-3"></div>
                    <!-- Números de asientos -->
                    <div class="flex items-center justify-center mt-6 space-x-2">
                        <div class="flex space-x-1 text-xs text-gray-500" id="seat-numbers-left"></div>
                        <div class="w-8"></div>
                        <div class="flex space-x-1 text-xs text-gray-500" id="seat-numbers-right"></div>
                    </div>
                </div>
            </div>

            <!-- Info de la película y resumen -->
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-b from-gray-800/50 to-gray-900/80 rounded-3xl overflow-hidden shadow-2xl sticky top-24">
                    <div class="relative aspect-[3/2] overflow-hidden">
                        <img id="movie-poster" src="" alt="" class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                    </div>
                    <div class="p-6">
                        <h3 id="movie-title" class="text-2xl font-black text-white mb-6"></h3>
                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 font-medium">Fecha</span>
                                <span class="text-white font-semibold" id="showtime-date"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 font-medium">Hora</span>
                                <span class="text-white font-semibold" id="showtime-time"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 font-medium">Sala</span>
                                <span class="text-white font-semibold" id="showtime-hall"></span>
                            </div>
                        </div>
                        <div id="selected-seats-summary" class="bg-gray-800/50 rounded-2xl p-4 mb-6 hidden">
                            <h4 class="text-white font-bold mb-3">Asientos Seleccionados</h4>
                            <div id="selected-seats-list" class="flex flex-wrap gap-2"></div>
                            <div class="mt-4 pt-4 border-t border-gray-700">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-400">Total (<span id="selected-count"></span> asientos)</span>
                                    <span class="text-white font-bold text-lg" id="total-price"></span>
                                </div>
                            </div>
                        </div>
                        <button id="continue-btn" disabled class="w-full py-4 rounded-2xl font-bold text-lg bg-gray-700 text-gray-400 cursor-not-allowed transition-all duration-300">
                            Selecciona tus asientos
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- Variables globales
        const showtimeId = new URLSearchParams(window.location.search).get('showtime_id');
        let seats = [];
        let selectedSeats = [];
        let showtime = null;
        let movie = null;

        // --- Helpers
        function posterUrlFromPath(path) {
            if (!path) return '/images/placeholder-poster.png';
            if (/^https?:\/\//i.test(path)) return path;
            if (path.startsWith('/storage')) return path;
            if (path.startsWith('storage/')) return '/' + path;
            const httpIndex = path.indexOf('http');
            if (httpIndex !== -1) return path.slice(httpIndex);
            return `/storage/${path}`;
        }

        // --- Renderizado y selección de asientos (expuestos en window)
        window.toggleSeatSelection = function(seatId) {
            if (selectedSeats.includes(seatId)) selectedSeats = selectedSeats.filter(id => id !== seatId);
            else selectedSeats.push(seatId);
            window.renderSeatMap();
        }

        window.renderSelectedSeatsSummary = function() {
            const summary = document.getElementById('selected-seats-summary');
            const list = document.getElementById('selected-seats-list');
            const count = document.getElementById('selected-count');
            const total = document.getElementById('total-price');
            const continueBtn = document.getElementById('continue-btn');

            if (!summary || !list || !count || !total || !continueBtn) return;

            if (selectedSeats.length > 0) {
                summary.classList.remove('hidden');
                list.innerHTML = '';
                selectedSeats.forEach(seatId => {
                    const seat = seats.find(s => s.id === seatId);
                    if (seat) {
                        const span = document.createElement('span');
                        span.className = "bg-gradient-to-r from-yellow-400 to-orange-500 text-black px-3 py-1 rounded-lg font-bold text-sm";
                        span.textContent = `${seat.row}${seat.number}`;
                        list.appendChild(span);
                    }
                });
                count.textContent = selectedSeats.length;
                total.textContent = `$${(selectedSeats.length * 2.5).toFixed(2)}`;
                continueBtn.disabled = false;
                continueBtn.className = "w-full py-4 rounded-2xl font-bold text-lg bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-black hover:scale-105 hover:shadow-lg hover:shadow-yellow-400/25 transition-all duration-300";
                continueBtn.textContent = 'Continuar con la compra';
            } else {
                summary.classList.add('hidden');
                continueBtn.disabled = true;
                continueBtn.className = "w-full py-4 rounded-2xl font-bold text-lg bg-gray-700 text-gray-400 cursor-not-allowed transition-all duration-300";
                continueBtn.textContent = 'Selecciona tus asientos';
            }
        }

        window.renderSeatMap = function() {
            const seatMap = document.getElementById('seat-map');
            if (!seatMap) return;
            seatMap.innerHTML = '';

            const rows = Array.from(new Set(seats.map(s => s.row))).sort((a,b) => {
                if (!isNaN(a) && !isNaN(b)) return Number(a) - Number(b);
                return String(a).localeCompare(String(b));
            });

            let availableCount = 0;

            rows.forEach(row => {
                const rowSeats = seats.filter(s => s.row === row).sort((a,b) => Number(a.number) - Number(b.number));
                const rowDiv = document.createElement('div');
                rowDiv.className = "flex items-center justify-center space-x-2";

                const leftLabel = document.createElement('div');
                leftLabel.className = "w-6 text-center text-gray-400 font-bold text-sm";
                leftLabel.textContent = row;
                rowDiv.appendChild(leftLabel);

                const seatsDiv = document.createElement('div');
                seatsDiv.className = "flex space-x-1";

                rowSeats.forEach(seat => {
                    const btn = document.createElement('button');
                    btn.textContent = seat.number;
                    btn.type = 'button';
                    btn.className = "w-8 h-8 rounded-lg text-xs font-bold transition-all duration-300";

                    if (seat.status === 'occupied') {
                        btn.classList.add('bg-gray-600', 'text-gray-400', 'cursor-not-allowed');
                        btn.disabled = true;
                    } else if (selectedSeats.includes(seat.id)) {
                        btn.classList.add('bg-gradient-to-r', 'from-yellow-400', 'to-orange-500', 'text-black', 'shadow-lg', 'shadow-yellow-400/25', 'scale-110');
                    } else {
                        btn.classList.add('bg-green-500', 'hover:bg-green-400', 'text-white', 'shadow-lg', 'hover:shadow-green-400/25');
                        btn.onclick = () => window.toggleSeatSelection(seat.id);
                        availableCount++;
                    }

                    seatsDiv.appendChild(btn);
                });

                rowDiv.appendChild(seatsDiv);

                const rightLabel = document.createElement('div');
                rightLabel.className = "w-6 text-center text-gray-400 font-bold text-sm";
                rightLabel.textContent = row;
                rowDiv.appendChild(rightLabel);

                seatMap.appendChild(rowDiv);
            });

            const availableElem = document.getElementById('available-count');
            if (availableElem) availableElem.textContent = `${availableCount} asientos disponibles`;

            window.renderSelectedSeatsSummary();
        }

        // --- Acción continuar: crear reserva via API
        document.getElementById('continue-btn').addEventListener('click', async function() {
            // simple chequeo de autenticación simulated (ajusta a tu sistema real)
            const userId = localStorage.getItem('user_id');
            if (!userId) {
                if (confirm('Necesitas iniciar sesión para reservar. ¿Ir a login?')) window.location.href = '/login';
                return;
            }

            if (selectedSeats.length === 0) return alert('Selecciona al menos un asiento');

            const payload = {
                user_id: Number(userId),
                showtime_id: Number(showtimeId),
                seats: selectedSeats,
                price: Number((selectedSeats.length * 2.5).toFixed(2)),
            };

            try {
                const res = await fetch('/api/reservations', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });

                if (!res.ok) {
                    const txt = await res.text();
                    throw new Error(txt || 'Error al crear reserva');
                }

                const json = await res.json();
                alert('Reserva creada: ID ' + json.id + '\nCódigo QR: ' + (json.qr_code_url || 'no disponible'));
                // redirigir a /reservations o refrescar
                window.location.href = '/';
            } catch (err) {
                console.error('Reserva error:', err);
                alert('No fue posible crear la reserva: ' + (err.message || err));
            }
        });

        // --- fetchData: obtiene showtime, movie, seats y asientos reservados
        async function fetchData() {
            try {
                // 1) showtime
                const showtimeRes = await fetch(`/api/showtimes/${showtimeId}`);
                if (!showtimeRes.ok) {
                    const txt = await showtimeRes.text();
                    throw new Error('Error al cargar la función: ' + txt);
                }
                const showtimeJson = await showtimeRes.json();
                showtime = {
                    id: showtimeJson.id,
                    hall_id: showtimeJson.hall_id,
                    movie_id: showtimeJson.movie_id,
                    starts_at: showtimeJson.starts_at,
                    hall: showtimeJson.hall || null,
                    movie: showtimeJson.movie || null,
                };

                // 2) movie fallback
                if (!showtime.movie && showtime.movie_id) {
                    const movieRes = await fetch(`/api/movies/${showtime.movie_id}`);
                    if (movieRes.ok) {
                        const movieJson = await movieRes.json();
                        showtime.movie = {
                            id: movieJson.id,
                            title: movieJson.title,
                            poster_path: movieJson.poster_path,
                            description: movieJson.description ?? null,
                            genre: movieJson.genre ?? null,
                        };
                    } else {
                        console.warn('No se pudo obtener movie por separado', await movieRes.text());
                        showtime.movie = null;
                    }
                }

                // 3) seats source
                if (showtime.hall && Array.isArray(showtime.hall.seats) && showtime.hall.seats.length > 0) {
                    seats = showtime.hall.seats;
                } else {
                    const hallRes = await fetch(`/api/halls/${showtime.hall_id}`);
                    if (!hallRes.ok) {
                        const txt = await hallRes.text();
                        throw new Error('Error al cargar la sala: ' + txt);
                    }
                    const hallJson = await hallRes.json();
                    seats = hallJson.seats || [];
                }

                // 4) reserved seats
                const reservedRes = await fetch(`/api/showtimes/${showtimeId}/reserved-seats`);
                if (!reservedRes.ok) {
                    const txt = await reservedRes.text();
                    throw new Error('Error al cargar asientos reservados: ' + txt);
                }
                const reservedSeatIds = await reservedRes.json();

                // 5) marcar estado
                seats = seats.map(seat => ({
                    ...seat,
                    status: Array.isArray(reservedSeatIds) && reservedSeatIds.includes(seat.id) ? 'occupied' : 'available'
                }));

                movie = showtime.movie || null;

                window.renderSeatMap();
                window.renderMovieInfo();
            } catch (err) {
                console.error('fetchData error:', err);
                const seatMap = document.getElementById('seat-map');
                if (seatMap) {
                    seatMap.innerHTML = `<div class="p-6 text-center text-red-400">Error al cargar datos: ${String(err.message || err)}</div>`;
                } else {
                    alert('Error al cargar datos: ' + (err.message || err));
                }
            }
        }

        // --- renderMovieInfo guardado y seguro
        window.renderMovieInfo = function() {
            if (!movie) {
                document.getElementById('movie-poster').src = '/images/placeholder-poster.png';
                document.getElementById('movie-title').textContent = 'Película desconocida';
                if (showtime && showtime.starts_at) {
                    const d = new Date(showtime.starts_at);
                    document.getElementById('showtime-date').textContent = d.toLocaleDateString('es-ES');
                    document.getElementById('showtime-time').textContent = d.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                }
                document.getElementById('showtime-hall').textContent = showtime ? showtime.hall_id : '';
                return;
            }

            const poster = posterUrlFromPath(movie.poster_path || movie.poster || '');
            document.getElementById('movie-poster').src = poster;
            document.getElementById('movie-title').textContent = movie.title || 'Sin título';
            if (showtime && showtime.starts_at) {
                const dateObj = new Date(showtime.starts_at);
                document.getElementById('showtime-date').textContent = dateObj.toLocaleDateString('es-ES');
                document.getElementById('showtime-time').textContent = dateObj.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
            }
            document.getElementById('showtime-hall').textContent = showtime ? showtime.hall_id : '';
        }

        document.addEventListener('DOMContentLoaded', fetchData);
    </script>
</body>
</html>
