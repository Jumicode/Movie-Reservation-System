<?php
namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use Filament\Resources\Pages\EditRecord;

class EditReservation extends EditRecord
{
    protected static string $resource = ReservationResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $seatIds = $data['seats'] ?? [];
        unset($data['seats']);

        $reservation = parent::handleRecordUpdate($record, $data);

        // reemplazo la selección
        $reservation->seats()->sync($seatIds);

        return $reservation;
    }
}
