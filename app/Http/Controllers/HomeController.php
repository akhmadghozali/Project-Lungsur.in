<?php

namespace App\Http\Controllers;

use App\Models\Listing;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return redirect()->route('admin.verifications.index');
        }

        $query = Listing::available()->with(['category', 'images']);

        // Filter pengurutan
        $sort = request('sort', 'latest');

        if ($sort === 'lowest') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'highest') {
            $query->orderBy('price', 'desc');
        } else {
            $sort = 'latest';
            $query->latest();
        }

        if (auth()->check()) {
            // User login: 12 barang per halaman
            $listings = $query->paginate(12)->withQueryString();
        } else {
            // Guest: maksimal 10 barang acak
            $listings = $query->inRandomOrder()->limit(10)->get();
        }

        return view('welcome', compact('listings', 'sort'));
    }
}