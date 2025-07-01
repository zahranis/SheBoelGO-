<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class FirebaseCartService
{
    protected $accessToken;
    protected $projectId;

    public function __construct()
    {
        $this->projectId = env('FIREBASE_PROJECT_ID');
    }

    public function getCartItems($userId, $status = 'aktif')
    {
        $accessToken = $this->getAccessToken();
        $collectionName = "cart_items_" . $status;

        try {
            // Ambil semua best_seller (sekali saja)
            $bestSellers = $this->getAllBestSellers();

            // Ambil semua cart_items_{status}
            $response = Http::withToken($accessToken)->get(
                "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collectionName}",
                [
                    'pageSize' => 100,
                    'orderBy' => 'date desc'
                ]
            );

            if (!$response->successful()) {
                return [];
            }

            $data = $response->json();
            $documents = $data['documents'] ?? [];

            $cartItems = [];

            foreach ($documents as $doc) {
                $fields = $doc['fields'];
                
                if (($fields['user_id']['stringValue'] ?? '') !== $userId) {
                    continue;
                }

                $docId = basename($doc['name']);
                $itemId = $fields['item_id']['stringValue'] ?? null;

                // Ambil data dari best_sellers
                $item = $bestSellers[$itemId] ?? null;

                $cartItems[] = [
                    'id' => $docId,
                    'item_id' => $itemId,
                    'name' => $item['name'] ?? '',
                    'price' => $item['price'] ?? 0,
                    'image_res' => $item['image_res'] ?? '',
                    'quantity' => $fields['quantity']['integerValue'] ?? 1,
                    'status' => $fields['status']['stringValue'] ?? '',
                    'created_at' => isset($fields['date']['timestampValue']) 
                        ? Carbon::parse($fields['date']['timestampValue']) 
                        : Carbon::now(),
                    'user_id' => $fields['user_id']['stringValue'] ?? ''
                ];
            }

            return $cartItems;

        } catch (\Exception $e) {
            \Log::error('Error fetching cart items: ' . $e->getMessage());
            return [];
        }
    }

    public function getAllBestSellers()
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)->get(
            "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/best_seller"
        );

        if (!$response->successful()) {
            return [];
        }

        $documents = $response->json()['documents'] ?? [];

        $bestSellers = [];

        foreach ($documents as $doc) {
            $fields = $doc['fields'] ?? [];
            $id = basename($doc['name']);

            $bestSellers[$id] = [
                'id' => $id,
                'name' => $fields['name']['stringValue'] ?? '',
                'price' => $fields['price']['doubleValue'] ?? $fields['price']['integerValue'] ?? 0,
                'image_res' => $fields['image_res']['stringValue'] ?? '',
                'category' => $fields['category']['stringValue'] ?? '',
                'description' => $fields['description']['stringValue'] ?? ''
            ];
        }

        return $bestSellers;
    }

    public function addCartItem($userId, $itemData)
    {
        $accessToken = $this->getAccessToken();

        $payload = [
            'fields' => [
                'item_id' => ['stringValue' => $itemData['item_id']],
                'name' => ['stringValue' => $itemData['name']],
                'price' => ['doubleValue' => (float)$itemData['price']],
                'image_res' => ['stringValue' => $itemData['image_res']],
                'quantity' => ['integerValue' => (int)$itemData['quantity']],
                'status' => ['stringValue' => 'Sedang diproses'],
                'user_id' => ['stringValue' => $userId],
                'date' => ['timestampValue' => Carbon::now()->toIso8601String()],
            ]
        ];

        $response = Http::withToken($accessToken)->post(
            "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/cart_items_aktif",
            $payload
        );

        return $response->successful();
    }

    public function moveCartItem($orderId, $userId, $fromStatus, $toStatus, $newStatus = null)
    {
        $accessToken = $this->getAccessToken();

        try {
            $fromCollection = "cart_items_" . $fromStatus;
            $toCollection = "cart_items_" . $toStatus;

            $orderResponse = Http::withToken($accessToken)
                ->get("https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$fromCollection}/{$orderId}");

            if (!$orderResponse->successful()) {
                throw new \Exception('Order tidak ditemukan');
            }

            $orderData = $orderResponse->json();
            
            if ($orderData['fields']['user_id']['stringValue'] !== $userId) {
                throw new \Exception('Unauthorized');
            }

            if ($newStatus) {
                $orderData['fields']['status']['stringValue'] = $newStatus;
            }

            $timestampField = $toStatus . '_at';
            $orderData['fields'][$timestampField] = ['timestampValue' => Carbon::now()->toIso8601String()];

            $moveResponse = Http::withToken($accessToken)->post(
                "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$toCollection}",
                ['fields' => $orderData['fields']]
            );

            if ($moveResponse->successful()) {
                Http::withToken($accessToken)
                    ->delete("https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$fromCollection}/{$orderId}");

                return true;
            }

            return false;

        } catch (\Exception $e) {
            \Log::error('Error moving cart item: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getOrderDetail($orderId, $userId, $status = 'aktif')
    {
        $accessToken = $this->getAccessToken();
        $collectionName = "cart_items_" . $status;

        try {
            $orderResponse = Http::withToken($accessToken)
                ->get("https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collectionName}/{$orderId}");

            if (!$orderResponse->successful()) {
                return null;
            }

            $orderData = $orderResponse->json();
            $fields = $orderData['fields'];

            if ($fields['user_id']['stringValue'] !== $userId) {
                return null;
            }

            return [
                'id' => $orderId,
                'item_id' => $fields['item_id']['stringValue'] ?? '',
                'name' => $fields['name']['stringValue'] ?? '',
                'price' => $fields['price']['doubleValue'] ?? $fields['price']['integerValue'] ?? 0,
                'quantity' => $fields['quantity']['integerValue'] ?? 1,
                'image_res' => $fields['image_res']['stringValue'] ?? '',
                'status' => $fields['status']['stringValue'] ?? '',
                'created_at' => isset($fields['date']['timestampValue']) 
                    ? Carbon::parse($fields['date']['timestampValue']) 
                    : Carbon::now(),
                'user_id' => $fields['user_id']['stringValue'] ?? ''
            ];

        } catch (\Exception $e) {
            \Log::error('Error fetching order detail: ' . $e->getMessage());
            return null;
        }
    }

    private function getAccessToken()
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

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

        $this->accessToken = $response->json('access_token');
        return $this->accessToken;
    }
}
