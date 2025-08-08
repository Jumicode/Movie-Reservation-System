<?php

namespace App\Http\Controllers;

use App\Models\Hall;
use Illuminate\Http\Request;

class HallController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $halls = Hall::all();

        return response()->json($halls->map(function ($hall) {
            return [
                'id' => $hall->id,
                'name' => $hall->name,
                'capacity' => $hall->capacity,
                'created_at' => $hall->created_at,
                'updated_at' => $hall->updated_at,
                'seats' => $hall->seats->map(function ($seat) {
                    return [
                        'id' => $seat->id,
                        'hall_id' => $seat->hall_id,
                        'row' => $seat->row,
                        'number' => $seat->number,
                    ];
                })
            ];
        }));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Hall $hall)
    {
$halls = Hall::find($hall->id);
        if (!$halls) {
            return response()->json(['message' => 'Hall not found'], 404);
        }
        return response()->json([
            'id' => $halls->id,
            'name' => $halls->name,
            'capacity' => $halls->capacity,
            'created_at' => $halls->created_at,
            'updated_at' => $halls->updated_at,
            'seats' => $halls->seats->map(function ($seat) {
                return [
                    'id' => $seat->id,
                    'hall_id' => $seat->hall_id,
                    'row' => $seat->row,
                    'number' => $seat->number,
                ];
            }),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hall $hall)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hall $hall)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hall $hall)
    {
        //
    }
}
