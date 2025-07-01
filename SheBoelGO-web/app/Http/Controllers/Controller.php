<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Get current Firebase user from session
     */
    protected function getFirebaseUser()
    {
        $userData = Session::get('firebase_user');
        
        if ($userData && $userData['authenticated']) {
            return (object) $userData;
        }
        
        return null;
    }

    /**
     * Check if user is authenticated
     */
    protected function isAuthenticated()
    {
        return $this->getFirebaseUser() !== null;
    }

    /**
     * Redirect to login if not authenticated
     */
    protected function requireAuth()
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login');
        }
        return null;
    }
}

// ItemController.php (Updated)
// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Services\BestSellerService;
// use Illuminate\Support\Facades\Http;
// use Carbon\Carbon;

// class ItemController extends Controller
// {
//     protected $bestSellerService;

//     public function __construct(BestSellerService $bestSellerService)
//     {
//         $this->bestSellerService = $bestSellerService;
//     }

//     public function show($itemId)
//     {
//         $items = $this->bestSellerService->getAll();
//         $item = $items->firstWhere('id', $itemId);

//         if (!$item) {
//             abort(404, 'Item tidak ditemukan');
//         }

//         return view('item.detail', ['item' => $item]);
//     }

//     public function addToCart(Request $request)
//     {
//         // Check authentication
//         if ($redirect = $this->requireAuth()) {
//             return $redirect;
//         }

//         $request->validate([
//             'item_id' => 'required|string',
//             'quantity' => 'required|integer|min:1',
//         ]);

//         $user = $this->getFirebaseUser();
//         $userId = $user->uid;

//         $accessToken = $this->getAccessToken();
//         $projectId = env('FIREBASE_PROJECT_ID');

//         $payload = [
//             'fields' => [
//                 'item_id' => ['stringValue' => $request->item_id],
//                 'quantity' => ['integerValue' => $request->quantity],
//                 'status' => ['stringValue' => 'aktif'],
//                 'user_id' => ['stringValue' => $userId],
//                 'date' => ['timestampValue' => Carbon::now()->toIso8601String()],
//             ]
//         ];

//         $response = Http::withToken($accessToken)->post(
//             "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/cart_items_aktif",
//             $payload
//         );

//         if (!$response->successful()) {
//             return back()->withErrors('Gagal menambahkan ke keranjang.');
//         }

//         return back()->with('success', 'Item berhasil ditambahkan ke keranjang.');
//     }

//     private function getAccessToken()
//     {
//         $credentials = json_decode(file_get_contents(config('firebase.credentials.file')), true);
//         $jwt = \Firebase\JWT\JWT::encode([
//             'iss' => $credentials['client_email'],
//             'scope' => 'https://www.googleapis.com/auth/datastore',
//             'aud' => 'https://oauth2.googleapis.com/token',
//             'iat' => time(),
//             'exp' => time() + 3600,
//         ], $credentials['private_key'], 'RS256');

//         $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
//             'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
//             'assertion' => $jwt,
//         ]);

//         return $response->json('access_token');
//     }
// }