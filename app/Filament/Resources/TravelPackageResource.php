<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TravelPackageResource\Pages;
use App\Models\TravelPackage;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class TravelPackageResource extends Resource
{
    protected static ?string $model = TravelPackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->label('Kategori'),
                TextInput::make('title')
                    ->label('Judul Paket')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->required()
                    ->readOnly(),
                TextInput::make('location')
                    ->label('Lokasi')
                    ->required(),
                TextInput::make('duration')
                    ->label('Durasi (misal: 3D2N)')
                    ->required(),
                Select::make('type')
                    ->label('Tipe Trip')
                    ->options([
                        'open_trip' => 'Open Trip',
                        'private_trip' => 'Private Trip',
                    ])
                    ->required(),
                TextInput::make('meeting_point')
                    ->label('Meeting Point')
                    ->required(),
                FileUpload::make('featured_image')
                    ->label('Foto Utama')
                    ->image()
                    ->directory('travel-packages')
                    ->required(),
                RichEditor::make('description')
                    ->label('Deskripsi Paket')
                    ->columnSpanFull()
                    ->required(),
                Textarea::make('included')
                    ->label('Fasilitas Termasuk (Included)')
                    ->required(),
                Textarea::make('excluded')
                    ->label('Fasilitas Tidak Termasuk (Excluded)')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')->label('Foto'),
                TextColumn::make('title')->label('Judul')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Kategori'),
                TextColumn::make('location')->label('Lokasi'),
                TextColumn::make('type')->label('Tipe'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTravelPackages::route('/'),
            'create' => Pages\CreateTravelPackage::route('/create'),
            'edit' => Pages\EditTravelPackage::route('/{record}/edit'),
        ];
    }
}