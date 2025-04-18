<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $movies = Movie::all();


    return response()->json($movies);

    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
       
        $movie = Movie::find($movie->id);
        if (!$movie) {
            return response()->json(['message' => 'Movie not found'], 404);
        }
        return response() -> json([
  
            'id'          => $movie->id,
            'title'       => $movie->title,
            'description' => $movie->description,
            'poster_path' => $movie->poster_path,
            'genre'       => $movie->genre,
         
            ]);
    }

}
