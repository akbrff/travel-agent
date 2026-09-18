<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout {{ $booking->booking_code }} - Travel Agent</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-blue-600">TravelAgent</a>
            <div class="flex items-center space-x-4">
                <a href="{{ route('booking.history') }}" class="text-gray-600 hover:text-blue-600 text-sm font-medium">Riwayat Pesanan</a>
            </div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-12">
        <div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm space-y-6">
            
            <div class="text-center border-b border-gray-100 pb-6">
                <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-3 py-1 rounded-full uppercase">Status: {{ strtoupper($booking->status) }}</span>
                <h1 class="text-2xl font-bold mt-3">Selesaikan Pembayaran Anda</h1>
                <p class="text-gray-500 text-sm mt-1">Kode Booking: <strong class="text-blue-600">{{ $booking->booking_code }}</strong></p>
            </div>

            <!-- Detail Pemesanan -->
            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Paket Wisata</span>
                    <span class="font-semibold text-gray-900">{{ $booking->packageSchedule->travelPackage->title }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Tanggal Keberangkatan</span>
                    <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->packageSchedule->departure_date)->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Jumlah Peserta</span>
                    <span class="font-semibold text-gray-900">{{ $booking->total_passengers }} Pax</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50 text-base">
                    <span class="font-bold text-gray-700">Total Biaya</span>
                    <span class="font-extrabold text-emerald-600">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Instruksi Transfer -->
            <div class="bg-blue-50 p-5 rounded-xl text-sm text-blue-900 space-y-2">
                <p class="font-bold">Silakan Transfer Pembayaran ke Rekening Berikut:</p>
                <p class="text-xs">Bank BCA: <strong>1234-5678-90</strong> a.n. PT Travel Agent Indonesia</p>
                <p class="text-xs text-blue-700 mt-2">*Setelah transfer, status pembayaran akan diverifikasi oleh admin di dashboard.</p>
            </div>

            <div class="pt-4 flex space-x-4">
                <a href="{{ route('booking.history') }}" class="w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition">Lihat Riwayat Pesanan</a>
            </div>

        </div>
    </main>

</body>
</html>