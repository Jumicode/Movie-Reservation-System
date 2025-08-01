<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CinemaHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lucide-react@^0.255.0/dist/umd/icons.min.css" />
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900 text-gray-200 overflow-x-hidden">
  {{-- Animated Background --}}
  <div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div class="absolute -top-40 -right-40 w-80 h-80 bg-yellow-400/10 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-red-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl animate-pulse delay-500"></div>
  </div>

  {{-- Header --}}
  <header class="relative bg-black/80 backdrop-blur-xl border-b border-gray-800/50 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-20">
        <div class="flex items-center space-x-3 group">
          <div class="relative">
            <i data-feather="film" class="h-10 w-10 text-yellow-400 group-hover:rotate-12 transition-transform duration-300"></i>
            <div class="absolute inset-0 bg-yellow-400/20 rounded-full blur-lg group-hover:blur-xl transition-all duration-300"></div>
          </div>
          <div class="text-3xl font-black tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-300">
            CINEMA<span class="text-yellow-400">HUB</span>
          </div>
        </div>
        <nav class="hidden md:flex space-x-8">
          @foreach(['Cartelera','Próximamente','Promociones','Salas','Experiencias'] as $i => $item)
            <a href="#" class="relative text-sm font-semibold transition duration-300 hover:text-yellow-400 {{ $i===0?'text-yellow-400':'text-gray-300' }} group">
              {{ $item }}
              <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-400 group-hover:w-full transition-all duration-300"></span>
            </a>
          @endforeach
        </nav>
        <div class="flex items-center space-x-4">
          <button class="p-2 text-gray-300 hover:text-yellow-400 hover:bg-yellow-400/10 rounded-xl transition duration-200">
            <i data-feather="search" class="h-5 w-5"></i>
          </button>
          <button class="p-2 text-gray-300 hover:text-yellow-400 hover:bg-yellow-400/10 rounded-xl transition duration-200">
            <i data-feather="user" class="h-5 w-5"></i>
          </button>
          <button class="md:hidden p-2 text-gray-300 hover:text-yellow-400 hover:bg-yellow-400/10 rounded-xl transition duration-200">
            <i data-feather="menu" class="h-5 w-5"></i>
          </button>
        </div>
      </div>
    </div>
  </header>

  {{-- Hero --}}
  <section class="relative min-h-[80vh] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 bg-cover bg-center" style="background-image:url('https://images.pexels.com/photos/7991579/pexels-photo-7991579.jpeg')">
      <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-black/30"></div>
    </div>
    <div class="relative z-10 max-w-3xl text-center px-4">
      <div class="flex items-center justify-center space-x-2 mb-6 text-yellow-400 uppercase text-sm font-semibold">
        <i data-feather="zap" class="h-6 w-6"></i>
        Experiencia Premium
      </div>
      <h1 class="text-6xl md:text-7xl font-black mb-6 leading-tight">
        <span class="bg-clip-text text-transparent bg-gradient-to-r from-white via-gray-100 to-gray-300">Vive la</span><br>
        <span class="bg-clip-text text-transparent bg-gradient-to-r from-yellow-400 to-orange-500">Magia del Cine</span>
      </h1>
      <p class="text-xl text-gray-200 mb-10 leading-relaxed">
        Sumérgete en mundos extraordinarios con tecnología vanguardia, sonido envolvente y comodidad. Tu próxima aventura comienza aquí.
      </p>
      <div class="flex flex-col sm:flex-row gap-6 justify-center">
        <a href="#cartelera" class="group bg-gradient-to-r from-yellow-400 to-orange-500 text-black px-10 py-4 rounded-2xl font-bold flex items-center justify-center space-x-3 hover:scale-105 hover:shadow-2xl transition">
          <i data-feather="play" class="h-6 w-6 group-hover:scale-110 transition"></i><span>Explorar Cartelera</span>
        </a>
        <a href="#" class="group border-2 border-white/30 text-white px-10 py-4 rounded-2xl font-bold hover:border-yellow-400 hover:bg-yellow-400/10 transition backdrop-blur-sm">
          Próximos Estrenos
        </a>
      </div>
    </div>
  </section>

  {{-- Movies Section --}}
  <section id="cartelera" class="py-20 bg-gradient-to-b from-transparent to-black/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-16">
        <div class="flex items-center justify-center space-x-2 mb-4 text-yellow-400 uppercase text-sm font-semibold">
          <i data-feather="star" class="h-6 w-6"></i> En Cartelera
        </div>
        <h2 class="text-5xl font-black text-white mb-6">Películas <span class="text-yellow-400">Destacadas</span></h2>
        <p class="text-gray-400 text-lg max-w-3xl mx-auto leading-relaxed">Descubre las mejores películas cautivando audiencias en nuestras salas premium.</p>
      </div>

      {{-- Genre Filter (handled in JS) --}}
      <div class="flex flex-wrap justify-center gap-3 mb-12" id="genre-filters"></div>

      {{-- Content placeholder --}}
      <div id="movies-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8"></div>

      {{-- Loading/Error handled via JS --}}
    </div>
  </section>

  {{-- Footer --}}
  <footer class="bg-gradient-to-t from-black to-gray-900 border-t border-gray-800/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid grid-cols-1 md:grid-cols-4 gap-12 text-gray-400">
      <div class="md:col-span-2">
        <div class="flex items-center space-x-3 mb-6">
          <i data-feather="film" class="h-10 w-10 text-yellow-400"></i>
          <span class="text-2xl font-black text-white">CINEMA<span class="text-yellow-400">HUB</span></span>
        </div>
        <p class="mb-6">Tu destino cinematográfico premium. Experiencias extraordinarias con tecnología de vanguardia.</p>
        <div class="flex space-x-4">
          <a href="#" class="p-3 bg-gray-800/50 hover:bg-yellow-400/50 rounded-xl transition"><i data-feather="facebook" class="h-5 w-5"></i></a>
          <a href="#" class="p-3 bg-gray-800/50 hover:bg-yellow-400/50 rounded-xl transition"><i data-feather="twitter" class="h-5 w-5"></i></a>
          <a href="#" class="p-3 bg-gray-800/50 hover:bg-yellow-400/50 rounded-xl transition"><i data-feather="instagram" class="h-5 w-5"></i></a>
        </div>
      </div>
      <div>
        <h3 class="text-white font-bold text-lg mb-6">Contacto</h3>
        <ul class="space-y-4">
          <li class="flex items-center space-x-3 hover:text-yellow-400 transition"><i data-feather="map-pin"></i><span>Av. Principal 123, Ciudad</span></li>
          <li class="flex items-center space-x-3 hover:text-yellow-400 transition"><i data-feather="phone"></i><span>+1 (555) 123-4567</span></li>
          <li class="flex items-center space-x-3 hover:text-yellow-400 transition"><i data-feather="mail"></i><span>info@cinemahub.com</span></li>
        </ul>
      </div>
      <div>
        <h3 class="text-white font-bold text-lg mb-6">Enlaces</h3>
        <ul class="space-y-3">
          @foreach(['Cartelera','Promociones','Membresías','Eventos','Contacto'] as $link)
            <li><a href="#" class="hover:text-yellow-400 transition">{{ $link }}</a></li>
          @endforeach
        </ul>
      </div>
    </div>
    <div class="text-center text-gray-600 py-4 border-t border-gray-800/50">
      &copy; 2025 CinemaHub. Todos los derechos reservados.
    </div>
  </footer>

  {{-- Scripts --}}
  <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
  <script>
    feather.replace();

    let movies = [];
    let filteredMovies = [];

    async function fetchMovies() {
      try {
        const res = await fetch('/api/movies');
        movies = await res.json();
        const genres = ['all', ...new Set(movies.map(m => m.genre).filter(Boolean))];
        const filtersDiv = document.getElementById('genre-filters');
        genres.forEach(genre => {
          const btn = document.createElement('button');
          btn.textContent = genre === 'all' ? 'Todas' : genre;
          btn.className = 'px-6 py-3 rounded-full font-semibold transition duration-300 ';
          btn.onclick = () => applyFilter(genre);
          filtersDiv.appendChild(btn);
        });
        applyFilter('all');
      } catch (e) {
        console.error(e);
      }
    }

    function applyFilter(genre) {
      filteredMovies = genre === 'all' ? movies : movies.filter(m => m.genre === genre);
      renderMovies();
      document.querySelectorAll('#genre-filters button').forEach(btn => {
        btn.classList.toggle('bg-yellow-400 text-black shadow-lg', btn.textContent.toLowerCase() === (genre === 'all'?'todas':genre));
      });
    }

    function renderMovies() {
      const grid = document.getElementById('movies-grid');
      grid.innerHTML = '';
      filteredMovies.forEach(movie => {
        const div = document.createElement('div');
        div.className = 'group relative bg-gradient-to-b from-gray-800/50 to-gray-900/80 backdrop-blur-sm rounded-3xl overflow-hidden shadow-2xl hover:shadow-yellow-400/10 transition duration-500 hover:scale-105 hover:-translate-y-2';
        div.innerHTML = `
          <div class="relative aspect-[2/3] overflow-hidden">
            <img src="${movie.poster || 'https://via.placeholder.com/400x600'}" alt="${movie.title}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            ${movie.rating ? `<div class="absolute top-4 right-4 bg-gradient-to-r from-yellow-400 to-orange-500 text-black px-3 py-2 rounded-xl flex items-center space-x-1 font-bold shadow-lg"><i data-feather="star" class="h-4 w-4"></i><span>${movie.rating}</span></div>` : ''}
            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-500"><button class="bg-yellow-400/90 text-black p-4 rounded-full shadow-2xl hover:scale-110 transition duration-300"><i data-feather="play" class="h-8 w-8"></i></button></div>
          </div>
          <div class="p-6">
            <h3 class="text-xl font-bold mb-3 line-clamp-2 group-hover:text-yellow-400 transition-colors duration-300">${movie.title}</h3>
            ${movie.genre ? `<div class="inline-block bg-gray-700/50 text-gray-300 px-3 py-1 rounded-full text-sm font-medium mb-3">${movie.genre}</div>` : ''}
            ${movie.synopsis ? `<p class="text-gray-300 text-sm mb-4 line-clamp-3 leading-relaxed">${movie.synopsis}</p>` : ''}
            <div class="flex items-center justify-between text-xs text-gray-400 mb-6">
              ${movie.duration ? `<div class="flex items-center space-x-1"><i data-feather="clock" class="h-4 w-4"></i><span>${movie.duration} min</span></div>` : ''}
              ${movie.release_date ? `<div class="flex items-center space-x-1"><i data-feather="calendar" class="h-4 w-4"></i><span>${new Date(movie.release_date).getFullYear()}</span></div>` : ''}
            </div>
            <button class="w-full bg-gradient-to-r from-yellow-400 to-orange-500 text-black py-3 rounded-xl font-bold transition duration-300 hover:shadow-lg hover:shadow-yellow-400/25 hover:scale-105">Ver Horarios</button>
          </div>
        `;
        grid.appendChild(div);
      });
    }

    document.addEventListener('DOMContentLoaded', fetchMovies);
  </script>
</body>
</html>
