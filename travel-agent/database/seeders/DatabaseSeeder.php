<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\PackageSchedule;
use App\Models\TravelPackage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin & Customer
        User::create([
            'name' => 'Admin Travel',
            'email' => 'admin@travel.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // 2. Kategori Sampel
        $category = Category::create([
            'name' => 'Open Trip',
            'slug' => 'open-trip',
        ]);

        // 3. Paket Wisata Sampel
        $package = TravelPackage::create([
            'category_id' => $category->id,
            'title' => 'Open Trip Pahawang Island',
            'slug' => 'open-trip-pahawang-island',
            'location' => 'Lampung',
            'duration' => '1 Day Trip',
            'type' => 'open_trip',
            'meeting_point' => 'Pelabuhan Ketapang',
            'featured_image' => 'travel-packages/sample.jpg',
            'description' => 'Nikmati keindahan terumbu karang dan keindahan alam Pulau Pahawang.',
            'included' => 'Makan Siang, Transport Lokal, Tour Guide, Peralatan Snorkeling',
            'excluded' => 'Pengeluaran Pribadi, Tiket Pesawat/Kapal ke Lampung',
        ]);

        // 4. Jadwal Keberangkatan Sampel
        PackageSchedule::create([
            'travel_package_id' => $package->id,
            'departure_date' => now()->addDays(7)->format('Y-m-d'),
            'price_per_person' => 250000,
            'quota' => 20,
            'remaining_quota' => 20,
        ]);
    }
}