<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageScheduleResource\Pages;
use App\Models\PackageSchedule;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PackageScheduleResource extends Resource
{
    protected static ?string $model = PackageSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('travel_package_id')
                    ->relationship('travelPackage', 'title')
                    ->required()
                    ->label('Paket Wisata'),
                DatePicker::make('departure_date')
                    ->label('Tanggal Keberangkatan')
                    ->required(),
                TextInput::make('price_per_person')
                    ->label('Harga Per Orang')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                TextInput::make('quota')
                    ->label('Total Kuota')
                    ->numeric()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('remaining_quota', $state)),
                TextInput::make('remaining_quota')
                    ->label('Sisa Kuota')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('travelPackage.title')->label('Paket Wisata'),
                TextColumn::make('departure_date')->label('Tgl Keberangkatan')->date('d M Y'),
                TextColumn::make('price_per_person')->label('Harga')->money('IDR'),
                TextColumn::make('remaining_quota')->label('Sisa Kuota'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPackageSchedules::route('/'),
            'create' => Pages\CreatePackageSchedule::route('/create'),
            'edit' => Pages\EditPackageSchedule::route('/{record}/edit'),
        ];
    }
}