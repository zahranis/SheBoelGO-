<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FirestoreService
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

    private function getCollectionUrl($collectionPath)
    {
        return str_replace('{project-id}', $this->projectId, $this->baseUrl) . "/$collectionPath";
    }

    public function getRole(string $uid): ?string
    {
        $url = $this->getCollectionUrl("roles/{$uid}") . '?key=' . $this->apiKey;

        $response = Http::get($url);

        if ($response->ok()) {
            $data = $response->json();
            return $data['fields']['role']['stringValue'] ?? null;
        }

        return null;
    }

    public function setRole(string $uid, string $role = 'user'): bool
    {
        $url = $this->getCollectionUrl("roles/{$uid}") . '?key=' . $this->apiKey;

        $body = [
            'fields' => [
                'role' => ['stringValue' => $role]
            ]
        ];

        $response = Http::patch($url, $body);

        return $response->ok();
    }
}
