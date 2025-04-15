<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Reservation extends Model
{

        
protected $fillable = [
'user_id',
'showtime_id',
'seat_number',
  ];

  public function user()
  {
      return $this->belongsTo(User::class);
  }

  public function showtime()
  {
      return $this->belongsTo(Showtime::class);
  }
}
