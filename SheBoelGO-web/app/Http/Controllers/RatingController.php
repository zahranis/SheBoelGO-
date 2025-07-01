<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseRatingService;
use App\Services\FirebaseItemService;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function show($itemId)
    {
        $user = session('firebase_user');
        $userId = $user['uid'] ?? 'anonymous';  
        $ratingService = new FirebaseRatingService();
        $itemService = new FirebaseItemService();

        $item = $itemService->getItemById($itemId);
        $existing = $ratingService->getUserRatingForItem($userId, $itemId);

        return view('rating.index', [
            'itemId' => $itemId,
            'item' => $item,
            'rating' => $existing['rating'] ?? 0,
            'comment' => $existing['comment'] ?? '',
            'hasRated' => $existing !== null
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $user = session('firebase_user');
        $userId = $user['uid'] ?? 'anonymous';  
        $service = new FirebaseRatingService();

        $existing = $service->getUserRatingForItem($userId, $request->item_id);
        if ($existing) {
            return redirect()->route('rating', ['itemId' => $request->item_id])
                ->with('error', 'Anda sudah memberikan rating.');
        }

        $service->submitRating([
            'userId' => $userId,
            'itemId' => $request->item_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('cart')->with('success', 'Terima kasih atas ulasan Anda!');
    }
}
