<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationSeat extends Model
{
    use HasFactory;

    protected $table = 'reservation_seat';

    protected $fillable = [
        'reservation_id',
        'seat_id',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}
