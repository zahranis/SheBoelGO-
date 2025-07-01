<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FirebaseRatingService
{
    private $baseUrl;
    private $projectId;
    private $apiKey;

    public function __construct()
    {
        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{project-id}/databases/(default)/documents";
        $this->projectId = env('FIREBASE_PROJECT_ID');
        $this->apiKey = env('FIREBASE_API_KEY');
    }

    private function getCollectionUrl($collection)
    {
        return str_replace('{project-id}', $this->projectId, $this->baseUrl) . "/$collection";
    }

    public function getUserRatingForItem($userId, $itemId)
    {
        $url = $this->getCollectionUrl("ratings") . '?key=' . $this->apiKey;

        $response = Http::get($url);

        if ($response->failed()) return null;

        $docs = $response->json()['documents'] ?? [];

        foreach ($docs as $doc) {
            $fields = $doc['fields'];
            if (($fields['userId']['stringValue'] ?? null) === $userId &&
                ($fields['itemId']['stringValue'] ?? null) === $itemId) {
                return [
                    'rating' => (int) ($fields['rating']['integerValue'] ?? 0),
                    'comment' => $fields['comment']['stringValue'] ?? '',
                ];
            }
        }

        return null;
    }

    public function submitRating(array $data)
    {
        $url = $this->getCollectionUrl("ratings") . '?key=' . $this->apiKey;

        $payload = [
            'fields' => [
                'userId' => ['stringValue' => $data['userId']],
                'itemId' => ['stringValue' => $data['itemId']],
                'rating' => ['integerValue' => (int) $data['rating']],
                'comment' => ['stringValue' => $data['comment']],
                'timestamp' => ['timestampValue' => now()->toISOString()],
            ]
        ];

        $response = Http::post($url, $payload);
        return $response->successful();
    }
}
