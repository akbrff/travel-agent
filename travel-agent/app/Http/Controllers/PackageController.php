<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\TravelPackage;

class PackageController extends Controller
{
    public function index()
    {
        $packages = TravelPackage::with(['category', 'schedules'])
            ->latest()
            ->paginate(9);

        $categories = Category::all();

        return view('packages.index', compact('packages', 'categories'));
    }

    public function show($slug)
    {
        $package = TravelPackage::with(['category', 'itineraries', 'schedules' => function ($query) {
            $query->where('departure_date', '>=', now())
                  ->where('remaining_quota', '>', 0);
        }])->where('slug', $slug)->firstOrFail();

        return view('packages.show', compact('package'));
    }
}