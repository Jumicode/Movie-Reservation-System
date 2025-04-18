<?php

namespace App\Http\Controllers;

use App\Models\Showtime;
use Illuminate\Http\Request;

class ShowtimesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $showtimes = Showtime::all();


        return response()->json($showtimes);
    }

    /**
     * Display the specified resource.
     */
    public function show(Showtime $showtimes)
    {
        $showtimes = Showtime::find($showtimes->id);
        if(!$showtimes){
            return response()->json(['message' => 'Showtimes not found'], 404);
        }
        return response()->json([
            'id'         => $showtimes->id,
            'movie_id'   => $showtimes->movie_id,
            'cinema_id'  => $showtimes->cinema_id,
            'start_time' => $showtimes->start_time,
            'end_time'   => $showtimes->end_time,
        ]);
    }


}
