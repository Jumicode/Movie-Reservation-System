<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use Illuminate\Http\Request;

class CinemasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cinemas = Cinema::all();


        return response()->json($cinemas);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cinema $cinemas)
    {
        $cinemas = Cinema::find($cinemas->id);
        if (!$cinemas) {
            return response()->json(['message' => 'Cinemas not found'], 404);
        }
        return response() -> json([
   
            'id'         => $cinemas->id,
            'name'       => $cinemas->name,
            'capacity'   => $cinemas->capacity,
         
            ]);
    }
}
