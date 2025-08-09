<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Pago - CinemaHub</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900 text-gray-200">
  <div class="max-w-6xl mx-auto px-4 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Left: Formulario de pago -->
      <div class="lg:col-span-2 bg-gray-800/60 rounded-2xl p-8 shadow-lg border border-gray-700">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-black">Tarjeta de Crédito</h2>
          <a href="javascript:history.back()" class="text-sm text-gray-300 hover:text-yellow-400">← Volver</a>
        </div>

        <form id="payment-form" class="space-y-6" onsubmit="return false;">
          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-sm text-gray-400 mb-2">Cédula</label>
              <select id="doc-type" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-sm">
                <option>V</option>
                <option>E</option>
              </select>
            </div>
            <div class="col-span-2">
              <label class="block text-sm text-gray-400 mb-2">Número</label>
              <input id="doc-number" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-sm" />
            </div>
          </div>

          <div>
            <label class="block text-sm text-gray-400 mb-2">Número de tarjeta</label>
            <input id="card-number" maxlength="19" placeholder="•••• •••• •••• ••••" class="w-full bg-gray-900 border border-gray-700 rounded p-3 text-sm" />
          </div>

          <div class="grid grid-cols-6 gap-4">
            <div class="col-span-2">
              <label class="block text-sm text-gray-400 mb-2">Mes</label>
              <select id="exp-month" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-sm">
                <option value="">MM</option>
                @for($m=1;$m<=12;$m++)
                  <option value="{{ $m }}">{{ str_pad($m,2,'0',STR_PAD_LEFT) }}</option>
                @endfor
              </select>
            </div>
            <div class="col-span-2">
              <label class="block text-sm text-gray-400 mb-2">Año</label>
              <select id="exp-year" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-sm"></select>
            </div>
            <div class="col-span-2">
              <label class="block text-sm text-gray-400 mb-2">CVV</label>
              <input id="cvv" maxlength="4" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-sm" />
            </div>
          </div>

          <div>
            <label class="block text-sm text-gray-400 mb-2">Nombre del titular</label>
            <input id="holder-name" class="w-full bg-gray-900 border border-gray-700 rounded p-3 text-sm" />
          </div>

          <div class="mt-6">
            <button id="pay-btn" type="button" class="w-full py-4 rounded-xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 text-white hover:opacity-95">
              Confirmar pago
            </button>
          </div>
        </form>

        <div id="payment-status" class="mt-6 text-center text-sm text-gray-300"></div>
      </div>

      <!-- Right: Resumen y resultado -->
      <aside class="bg-gray-800/60 rounded-2xl p-6 shadow-lg border border-gray-700">
        <h3 class="text-lg font-semibold mb-4">Resumen de compra</h3>
        <div id="summary" class="space-y-3 text-sm text-gray-300">
          <div><strong>Pelicula:</strong> <span id="sum-movie">-</span></div>
          <div><strong>Sala:</strong> <span id="sum-hall">-</span></div>
          <div><strong>Función:</strong> <span id="sum-starts">-</span></div>
          <div><strong>Asientos:</strong> <span id="sum-seats">-</span></div>
          <div class="pt-3 border-t border-gray-700">
            <div class="flex justify-between"><span>Subtotal</span><span id="sum-sub">$0.00</span></div>
            <div class="flex justify-between"><span>IVA</span><span id="sum-iva">$0.00</span></div>
            <div class="flex justify-between font-black text-xl mt-3"><span>Total</span><span id="sum-total">$0.00</span></div>
          </div>
        </div>

        <div id="ticket-result" class="mt-6 hidden">
          <h4 class="text-white font-bold mb-3">Entrada generada</h4>
          <div id="ticket-info" class="text-sm text-gray-200"></div>
          <div class="mt-4" id="ticket-qr"></div>
          <div class="mt-4">
            <button id="download-ticket" class="px-4 py-2 rounded bg-yellow-400 text-black font-bold">Descargar entrada</button>
            <a id="go-my-res" class="ml-2 text-sm text-gray-200 hover:text-yellow-400" href="/my-reservations">Ver mis reservas</a>
          </div>
        </div>
      </aside>
    </div>
  </div>

<script>
  // --- Datos inyectados desde la sesión (Blade)
  const USER_ID = @json(optional(auth()->user())->id); // null si no hay sesión
  const CSRF = @json(csrf_token());

  // Parámetros de querystring
  const showtimeId = new URLSearchParams(window.location.search).get('showtime_id');
  const seatsParam = new URLSearchParams(window.location.search).get('seats') || '';
  const seatIds = seatsParam ? seatsParam.split(',').map(s => Number(s)).filter(Boolean) : [];

  // helper: rellenar años
  (function fillYears(){
    const yearSelect = document.getElementById('exp-year');
    const now = new Date();
    for (let i = 0; i < 12; i++) {
      const opt = document.createElement('option');
      opt.value = now.getFullYear() + i;
      opt.text = now.getFullYear() + i;
      yearSelect.appendChild(opt);
    }
  })();

  // Precio y resumen calculado localmente
  const pricePerSeat = 2.5;
  const subtotal = (seatIds.length * pricePerSeat);
  const iva = +(subtotal * 0.16).toFixed(2); // ejemplo 16%
  const total = +(subtotal + iva).toFixed(2);

  document.getElementById('sum-seats').textContent = seatIds.length > 0 ? seatIds.join(', ') : '-';
  document.getElementById('sum-sub').textContent = `$${subtotal.toFixed(2)}`;
  document.getElementById('sum-iva').textContent = `$${iva.toFixed(2)}`;
  document.getElementById('sum-total').textContent = `$${total.toFixed(2)}`;

  // Cargar meta del showtime (para mostrar título / sala / hora)
  async function loadShowtimeMeta(){
    if (!showtimeId) return;
    try {
      const res = await fetch(`/api/showtimes/${showtimeId}`);
      if (!res.ok) {
        console.error(await res.text());
        return;
      }
      const json = await res.json();
      // si tu controller show() provee movie y hall, úsalos; si no, ajusta
      const movie = json.movie || null;
      const hall = json.hall || null;
      document.getElementById('sum-movie').textContent = movie ? movie.title : `ID ${json.movie_id || '-'}`;
      document.getElementById('sum-hall').textContent = hall ? hall.name : (json.hall_id || '-');
      if (json.starts_at) {
        const d = new Date(json.starts_at);
        document.getElementById('sum-starts').textContent = d.toLocaleString();
      }
    } catch(e){
      console.error(e);
    }
  }
  loadShowtimeMeta();

  // Helper: mostrar estado y logs mínimos
  function setStatus(msg, isError = false) {
    const el = document.getElementById('payment-status');
    el.textContent = msg;
    el.classList.toggle('text-red-400', isError);
    el.classList.toggle('text-gray-300', !isError);
    console.log('checkout:', msg);
  }

  // Simular pago y crear reserva vía ruta web autenticada con fallback a API
  document.getElementById('pay-btn').addEventListener('click', async function(){
    const payBtn = this;
    payBtn.disabled = true;
    setStatus('Procesando pago...');

    // validación mínima
    const card = document.getElementById('card-number').value.trim();
    const cvv = document.getElementById('cvv').value.trim();
    const holder = document.getElementById('holder-name').value.trim();
    if (!card || !cvv || !holder || seatIds.length === 0 || !showtimeId) {
      setStatus('Completa los datos y selecciona asientos válidos.', true);
      payBtn.disabled = false;
      return;
    }

    try {
      // Simulamos demora del gateway
      await new Promise(r => setTimeout(r, 700));

      // Payload
      const payload = {
        showtime_id: Number(showtimeId),
        seats: seatIds,
        price: total
      };

      // 1) Intentamos crear via ruta web que usa sesión (cookies) + CSRF
      setStatus('Intentando crear reserva con sesión web (si existe)...');
      let res = await fetch('/reservations/web', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF,
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      });

      console.log('Respuesta /reservations/web:', res.status, res.url, 'redirected=', res.redirected);

      // Detectar redirección a login (fetch sigue redirecciones por defecto)
      const redirectedToLogin = res.redirected && (res.url?.includes('/login') || res.url?.includes('login'));
      // También tratamos 302/401/419 como "no sesión / problema"
      if (redirectedToLogin || res.status === 302 || res.status === 401 || res.status === 419) {
        console.warn('No hay sesión válida o CSRF inválido; fallback a API con token.');
        // Intento fallback con token + user_id en localStorage
        const token = localStorage.getItem('token');
        const user_id = localStorage.getItem('user_id');

        if (!token || !user_id) {
          // No hay sesión ni token -> informamos y sugerimos login
          setStatus('No has iniciado sesión (web) ni existe token API. Por favor inicia sesión para continuar.', true);
          // opcional: redirigir: window.location.href = `/login?redirect=${encodeURIComponent(window.location.pathname + window.location.search)}`;
          payBtn.disabled = false;
          return;
        }

        // Llamada a API con token (igual que Postman)
        setStatus('Creando reserva usando token API...');
        res = await fetch('/api/reservations', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + token
          },
          body: JSON.stringify({
            user_id: Number(user_id),
            ...payload
          })
        });

        if (!res.ok) {
          let txt;
          try { txt = await res.json(); } catch (e) { txt = await res.text(); }
          throw new Error((txt && txt.message) ? txt.message : (txt || `Error creando reserva (API). status ${res.status}`));
        }

        const reservationApi = await res.json();
        setStatus('Pago confirmado (API). Entrada generada.');
        showTicketResult(reservationApi);
        return;
      }

      // Si no fue redirección y status OK, parseamos resultado
      if (res.ok) {
        const reservation = await res.json();
        setStatus('Pago confirmado. Entrada generada.');
        showTicketResult(reservation);
        return;
      }

      // Si no OK y no fue redirect, leemos cuerpo y mostramos error
      let errBody;
      try { errBody = await res.json(); } catch (_) { errBody = await res.text(); }
      throw new Error(errBody?.message || errBody || `Respuesta inesperada del servidor (status ${res.status})`);

    } catch (err) {
      console.error('checkout error:', err);
      setStatus('Error: ' + (err.message || err), true);
      payBtn.disabled = false;
    }
  });

  // Mostrar resultado + QR
  function showTicketResult(reservation) {
    document.getElementById('ticket-result').classList.remove('hidden');
    const info = document.getElementById('ticket-info');
    info.innerHTML = `
      <div><strong>ID:</strong> ${reservation.id}</div>
      <div><strong>Precio:</strong> $${Number(reservation.price).toFixed(2)}</div>
      <div><strong>Asientos:</strong> ${reservation.seats ? reservation.seats.map(s => s.code).join(', ') : '-'}</div>
    `;

    const qrContainer = document.getElementById('ticket-qr');
    qrContainer.innerHTML = '';

    const raw = reservation.qr_code || reservation.qr || '';
    if (!raw) {
      qrContainer.innerHTML = '<div class="text-sm text-yellow-400">QR no disponible</div>';
      return;
    }

    const isBase64 = /^[A-Za-z0-9+/=\n\r]+$/.test(raw) && raw.length > 50;
    const img = document.createElement('img');
    img.className = 'w-48 h-48 object-contain rounded';
    if (isBase64) img.src = 'data:image/svg+xml;base64,' + raw;
    else if (raw.trim().startsWith('<')) img.src = 'data:image/svg+xml;utf8,' + encodeURIComponent(raw);
    else img.src = raw;
    qrContainer.appendChild(img);

    // descarga
    document.getElementById('download-ticket').onclick = function(){
      let uri, filename = `ticket-${reservation.id}.svg`;
      if (isBase64) uri = 'data:image/svg+xml;base64,' + raw;
      else if (raw.trim().startsWith('<')) uri = 'data:image/svg+xml;utf8,' + encodeURIComponent(raw);
      else uri = raw;
      const a = document.createElement('a');
      a.href = uri;
      a.download = filename;
      document.body.appendChild(a);
      a.click();
      a.remove();
    };
  }
</script>
</body>
</html>
