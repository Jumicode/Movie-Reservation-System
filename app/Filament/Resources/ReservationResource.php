<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Filament\Resources\ReservationResource\RelationManagers;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use App\Models\User;
use App\Models\Showtime;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Email del usuario')
                    ->relationship('user', 'email') 
                    ->searchable()
                    ->preload(),
                    
    
                    Select::make('showtime_id')
                    ->label('Horario de Proyección')
                    ->options(function () {
                        return \App\Models\Showtime::with(['movie', 'cinema'])
                            ->get()
                            ->mapWithKeys(function ($showtime) {
                                return [
                                    $showtime->id => "{$showtime->movie->title} - {$showtime->cinema->name} - {$showtime->start_time->format('d/m/Y H:i')}"
                                ];
                            });
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
    
                TextInput::make('seat_number')
                    ->label('Número de Asiento')
                    ->nullable()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
 
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                    
                 TextColumn::make('user.email') // Añadimos la columna de email del usuario
                    ->label('Correo Electrónico')
                    ->searchable()
                    ->sortable(),
                  

                TextColumn::make('showtime.movie.title')
                    ->label('Película')
                    ->searchable()
                    ->sortable(),
    
                TextColumn::make('showtime.cinema.name')
                    ->label('Sala')
                    ->searchable()
                    ->sortable(),
    
                TextColumn::make('showtime.start_time')
                    ->label('Horario')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
    
                TextColumn::make('seat_number')
                    ->label('Asiento')
                    ->searchable()
                    ->sortable(),
                   
    
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
                Filter::make('user_email')
                ->form([
                    Forms\Components\TextInput::make('email')
                        ->label('Correo Electrónico del Usuario'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when($data['email'], function (Builder $query, $email) {
                        return $query->whereHas('user', function (Builder $userQuery) use ($email) {
                            return $userQuery->where('email', 'like', '%' . $email . '%');
                        });
                    });
                }),
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
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}
