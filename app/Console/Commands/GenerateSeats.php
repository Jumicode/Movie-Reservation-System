<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Hall;
use App\Models\Seat;

class GenerateSeats extends Command
{
    protected $signature = 'seats:generate
                            {hall_id : ID de la sala}
                            {rows : Filas, ejemplo A-J}
                            {max : Número máximo por fila}';

    protected $description = 'Genera asientos fila+numero para una sala';

    public function handle()
    {
        $hall = Hall::findOrFail($this->argument('hall_id'));

        [$start, $end] = explode('-', strtoupper($this->argument('rows')));
        $rows = range($start, $end);
        $max  = (int) $this->argument('max');

        foreach ($rows as $row) {
            for ($i = 1; $i <= $max; $i++) {
                Seat::firstOrCreate([
                    'hall_id' => $hall->id,
                    'row'     => $row,
                    'number'  => $i,
                ]);
            }
        }

        $this->info("Asientos con 1–$max generados para sala {$hall->name}.");
    }
}

