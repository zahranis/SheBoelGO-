<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BestSellerService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class ItemController extends Controller
{
    protected $bestSellerService;

    public function __construct(BestSellerService $bestSellerService)
    {
        $this->bestSellerService = $bestSellerService;
    }

    public function show($itemId)
    {
        $items = $this->bestSellerService->getAll();

        $item = $items->firstWhere('id', $itemId);

        if (!$item) {
            abort(404, 'Item tidak ditemukan');
        }

        return view('item.detail', ['item' => $item]);
    }

    // Contoh penggunaan di ItemController yang sudah ada
    public function addToCart(Request $request)
    {
        $request->validate([
            'item_id' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $accessToken = $this->getAccessToken();
        $projectId = env('FIREBASE_PROJECT_ID');
        
        $user = session('firebase_user');
        $userId = $user['uid'] ?? 'anonymous';        
        
        // Debug untuk memastikan user tersedia
        if (!$user) {
            return back()->withErrors('User tidak terautentikasi.');
        }

        $payload = [
            'fields' => [
                'item_id' => ['stringValue' => $request->item_id],
                'quantity' => ['integerValue' => $request->quantity],
                'status' => ['stringValue' => 'aktif'],
                'user_id' => ['stringValue' => $userId],
                'date' => ['timestampValue' => Carbon::now()->toIso8601String()],
            ]
        ];

        $response = Http::withToken($accessToken)->post(
            "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/cart_items_aktif",
            $payload
        );

        if (!$response->successful()) {
            return back()->withErrors('Gagal menambahkan ke keranjang.');
        }

        return back()->with('success', 'Item berhasil ditambahkan ke keranjang.');
    }

    private function getAccessToken()
    {
        $credentials = json_decode(file_get_contents(config('firebase.credentials.file')), true);

        $jwt = \Firebase\JWT\JWT::encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/datastore',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => time(),
            'exp' => time() + 3600,
        ], $credentials['private_key'], 'RS256');

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        return $response->json('access_token');
    }
}
