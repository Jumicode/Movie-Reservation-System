<?php

namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReservation extends CreateRecord
{
    protected static string $resource = ReservationResource::class;

    /**
     * Aquí interceptamos el array 'seats' antes de crear la reserva,
     * lo quitamos de $data, llamamos al padre y luego hacemos sync().
     */
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Extraemos y eliminamos seats del payload
        $seatIds = $data['seats'] ?? [];
        unset($data['seats']);

        // Creamos la reserva (usuario_id + showtime_id)
        $reservation = parent::handleRecordCreation($data);

        // Sincronizamos la relación many-to-many
        $reservation->seats()->sync($seatIds);

        return $reservation;
    }
}
