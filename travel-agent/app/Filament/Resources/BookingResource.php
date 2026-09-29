<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('package_schedule_id')
                    ->relationship('packageSchedule', 'id')
                    ->required(),
                Forms\Components\TextInput::make('booking_code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('total_passengers')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'waiting_verification' => 'Waiting Verification',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking_code')
                    ->label('Kode Booking')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('user.name')
                    ->label('Pemesan')
                    ->searchable(),

                TextColumn::make('packageSchedule.travelPackage.title')
                    ->label('Paket Wisata')
                    ->limit(20),

                TextColumn::make('total_passengers')
                    ->label('Pax')
                    ->alignCenter(),

                TextColumn::make('total_amount')
                    ->label('Total Biaya')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'waiting_verification' => 'warning',
                        'pending' => 'gray',
                        'cancelled' => 'danger',
                        'completed' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Tanggal Pesan')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                // 1. Tombol Lihat Bukti Pembayaran
                Action::make('view_payment')
                    ->label('Bukti Bayar')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->color('info')
                    ->visible(fn (Booking $record) => $record->payment !== null)
                    ->modalHeading('Detail Bukti Pembayaran')
                    ->modalContent(fn (Booking $record) => view('filament.pages.payment-modal', [
                        'payment' => $record->payment
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),

                // 2. Tombol Setujui Pembayaran (Approve)
                Action::make('approve_payment')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pembayaran?')
                    ->modalDescription('Status pesanan akan diubah menjadi PAID dan E-Ticket dapat diunduh oleh pengguna.')
                    ->visible(fn (Booking $record) => $record->status === 'waiting_verification')
                    ->action(function (Booking $record) {
                        $record->update(['status' => 'paid']);

                        Notification::make()
                            ->title('Pembayaran Disetujui')
                            ->body("Booking {$record->booking_code} telah dikonfirmasi (PAID).")
                            ->success()
                            ->send();
                    }),

                // 3. Tombol Tolak Pembayaran (Reject)
                Action::make('reject_payment')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Pembayaran?')
                    ->modalDescription('Status pesanan akan diubah menjadi CANCELLED dan kuota jadwal paket akan otomatis dikembalikan.')
                    ->visible(fn (Booking $record) => $record->status === 'waiting_verification' || $record->status === 'pending')
                    ->action(function (Booking $record) {
                        $record->update(['status' => 'cancelled']);

                        Notification::make()
                            ->title('Pembayaran Ditolak')
                            ->body("Booking {$record->booking_code} telah dibatalkan dan kuota dipulihkan.")
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}