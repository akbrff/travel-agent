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

            <!-- Daftar Peserta Penumpang -->
            @if($booking->passengers && $booking->passengers->count() > 0)
                <div class="space-y-2 border-t border-gray-100 pt-4">
                    <h4 class="font-bold text-gray-900 text-sm">Daftar Peserta (Pax)</h4>
                    <div class="grid grid-cols-1 gap-2">
                        @foreach($booking->passengers as $index => $passenger)
                            <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 text-xs flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-gray-800">{{ $index + 1 }}. {{ $passenger->name }}</p>
                                    <p class="text-gray-500 mt-0.5">No. KTP/Paspor: {{ $passenger->id_number }}</p>
                                </div>
                                @if($passenger->phone)
                                    <span class="text-gray-500 bg-white px-2.5 py-1 rounded-md border border-gray-200">{{ $passenger->phone }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Instruksi Transfer -->
            <div class="bg-blue-50 p-5 rounded-xl text-sm text-blue-900 space-y-2">
                <p class="font-bold">Silakan Transfer Pembayaran ke Rekening Berikut:</p>
                <p class="text-xs">Bank BCA: <strong>1234-5678-90</strong> a.n. PT Travel Agent Indonesia</p>
                <p class="text-xs text-blue-700 mt-2">*Setelah transfer, status pembayaran akan diverifikasi oleh admin di dashboard.</p>
            </div>

            <!-- Pesan Sukses -->
            @if(session('success'))
                <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if($booking->status === 'pending')
                <!-- Form Upload Bukti Transfer -->
                <form action="{{ route('booking.pay', $booking->booking_code) }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-4 border-t border-gray-100">
                    @csrf
                    <h3 class="font-bold text-gray-900 text-base">Konfirmasi Pembayaran</h3>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Transfer Dari Bank</label>
                        <input type="text" name="bank_name" placeholder="Contoh: BCA / Mandiri / BRI" class="w-full border-gray-300 border p-3 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pemilik Rekening / Pengirim</label>
                        <input type="text" name="account_name" placeholder="Nama sesuai pada bukti transfer" class="w-full border-gray-300 border p-3 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unggah Struk / Bukti Transfer (JPG, PNG, maks 2MB)</label>
                        <input type="file" name="proof_image" accept="image/*" class="w-full border-gray-300 border p-2 rounded-xl text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                    </div>

                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl shadow transition">Unggah Bukti Pembayaran</button>
                </form>
            @elseif($booking->status === 'waiting_verification')
                <div class="bg-amber-50 text-amber-800 p-4 rounded-xl text-sm text-center border border-amber-100">
                    ⌛ Bukti pembayaran Anda sedang diverifikasi oleh admin. Silakan cek berkala halaman ini atau menu <strong>Riwayat Pesanan</strong>.
                </div>
            @endif

            <div class="pt-2">
                <a href="{{ route('booking.history') }}" class="w-full block text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl transition text-sm">Lihat Riwayat Pesanan</a>
            </div>

        </div>
    </main>

</body>
</html>