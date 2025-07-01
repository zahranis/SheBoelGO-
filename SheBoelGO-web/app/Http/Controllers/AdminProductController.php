<?php

namespace App\Http\Controllers;

use App\Services\FirebaseItemService;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    private FirebaseItemService $firebase;

    public function __construct(FirebaseItemService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function create()
    {
        return view('admin.products.form', [
            'isEdit' => false,
            'product' => [],
        ]);
    }

    public function store(Request $request)
    {
        $this->firebase->createItem($request->all());
        return redirect()->route('main_admin');
    }

    public function delete()
    {
        $products = $this->firebase->getAllItems();
        return view('admin.products.delete', compact('products'));
    }

    public function destroy($id)
    {
        $this->firebase->deleteItem($id);
        return back();
    }

    public function edit()
    {
        $products = $this->firebase->getAllItems();
        return view('admin.products.edit', compact('products'));
    }

    public function editForm($id)
    {
        $product = $this->firebase->getItemById($id);
        return view('admin.products.form', [
            'isEdit' => true,
            'product' => $product,
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->firebase->updateItem($id, $request->all());
        return redirect()->route('main_admin');
    }
}
