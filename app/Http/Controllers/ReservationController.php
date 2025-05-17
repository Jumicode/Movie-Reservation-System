<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $data = $request->validate([
            'user_id'     => 'required|exists:users,id',
            'showtime_id' => 'required|exists:showtimes,id',
            'seats'       => 'required|array|min:1',
            'seats.*'     => 'required|exists:seats,id',
            'price'       => 'required|numeric|min:0',
        ]);

      
        $reservation = Reservation::create([
            'user_id'     => $data['user_id'],
            'showtime_id' => $data['showtime_id'],
            'price'       => $data['price'],
        ]);

 
        $reservation->seats()->sync($data['seats']);

        
        $reservation->load('seats');

       
        $seats = $reservation->seats->map(fn($seat) => [
            'id'   => $seat->id,
            'code' => $seat->code,     // asume getCodeAttribute()
        ]);

        return response()->json([
            'id'           => $reservation->id,
            'user_id'      => $reservation->user_id,
            'showtime_id'  => $reservation->showtime_id,
            'seats'        => $seats,
            'price'        => $reservation->price,
            'created_at'   => $reservation->created_at,
            'updated_at'   => $reservation->updated_at,
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
