<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BestSellerService;

class MainController extends Controller
{
    public function main(BestSellerService $bestSellerService)
    {
        try {
            $bestSellers = $bestSellerService->getAll();
            return view('user.main', compact('bestSellers'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengambil data best seller: ' . $e->getMessage());
        }
    }

    public function getByCategory($category, BestSellerService $bestSellerService)
    {
        $items = $bestSellerService->getByCategory($category);
        return response()->json($items);
    }
    
}
