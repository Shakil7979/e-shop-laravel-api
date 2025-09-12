<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // সব প্রোডাক্ট দেখাবে
    public function index()
    {
        return Product::all();
    }

    // নতুন প্রোডাক্ট তৈরি
    public function store(Request $request)
    { 
        // Validation
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        // Image upload (if exists)
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public'); 
            $validated['image'] = $imagePath;
        }

        // Create product
        $product = Product::create($validated);

        return response()->json([
            'message' => '✅ Product created successfully!',
            'data'    => $product,
        ], 201);
    }


    // একক প্রোডাক্ট দেখা
    public function show(Product $product)
    {
        return $product;
    }

    // আপডেট
    public function update(Request $request, Product $product)
    {
        // Validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && file_exists(storage_path('app/public/' . $product->image))) {
                unlink(storage_path('app/public/' . $product->image));
            }

            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        // Update product
        $product->update($validated);

        return response()->json($product);
    }


    // ডিলিট
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted']);
    }
}
