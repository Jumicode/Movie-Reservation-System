<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Mis Reservas - CinemaHub</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-900 text-gray-200">
  <div class="max-w-6xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-black mb-6">Mis Reservas</h1>
    <div id="reservations-list" class="grid grid-cols-1 md:grid-cols-2 gap-6"></div>
    <div id="no-res" class="text-gray-400 mt-8 hidden">No hay reservas.</div>
  </div>

<script>
  async function loadMyReservations(){
    // simula usuario por localStorage
    const userId = localStorage.getItem('user_id') || prompt('Introduce tu user_id para ver reservas (pruebas)');
    if (!userId) return;

    try {
      // intentamos petición con query param user_id (fallback si backend no está protegido)
      let res = await fetch(`/api/reservations?user_id=${userId}`);
      if (!res.ok) {
        // intentar sin query param y filtrar en cliente (si backend devuelve todas)
        res = await fetch('/api/reservations');
      }
      const data = await res.json();
      // si backend devolvió objeto con array, normaliza
      const arr = Array.isArray(data) ? data : (data.data || []);
      const filtered = arr.filter(r => {
        // si backend ya filtró, devolver todo; si no, filtrar por user_id
        return r.user_id == userId || (r.user && (r.user.id == userId || r.user_id == userId));
      });

      const container = document.getElementById('reservations-list');
      container.innerHTML = '';
      if (filtered.length === 0) {
        document.getElementById('no-res').classList.remove('hidden');
        return;
      } else {
        document.getElementById('no-res').classList.add('hidden');
      }

      filtered.forEach(resv => {
        const card = document.createElement('div');
        card.className = 'bg-gray-800/60 p-6 rounded-2xl border border-gray-700 shadow';
        // seats => puede venir como array de objetos con code
        const seats = (resv.seats || []).map(s => s.code || (s.row && s.number ? s.row + s.number : s.id)).join(', ');
        const date = new Date(resv.created_at || resv.updated_at || Date.now());
        const movieTitle = resv.showtime?.movie?.title || resv.movie_title || 'Película';

        card.innerHTML = `
          <div class="flex items-start justify-between">
            <div>
              <div class="text-sm text-gray-400">Reserva ID ${resv.id}</div>
              <h3 class="text-xl font-bold text-white mt-2">${movieTitle}</h3>
              <div class="text-gray-300 text-sm mt-1">Asientos: <strong>${seats || '-'}</strong></div>
              <div class="text-gray-400 text-sm mt-2">Precio: <strong>$${Number(resv.price||0).toFixed(2)}</strong></div>
              <div class="text-gray-500 text-xs mt-2">Creada: ${date.toLocaleString()}</div>
            </div>
            <div class="ml-4 text-right w-36">
              <div id="qr-${resv.id}" class="mb-2"></div>
              <a href="#" data-id="${resv.id}" class="download inline-block text-xs text-yellow-400">Descargar</a>
            </div>
          </div>
        `;
        container.appendChild(card);

        // render QR si existe
        const qrRaw = resv.qr_code || resv.qr || '';
        const qrDiv = document.getElementById(`qr-${resv.id}`);
        if (qrRaw) {
          const isBase64 = /^[A-Za-z0-9+/=\\n\\r]+$/.test(qrRaw) && qrRaw.length > 50;
          const img = document.createElement('img');
          img.className = 'w-28 h-28 object-contain';
          if (isBase64) img.src = 'data:image/svg+xml;base64,' + qrRaw;
          else if (qrRaw.trim().startsWith('<')) img.src = 'data:image/svg+xml;utf8,' + encodeURIComponent(qrRaw);
          else img.src = qrRaw;
          qrDiv.appendChild(img);

          // download handler
          card.querySelector('.download').addEventListener('click', (e) => {
            e.preventDefault();
            let uri;
            if (isBase64) uri = 'data:image/svg+xml;base64,' + qrRaw;
            else if (qrRaw.trim().startsWith('<')) uri = 'data:image/svg+xml;utf8,' + encodeURIComponent(qrRaw);
            else uri = qrRaw;
            const a = document.createElement('a');
            a.href = uri;
            a.download = `ticket-${resv.id}.svg`;
            document.body.appendChild(a);
            a.click();
            a.remove();
          });
        } else {
          qrDiv.innerHTML = '<div class="text-xs text-gray-500">QR no disponible</div>';
        }
      });

    } catch (err) {
      console.error(err);
      alert('Error cargando reservas: ' + (err.message || err));
    }
  }

  document.addEventListener('DOMContentLoaded', loadMyReservations);
</script>
</body>
</html>
