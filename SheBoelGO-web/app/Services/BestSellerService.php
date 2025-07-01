<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BestSellerService
{
    public function getAll()
    {
        $projectId = env('FIREBASE_PROJECT_ID');
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->get("https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/best_seller");

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch best sellers');
        }

        $documents = $response->json('documents') ?? [];

        return collect($documents)->map(function ($doc) {
            $fields = $doc['fields'];

            return [
                'id' => basename($doc['name']),
                'name' => $fields['name']['stringValue'] ?? '',
                'price' => $fields['price']['integerValue'] ?? 0,
                'description' => $fields['description']['stringValue'] ?? '',
                'image_res' => $fields['image_res']['stringValue'] ?? '',
                'category' => $fields['category']['stringValue'] ?? '',
            ];
        });
    }

    public function getByCategory($category)
{
    $projectId = env('FIREBASE_PROJECT_ID');
    $accessToken = $this->getAccessToken();

    $response = Http::withToken($accessToken)
        ->post("https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents:runQuery", [
            "structuredQuery" => [
                "from" => [["collectionId" => "best_seller"]],
                "where" => [
                    "fieldFilter" => [
                        "field" => ["fieldPath" => "category"],
                        "op" => "EQUAL",
                        "value" => ["stringValue" => strtolower($category)],
                    ],
                ],
            ]
        ]);

    if (!$response->successful()) {
        throw new \Exception("Failed to fetch category $category");
    }

    $documents = collect($response->json())
        ->filter(fn ($doc) => isset($doc['document']))
        ->map(function ($doc) {
            $fields = $doc['document']['fields'];
            return [
                'id' => basename($doc['document']['name']),
                'name' => $fields['name']['stringValue'] ?? '',
                'price' => $fields['price']['integerValue'] ?? 0,
                'description' => $fields['description']['stringValue'] ?? '',
                'image_res' => $fields['image_res']['stringValue'] ?? '',
                'category' => $fields['category']['stringValue'] ?? '',
            ];
        });

    return $documents;
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
