<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;

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
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'email')
                    ->required(),
                Forms\Components\Select::make('showtime_id')
                    ->relationship('showtime', 'starts_at')
                    ->required(),
                Forms\Components\MultiSelect::make('seats')
                    ->relationship('seats', 'id')
                    ->label('Asientos')
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
                Tables\Columns\TagsColumn::make('seats.row')->label('Filas'),
                Tables\Columns\TextColumn::make('seats.number')->label('Asientos'),
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