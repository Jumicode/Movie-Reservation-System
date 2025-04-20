<?php
namespace App\Filament\Resources;

use App\Filament\Resources\SeatResource\Pages;
use App\Models\Seat;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;

class SeatResource extends Resource
{
    protected static ?string $model = Seat::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Asientos';
    protected static ?string $navigationGroup = 'Cine';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('hall_id')
                    ->relationship('hall', 'name')
                    ->required(),
                Forms\Components\TextInput::make('row')->required(),
                Forms\Components\TextInput::make('number')->numeric()->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hall.name')->label('Sala')->searchable(),
                Tables\Columns\TextColumn::make('row'),
                Tables\Columns\TextColumn::make('number'),
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
            'index' => Pages\ListSeats::route('/'),
            'create' => Pages\CreateSeat::route('/create'),
            'edit' => Pages\EditSeat::route('/{record}/edit'),
        ];
    }
}
