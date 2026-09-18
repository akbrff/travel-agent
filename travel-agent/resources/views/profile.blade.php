<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Travel Agent</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    <!-- Header / Navbar -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-blue-600">TravelAgent</a>
            <nav class="space-x-6 hidden md:flex items-center">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-blue-600 font-medium">Beranda</a>
                <a href="{{ route('packages.index') }}" class="text-gray-600 hover:text-blue-600 font-medium">Paket Wisata</a>
                <a href="{{ route('booking.history') }}" class="text-gray-600 hover:text-blue-600 font-medium">Riwayat Pesanan</a>
            </nav>
            <div class="flex items-center space-x-4">
                <a href="{{ route('profile.edit') }}" class="text-blue-600 font-semibold text-sm">Profil Saya</a>
            </div>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 py-10">
        <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm space-y-6">
            <h1 class="text-2xl font-bold text-gray-900">Pengaturan Profil</h1>

            @if(session('success'))
                <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border-gray-300 border p-3 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border-gray-300 border p-3 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 08123456789" class="w-full border-gray-300 border p-3 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <hr class="my-6 border-gray-100">

                <p class="text-sm font-bold text-gray-800">Ubah Password (Kosongkan jika tidak ingin diubah)</p>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <input type="password" name="password" class="w-full border-gray-300 border p-3 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="w-full border-gray-300 border p-3 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow transition mt-4">Simpan Perubahan</button>
            </form>
        </div>
    </main>

</body>
</html>