<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{
 protected $fillable = [
    'movie_id',
    'cinema_id',
    'start_time',
    'price',
 ];
 protected $casts = [
    'start_time' => 'datetime',
 ];
 public function movie()
 {
    return $this->belongsTo(Movie::class);
 }
 public function cinema()
 {
    return $this->belongsTo(Cinema::class);
 }  
}
