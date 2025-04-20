<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'poster_path',
        'genre',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }

    public function getPosterUrlAttribute()
    {
        return asset('storage/' . $this->poster_path);
    }
    public function getPosterPathAttribute($value)
    {
        return asset('storage/' . $value);
    }
    public function getPosterPath()
    {
        return $this->poster_path;
    }
    public function getPosterUrl()
    {
        return $this->poster_url;
    }
}
