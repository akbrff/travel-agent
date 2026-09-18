<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\TravelPackage;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $featuredPackages = TravelPackage::with(['category', 'schedules'])
            ->latest()
            ->take(6)
            ->get();

        return view('home', compact('categories', 'featuredPackages'));
    }
}