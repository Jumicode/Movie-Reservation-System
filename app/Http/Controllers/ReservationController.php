<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Showtime;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $user = $request->user();
    

        $reservations = Reservation::with('seats')
            ->where('user_id', $user->id)
            ->get();
    
     
        $result = $reservations->map(function ($reservation) {
            return [
                'id'           => $reservation->id,
                'showtime_id'  => $reservation->showtime_id,
                'price'        => $reservation->price,
                'seats'        => $reservation->seats->map(fn($seat) => [
                    'id'   => $seat->id,
                    'code' => $seat->code,   // usa el accessor getCodeAttribute()
                ]),
                'created_at'   => $reservation->created_at,
                'updated_at'   => $reservation->updated_at,
            ];
        });
    
        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'showtime_id' => 'required|integer|exists:showtimes,id',
            'seats' => 'required|array|min:1',
            'seats.*' => 'required|integer|exists:seats,id',
        ]);

        //  Verify that the selected seats belong to the showtime
        $showtime = Showtime::with('hall.seats')->find($request->showtime_id);
        if (!$showtime) {
            return response()->json(['message' => 'Showtime not found'], 404);
        }

        $validSeatIds = $showtime->hall->seats->pluck('id')->toArray();
        foreach ($request->seats as $seatId) {
            if (!in_array($seatId, $validSeatIds)) {
                return response()->json(['message' => 'One or more seats are invalid for the selected showtime'], 400);
            }
        }

        // Check that seats are not already reserved for this showtime
        $alreadyReserved = \DB::table('reservation_seat')
            ->join('reservations', 'reservation_seat.reservation_id', '=', 'reservations.id')
            ->where('reservations.showtime_id', $request->showtime_id)
            ->whereIn('reservation_seat.seat_id', $request->seats)
            ->exists();

        if ($alreadyReserved) {
            return response()->json(['message' => 'One or more seats are already reserved for this showtime'], 409);
        }
        // Calculate the total price based on the seats
        $pricePerSeat = 2.5;
        $totalPrice = count($request->seats) * $pricePerSeat;

        // Create the reservation
        $reservation = Reservation::create([
            'user_id' => $request->user_id,
            'showtime_id' => $request->showtime_id,
            'price' => $totalPrice,
        ]);
        $reservation->seats()->sync($request->seats);
        $reservation->load('seats');

        $seats = $reservation->seats->map(fn($seat) => [
            'id' => $seat->id,
            'code' => $seat->code,
        ]);

        // Contenido del QR (puedes personalizarlo)
        $qrContent = json_encode([
            'reservation_id' => $reservation->id,
            'user_id' => $reservation->user_id,
            'showtime_id' => $reservation->showtime_id,
            'seats' => $seats,
            'price' => $reservation->price,
        ]);

        // Generar QR como imagen PNG en base64 usando GD
        $qrImage = base64_encode(QrCode::format('svg')->size(250)->generate($qrContent));

        return response()->json([
            'id' => $reservation->id,
            'user_id' => $reservation->user_id,
            'showtime_id' => $reservation->showtime_id,
            'seats' => $seats,
            'price' => $reservation->price,
            'qr_code' => $qrImage, // base64 PNG
            'created_at' => $reservation->created_at,
            'updated_at' => $reservation->updated_at,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Reservation $reservation)
    {
       
        if ($request->user()->id !== $reservation->user_id) {
            return response()->json([
                'message' => 'No tienes permiso para ver esta reserva.'
            ], Response::HTTP_FORBIDDEN);
        }

   
        $reservation->load('seats');

       
        return response()->json([
            'id'           => $reservation->id,
            'user_id'      => $reservation->user_id,
            'showtime_id'  => $reservation->showtime_id,
            'seats'        => $reservation->seats->map(fn($seat) => [
                'id'   => $seat->id,
                'code' => $seat->code,        // asume getCodeAttribute()
            ]),
            'price'       => $reservation->price,
            'created_at'  => $reservation->created_at,
            'updated_at'  => $reservation->updated_at,
        ]);
    }

}
