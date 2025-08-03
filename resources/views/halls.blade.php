<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CinemaHub - Salas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lucide-react@^0.255.0/dist/umd/icons.min.css" />
</head>
<body class="min-h-screen bg-gray-900 text-gray-200 overflow-x-hidden">
  <!-- Header -->
  <header class="bg-black/80 backdrop-blur-xl border-b border-gray-800/50 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <div class="flex items-center space-x-3">
          <i data-feather="film" class="h-8 w-8 text-yellow-400"></i>
          <span class="text-2xl font-black">Cinema<span class="text-yellow-400">Hub</span></span>
        </div>
        <nav class="hidden md:flex space-x-6">
          <a href="/" class="hover:text-yellow-400 transition">Cartelera</a>
          <a href="/halls" class="text-yellow-400 font-semibold">Salas</a>
          <a href="/reservations" class="hover:text-yellow-400 transition">Reservas</a>
        </nav>
      </div>
    </div>
  </header>

  <!-- Hero -->
  <section class="bg-gradient-to-b from-gray-800 to-gray-900 text-center py-16">
    <h1 class="text-5xl font-black mb-4">Nuestras Salas</h1>
    <p class="text-gray-400">Descubre la capacidad y características de cada una de nuestras salas</p>
  </section>

  <!-- Halls Grid -->
  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div id="halls-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8"></div>
  </main>

  <!-- Footer -->
  <footer class="bg-black/80 border-t border-gray-800/50">
    <div class="max-w-7xl mx-auto px-4 py-8 text-center text-gray-500">
      &copy; 2025 CinemaHub. Todos los derechos reservados.
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
  <script>
    feather.replace();

    async function fetchHalls() {
      try {
        const res = await fetch('/api/halls');
        const halls = await res.json();
        const grid = document.getElementById('halls-grid');
        halls.forEach(hall => {
          const card = document.createElement('div');
          card.className = 'bg-gray-800 rounded-2xl shadow-lg p-6 hover:shadow-yellow-400/20 transition duration-300';
          card.innerHTML = `
           <img src="https://www.lamoncloa.gob.es/serviciosdeprensa/notasprensa/cultura/PublishingImages/Recursos/151223-sala-de-cine.jpg?RenditionID=33"
       alt="${hall.name}"
       class="w-full h-40 object-cover rounded-lg mb-4" />
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-xl font-bold text-white">${hall.name}</h2>
              <i data-feather="map-pin" class="h-6 w-6 text-yellow-400"></i>
            </div>
            <p class="text-gray-400 mb-2"><strong>Capacidad:</strong> ${hall.capacity} personas</p>
           
          `;
          grid.appendChild(card);
        });
      } catch (error) {
        console.error('Error cargando salas:', error);
      }
    }

    document.addEventListener('DOMContentLoaded', fetchHalls);
  </script>
</body>
</html>
