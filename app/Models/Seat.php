<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $fillable = [
        'hall_id',
        'row',
        'number'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
   

   public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function showtimes()
    {
        return $this->belongsToMany(Reservation::class, 'reservation_seat')
        ->withTimestamps();
    }

    public function getCodeAttribute()
    {
        return "{$this->row}{$this->number}";
    }
    
}
