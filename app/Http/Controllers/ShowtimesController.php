<?php


namespace App\Http\Controllers;

use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShowtimesController extends Controller
{
    public function index()
    {
        return response()->json(Showtime::all());
    }

    // show con relaciones (hall.seats + movie)
    public function show(Showtime $showtime)
    {
        $showtime->load(['hall.seats', 'movie']);

        return response()->json([
            'id' => $showtime->id,
            'hall_id' => $showtime->hall_id,
            'movie_id' => $showtime->movie_id,
            'starts_at' => $showtime->starts_at,
            'hall' => [
                'id' => $showtime->hall->id,
                'name' => $showtime->hall->name,
                'seats' => $showtime->hall->seats->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'row' => $s->row,
                        'number' => $s->number,
                        'code' => $s->row . $s->number,
                    ];
                })->values(),
            ],
            'movie' => $showtime->movie ? [
                'id' => $showtime->movie->id,
                'title' => $showtime->movie->title,
                'poster_path' => $showtime->movie->poster_path,
                'description' => $showtime->movie->description ?? null,
                'genre' => $showtime->movie->genre ?? null,
            ] : null,
        ]);
    }

    // endpoint público -> devuelve array de seat_id ya reservados para esta función
    public function reservedSeats(Showtime $showtime)
    {
        $seatIds = DB::table('reservation_seat')
            ->join('reservations', 'reservation_seat.reservation_id', '=', 'reservations.id')
            ->where('reservations.showtime_id', $showtime->id)
            ->pluck('reservation_seat.seat_id')
            ->unique()
            ->values();

        return response()->json($seatIds);
    }
}

