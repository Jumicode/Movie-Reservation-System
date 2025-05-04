<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $reservation = Reservation::all();
      return response()->json($reservation);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(Reservation $reservation)
    {
        $reservation = Reservation::find($reservation->id);
        if(!$reservation) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }

        return response()->json([
            'id' => $reservation->id,
            'user_id' => $reservation->user_id,
            'showtime_id' => $reservation->showtime_id,
            'seats' => $reservation->seats,
            'price' => $reservation->price,
            'created_at' => $reservation->created_at,
            'updated_at' => $reservation->updated_at,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reservation $reservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reservation $reservation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        //
    }
}
