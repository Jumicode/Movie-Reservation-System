<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShowtimeResource\Pages;
use App\Filament\Resources\ShowtimeResource\RelationManagers;
use App\Models\Showtime;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use App\Models\Movie;
use App\Models\Cinema;

class ShowtimeResource extends Resource
{
    protected static ?string $model = Showtime::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('movie_id')
                    ->label('Película')
                    ->relationship('movie', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
    
                Select::make('cinema_id')
                    ->label('Sala de Cine')
                    ->relationship('cinema', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
    
                DateTimePicker::make('start_time')
                    ->label('Fecha y Hora de Inicio')
                    ->required()
                    ->displayFormat('d/m/Y H:i')
                    ->withoutSeconds(),
    
                TextInput::make('price')
                    ->label('Precio')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->step(0.01),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('movie.title')
                    ->label('Película')
                    ->searchable()
                    ->sortable(),
    
                TextColumn::make('cinema.name')
                    ->label('Sala')
                    ->searchable()
                    ->sortable(),
    
                TextColumn::make('start_time')
                    ->label('Inicio')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
    
                TextColumn::make('price')
                    ->label('Precio')
                    ->money('USD'), 
    
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
    
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('upcoming')
                    ->query(fn (Builder $query): Builder => $query->where('start_time', '>', now())),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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
