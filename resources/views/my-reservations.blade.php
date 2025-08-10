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
async function loadMyReservations() {
  // pedir user_id si no está en localStorage (solo para pruebas)
  const userId = localStorage.getItem('user_id') || prompt('Introduce tu user_id para ver reservas (pruebas)');
  if (!userId) return;

  // token si lo guardaste en login
  const token = localStorage.getItem('token');

  const headers = { 'Accept': 'application/json' };
  if (token) headers['Authorization'] = 'Bearer ' + token;

  try {
    // Intento principal: llamar a la ruta web JSON que creamos (acepta sesión o token).
    // Si tenemos token lo usaremos (credentials omit), si no usaremos cookies (include).
    const res = await fetch('/web/reservations/json', {
      method: 'GET',
      headers,
      credentials: token ? 'omit' : 'include'
    });

    // Manejo de errores: si es 401 -> informar; si no ok -> leer body seguro
    if (!res.ok) {
      const ct = res.headers.get('content-type') || '';
      if (ct.includes('application/json')) {
        const errJson = await res.json();
        throw new Error(errJson.message || JSON.stringify(errJson));
      } else {
        const txt = await res.text();
        throw new Error('Respuesta no-JSON: ' + txt.slice(0, 500));
      }
    }

    // OK
    const data = await res.json();
    const arr = Array.isArray(data) ? data : (data.data || data.reservations || []);
    const filtered = arr.filter(r => Number(r.user_id) === Number(userId) || (r.user && Number(r.user.id) === Number(userId)));
    renderReservations(filtered);

  } catch (err) {
    console.error('loadMyReservations error:', err);

    // Si fallo por Unauthenticated al usar cookies, intentamos la API protegida por token (/api/reservations)
    if (String(err.message).toLowerCase().includes('unauthenticated') || String(err.message).includes('401')) {
      // Si no tenemos token, mostrar mensaje de login; si tenemos token, intentar la API
      if (!token) {
        document.getElementById('reservations-list').innerHTML =
          `<div class="bg-red-800/40 p-4 rounded text-red-200">No has iniciado sesión. Por favor <a href="/login" class="text-yellow-400 underline">inicia sesión</a>.</div>`;
        return;
      }

      // si hay token, intentar endpoint API con Bearer
      try {
        const res2 = await fetch(`/api/reservations?user_id=${encodeURIComponent(userId)}`, {
          method: 'GET',
          headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token },
          credentials: 'omit'
        });
        if (!res2.ok) {
          const txt = await res2.text();
          throw new Error('API error: ' + txt.slice(0, 500));
        }
        const data2 = await res2.json();
        const arr2 = Array.isArray(data2) ? data2 : (data2.data || data2.reservations || []);
        renderReservations(arr2.filter(r => Number(r.user_id) === Number(userId) || (r.user && Number(r.user.id) === Number(userId))));
        return;
      } catch (err2) {
        console.error('Fallback API error:', err2);
        document.getElementById('reservations-list').innerHTML =
          `<div class="bg-red-800/40 p-4 rounded text-red-200">Error cargando reservas: ${escapeHtml(err2.message || err2)}</div>`;
        return;
      }
    }

    // Error genérico: mostrar mensaje
    document.getElementById('reservations-list').innerHTML =
      `<div class="bg-red-800/40 p-4 rounded text-red-200">Error cargando reservas: ${escapeHtml(err.message || err)}</div>`;
  }
}

function renderReservations(filtered) {
  const container = document.getElementById('reservations-list');
  container.innerHTML = '';
  if (!filtered || filtered.length === 0) {
    document.getElementById('no-res').classList.remove('hidden');
    return;
  }
  document.getElementById('no-res').classList.add('hidden');

  filtered.forEach(resv => {
    const card = document.createElement('div');
    card.className = 'bg-gray-800/60 p-6 rounded-2xl border border-gray-700 shadow';
    const seats = (resv.seats || []).map(s => s.code || (s.row && s.number ? s.row + s.number : s.id)).join(', ');
    const date = new Date(resv.created_at || resv.updated_at || Date.now());
    const movieTitle = (resv.showtime && resv.showtime.movie && resv.showtime.movie.title) || resv.movie_title || (resv.movie && resv.movie.title) || 'Película';

    card.innerHTML = `
      <div class="flex items-start justify-between">
        <div>
          <div class="text-sm text-gray-400">Reserva ID ${escapeHtml(String(resv.id))}</div>
          <h3 class="text-xl font-bold text-white mt-2">${escapeHtml(movieTitle)}</h3>
          <div class="text-gray-300 text-sm mt-1">Asientos: <strong>${escapeHtml(seats || '-')}</strong></div>
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

    const qrRaw = resv.qr_code || resv.qr || '';
    const qrDiv = document.getElementById(`qr-${resv.id}`);
    if (qrRaw) {
      const maybeBase64 = /^[A-Za-z0-9+/=\n\r]+$/.test(qrRaw) && qrRaw.length > 50;
      const img = document.createElement('img');
      img.className = 'w-28 h-28 object-contain';
      if (maybeBase64) img.src = 'data:image/svg+xml;base64,' + qrRaw;
      else if (qrRaw.trim().startsWith('<')) img.src = 'data:image/svg+xml;utf8,' + encodeURIComponent(qrRaw);
      else img.src = qrRaw;
      qrDiv.appendChild(img);

      card.querySelector('.download').addEventListener('click', (e) => {
        e.preventDefault();
        let uri;
        if (maybeBase64) uri = 'data:image/svg+xml;base64,' + qrRaw;
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
      card.querySelector('.download').addEventListener('click', (e) => { e.preventDefault(); alert('QR no disponible para descargar'); });
    }
  });
}

function escapeHtml(unsafe) {
  return String(unsafe)
    .replaceAll('&','&amp;')
    .replaceAll('<','&lt;')
    .replaceAll('>','&gt;')
    .replaceAll('"','&quot;')
    .replaceAll("'",'&#039;');
}

document.addEventListener('DOMContentLoaded', loadMyReservations);
</script>


</body>
</html>
