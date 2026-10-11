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
use Illuminate\Support\Facades\DB;

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
                // 1. Lihat Bukti Pembayaran
                Action::make('view_payment')
                    ->label('Bukti Bayar')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->color('info')
                    ->visible(fn (Booking $record) => $record->payment !== null)
                    ->modalHeading('Detail Bukti Pembayaran')
                    ->modalContent(fn (Booking $record) => view(
                        'filament.pages.payment-modal',
                        [
                            'payment' => $record->payment,
                        ]
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),

                // 2. Setujui Pembayaran
                Action::make('approve_payment')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pembayaran?')
                    ->modalDescription(
                        'Booking akan ditandai lunas jika masih menunggu verifikasi dan nominal pembayaran sesuai.'
                    )
                    ->visible(
                        fn (Booking $record) =>
                            $record->status === 'waiting_verification'
                    )
                    ->action(function (Booking $record) {
                        DB::transaction(function () use ($record) {
                            $booking = Booking::whereKey($record->id)
                                ->lockForUpdate()
                                ->firstOrFail();

                            if ($booking->status !== 'waiting_verification') {
                                throw new \RuntimeException(
                                    'Status booking sudah berubah. Muat ulang halaman dan periksa kembali.'
                                );
                            }

                            $payment = $booking->payment;

                            if (
                                !$payment ||
                                (float) $payment->amount !==
                                (float) $booking->total_amount
                            ) {
                                throw new \RuntimeException(
                                    'Bukti pembayaran tidak ditemukan atau nominal pembayaran tidak sesuai.'
                                );
                            }

                            $booking->update([
                                'status' => 'paid',
                            ]);
                        });

                        Notification::make()
                            ->title('Pembayaran Disetujui')
                            ->body(
                                "Booking {$record->booking_code} telah ditandai lunas."
                            )
                            ->success()
                            ->send();
                    }),

                // 3. Tolak Pembayaran
                Action::make('reject_payment')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Bukti Pembayaran?')
                    ->modalDescription(
                        'Bukti pembayaran akan ditolak. Status booking menjadi dibatalkan sementara dan pengguna dapat mengunggah bukti baru pada booking yang sama.'
                    )
                    ->visible(
                        fn (Booking $record) =>
                            $record->status === 'waiting_verification'
                    )
                    ->action(function (Booking $record) {
                        DB::transaction(function () use ($record) {
                            $booking = Booking::whereKey($record->id)
                                ->lockForUpdate()
                                ->firstOrFail();

                            if ($booking->status !== 'waiting_verification') {
                                throw new \RuntimeException(
                                    'Status booking sudah berubah. Muat ulang halaman dan periksa kembali.'
                                );
                            }

                            /*
                             * Pengembalian kuota ditangani oleh
                             * BookingObserver saat status berubah
                             * menjadi cancelled.
                             *
                             * Jangan menambahkan kuota secara manual
                             * di aksi ini agar tidak terjadi duplikasi.
                             */
                            $booking->update([
                                'status' => 'cancelled',
                            ]);
                        });

                        Notification::make()
                            ->title('Bukti Pembayaran Ditolak')
                            ->body(
                                "Booking {$record->booking_code} dibatalkan sementara. Pengguna dapat mengunggah bukti pembayaran baru."
                            )
                            ->warning()
                            ->send();
                    }),
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
        ];
    }
}