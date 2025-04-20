<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShowtimeResource\Pages;
use App\Models\Showtime;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;

class ShowtimeResource extends Resource
{
    protected static ?string $model = Showtime::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Funciones';
    protected static ?string $navigationGroup = 'Cine';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('hall_id')
                    ->relationship('hall', 'name')->required(),
                Forms\Components\Select::make('movie_id')
                    ->relationship('movie', 'title')->required(),
                Forms\Components\DateTimePicker::make('starts_at')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hall.name')->label('Sala'),
                Tables\Columns\TextColumn::make('movie.title')->label('Película')->searchable(),
                Tables\Columns\TextColumn::make('starts_at')->dateTime()->sortable(),
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
            'index' => Pages\ListShowtimes::route('/'),
            'create' => Pages\CreateShowtime::route('/create'),
            'edit' => Pages\EditShowtime::route('/{record}/edit'),
        ];
    }
}
