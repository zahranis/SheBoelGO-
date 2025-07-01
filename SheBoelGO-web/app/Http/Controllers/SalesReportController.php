<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $collection = $request->get('status', 'cart_items_aktif');
        $accessToken = $this->getAccessToken();
        $projectId = env('FIREBASE_PROJECT_ID');

        // Ambil dokumen dari Firestore
        $response = Http::withToken($accessToken)
            ->get("https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/{$collection}");

        if (!$response->successful()) {
            return back()->withErrors('Gagal mengambil data.');
        }

        $items = [];

        foreach ($response->json('documents') ?? [] as $doc) {
            $fields = $doc['fields'] ?? [];

            $itemId = basename($doc['name']); // ambil ID dokumen dari path
            $productId = $fields['item_id']['stringValue'] ?? null;
            $userId = $fields['user_id']['stringValue'] ?? null;
            $quantity = $fields['quantity']['integerValue'] ?? 0;
            $status = $fields['status']['stringValue'] ?? '-';
            $date = $fields['date']['timestampValue'] ?? null;

            // Ambil data produk
            $productName = '-';
            if ($productId) {
                $productResponse = Http::withToken($accessToken)
                    ->get("https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/best_seller/{$productId}");
                if ($productResponse->successful()) {
                    $productName = $productResponse['fields']['name']['stringValue'] ?? '-';
                }
            }

            // Ambil data user
            $userEmail = '-';
            if ($userId) {
                $userResponse = Http::withToken($accessToken)
                    ->get("https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/users/{$userId}");
                if ($userResponse->successful()) {
                    $userEmail = $userResponse['fields']['email']['stringValue'] ?? '-';
                }
            }

            $items[] = [
                'id' => $itemId,
                'quantity' => $quantity,
                'status' => $status,
                'date' => $date,
                'product' => ['name' => $productName],
                'user_email' => $userEmail,
            ];
        }

        return view('admin.sales_report', [
            'status' => $collection,
            'items' => $items,
        ]);
    }

    public function detail($collection, $id)
    {
        $accessToken = $this->getAccessToken();
        $projectId = env('FIREBASE_PROJECT_ID');

        $response = Http::withToken($accessToken)->get(
            "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/{$collection}/{$id}"
        );

        if (!$response->successful()) {
            abort(404, 'Data tidak ditemukan.');
        }

        $fields = $response['fields'];
        $item = [
            'id' => $id,
            'status' => $fields['status']['stringValue'] ?? '-',
        ];

        return view('admin.sales_report_detail', [
            'item' => $item,
            'collection' => $collection,
        ]);
    }

    public function updateStatus(Request $request, $collection, $id)
    {
        $accessToken = $this->getAccessToken();
        $projectId = env('FIREBASE_PROJECT_ID');

        $newStatus = $request->input('status');

        $payload = [
            'fields' => [
                'status' => ['stringValue' => $newStatus]
            ]
        ];

        $response = Http::withToken($accessToken)->patch(
            "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/{$collection}/{$id}?updateMask.fieldPaths=status",
            $payload
        );

        if (!$response->successful()) {
            return back()->withErrors('Gagal memperbarui status.');
        }

        return redirect()->route('admin.sales_report.detail', [$collection, $id])
                        ->with('success', 'Status diperbarui.');
    }

    public function move(Request $request, $from, $to, $id)
    {
        $accessToken = $this->getAccessToken();
        $projectId = env('FIREBASE_PROJECT_ID');

        // Ambil dokumen lama
        $oldDoc = Http::withToken($accessToken)->get(
            "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/{$from}/{$id}"
        );

        if (!$oldDoc->successful()) {
            return back()->withErrors('Gagal mengambil data.');
        }

        $fields = $oldDoc['fields'];

        // Simpan ke koleksi baru
        $newDoc = Http::withToken($accessToken)->post(
            "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/{$to}",
            ['fields' => $fields]
        );

        if (!$newDoc->successful()) {
            return back()->withErrors('Gagal memindahkan data.');
        }

        // Hapus dari koleksi lama
        Http::withToken($accessToken)->delete(
            "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/{$from}/{$id}"
        );

        return redirect()->route('admin.sales_report', ['status' => $to])
                        ->with('success', 'Data berhasil dipindahkan.');
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