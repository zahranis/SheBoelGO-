<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\BestSellerService;
use App\Services\FirebaseCartService;

class CartController extends Controller
{
    protected $bestSellerService;
    protected $firebaseCartService;

    public function __construct(BestSellerService $bestSellerService, FirebaseCartService $firebaseCartService)
    {
        $this->bestSellerService = $bestSellerService;
        $this->firebaseCartService = $firebaseCartService;
    }

    public function index(Request $request)
    {
        $selectedTab = $request->get('tab', 'aktif');
        $user = session('firebase_user');
        $userId = $user['uid'] ?? 'anonymous';        
        
        $cartItems = $this->firebaseCartService->getCartItems($userId, $selectedTab);
        
        return view('cart.index', compact('cartItems', 'selectedTab'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'item_id' => 'required|string',
            'quantity' => 'required|integer|min:1'
        ]);

        $user = session('firebase_user');
        $userId = $user['uid'] ?? 'anonymous';        

        $items = $this->bestSellerService->getAll();
        $item = $items->firstWhere('id', $request->item_id);
        
        if (!$item) {
            return back()->withErrors('Item tidak ditemukan.');
        }

        $itemData = [
            'item_id' => $request->item_id,
            'name' => $item['name'],
            'price' => $item['price'],
            'image_res' => $item['image_res'],
            'quantity' => $request->quantity
        ];

        $success = $this->firebaseCartService->addCartItem($userId, $itemData);

        if (!$success) {
            return back()->withErrors('Gagal menambahkan ke keranjang.');
        }

        return back()->with('success', 'Item berhasil ditambahkan ke keranjang.');
    }

    public function cancelOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string'
        ]);

        $user = session('firebase_user');
        $userId = $user['uid'] ?? 'anonymous';        
        $orderId = $request->order_id;

        try {
            $this->firebaseCartService->moveCartItem(
                $orderId, 
                $userId, 
                'aktif', 
                'dibatalkan', 
                'Dibatalkan'
            );

            return response()->json(['success' => true, 'message' => 'Order berhasil dibatalkan']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function completeOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string'
        ]);

        $user = session('firebase_user');
        $userId = $user['uid'] ?? 'anonymous';
        $orderId = $request->order_id;

        try {
            $this->firebaseCartService->moveCartItem(
                $orderId, 
                $userId, 
                'aktif', 
                'selesai', 
                'Selesai'
            );

            return response()->json(['success' => true, 'message' => 'Order selesai']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getOrderStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string'
        ]);

        $user = session('firebase_user');
        $userId = $user['uid'] ?? 'anonymous';
        $orderId = $request->order_id;

        try {
            $order = $this->firebaseCartService->getOrderDetail($orderId, $userId, 'aktif');

            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Order tidak ditemukan']);
            }

            $status = $order['status'] ?? 'Status tidak tersedia';
            
            // Enhanced status messages based on current status
            $statusMessages = [
                'Sedang diproses' => 'Pesanan Anda sedang disiapkan oleh kitchen',
                'Dalam perjalanan' => 'Driver sedang dalam perjalanan menuju lokasi Anda',
                'Tiba di lokasi' => 'Driver telah sampai di lokasi. Silakan ambil pesanan Anda',
                'Selesai' => 'Pesanan telah selesai diterima'
            ];

            $message = $statusMessages[$status] ?? 'Status pengantaran sedang diperbarui';

            return response()->json([
                'success' => true,
                'status' => $status,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memuat status pengantaran']);
        }
    }
}