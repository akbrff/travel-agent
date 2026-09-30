<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelPackage extends Model
{
    use HasFactory;

    // Mengizinkan seluruh kolom dapat diisi melalui form Filament
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function itineraries()
    {
        return $this->hasMany(PackageItinerary::class);
    }

    public function schedules()
    {
        return $this->hasMany(PackageSchedule::class);
    }
}