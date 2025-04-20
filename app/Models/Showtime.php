<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Showtime extends Model

{
  
   protected $fillable = [
       'hall_id',
       'movie_id',
       'starts_at',
   ];

   protected $dates = ['starts_at'];

   public function hall()
   {
       return $this->belongsTo(Hall::class);
   }

   public function movie()
   {
       return $this->belongsTo(Movie::class);
   }

   public function reservations()
   {
       return $this->hasMany(Reservation::class);
   }
}

