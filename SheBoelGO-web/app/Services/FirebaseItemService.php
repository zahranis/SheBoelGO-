<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FirebaseItemService
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

    public function getItemById($itemId)
    {
        $url = $this->getCollectionUrl("best_seller/$itemId") . '?key=' . $this->apiKey;
        $res = Http::get($url);

        if ($res->failed()) return null;

        $fields = $res->json()['fields'] ?? [];

        return [
            'id' => $itemId,
            'name' => $fields['name']['stringValue'] ?? '',
            'price' => (int) ($fields['price']['integerValue'] ?? 0),
            'image_res' => $fields['image_res']['stringValue'] ?? 'placeholder.jpg',
            'description' => $fields['description']['stringValue'] ?? '',
            'category' => $fields['category']['stringValue'] ?? '',
        ];
    }

    public function createItem(array $data)
    {
        $formatted = [
            'fields' => [
                'name' => ['stringValue' => $data['name']],
                'category' => ['stringValue' => $data['category']],
                'description' => ['stringValue' => $data['description']],
                'image_res' => ['stringValue' => $data['image_res']],
                'price' => ['integerValue' => (int) $data['price']],
            ]
        ];
        $url = $this->getCollectionUrl("best_seller") . '?key=' . $this->apiKey;
        return Http::post($url, $formatted)->ok();
    }

    public function updateItem(string $id, array $data)
    {
        $formatted = [
            'fields' => [
                'name' => ['stringValue' => $data['name']],
                'category' => ['stringValue' => $data['category']],
                'description' => ['stringValue' => $data['description']],
                'image_res' => ['stringValue' => $data['image_res']],
                'price' => ['integerValue' => (int) $data['price']],
            ]
        ];
        $url = $this->getCollectionUrl("best_seller/$id") . '?key=' . $this->apiKey;
        return Http::patch($url, $formatted)->ok();
    }

    public function deleteItem(string $id)
    {
        $url = $this->getCollectionUrl("best_seller/$id") . '?key=' . $this->apiKey;
        return Http::delete($url)->ok();
    }

    public function getAllItems()
    {
        $url = $this->getCollectionUrl("best_seller") . '?key=' . $this->apiKey;
        $res = Http::get($url);

        if ($res->failed()) return [];

        return collect($res->json()['documents'] ?? [])->map(function ($doc) {
            $fields = $doc['fields'] ?? [];
            return [
                'id' => basename($doc['name']),
                'name' => $fields['name']['stringValue'] ?? '',
            ];
        })->all();
    }
}
