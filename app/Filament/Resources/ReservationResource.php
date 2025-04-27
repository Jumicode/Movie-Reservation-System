<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Reservation;
use App\Models\Seat;
use App\Models\Showtime;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\BelongsToManyMultiSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\MultiSelect;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Reservas';
    protected static ?string $navigationGroup = 'Cine';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'email')
                    ->required(),
    
                Select::make('showtime_id')
                    ->relationship('showtime', 'starts_at')
                    ->label('Función')
                    ->reactive()     // 👉 para que al cambiar se recalcule seats
                    ->required(),
    
                MultiSelect::make('seats')
                    ->label('Asientos')
                    ->options(function (callable $get) {
                        $showtimeId = $get('showtime_id');
                        if (! $showtimeId) {
                            return [];
                        }
                        // cargamos sala + sus asientos
                        $showtime = Showtime::with('hall.seats')->find($showtimeId);
                        if (! $showtime) {
                            return [];
                        }
                        // pluck code (ej. "E5") como label, id como value
                        return $showtime
                            ->hall
                            ->seats
                            ->pluck('code', 'id')
                            ->toArray();
                    })
                    ->preload()      // opcional, carga todo en un solo request
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.email')->label('Email del Usuario')->searchable(),
                Tables\Columns\TextColumn::make('showtime.movie.title')->label('Película'),
                Tables\Columns\TextColumn::make('showtime.starts_at')->label('Función')->dateTime(),
                Tables\Columns\TagsColumn::make('seats')
                ->label('Asientos')
                ->getStateUsing(fn ($record) => 
                    $record->seats->map(fn ($seat) => $seat->row . $seat->number)->toArray()
                ),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}