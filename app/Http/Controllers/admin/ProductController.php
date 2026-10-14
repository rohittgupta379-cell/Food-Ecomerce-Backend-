<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
class ProductController extends Controller
{
    // fetch api
    public function index(){
        $products = Product::orderBy('created_at','DESC')->get();
        return response()->json([
            'status' => true,
            'message' => 'Product Fetched successfully.',
            'data' => [
                'products' => $products
            ]
        ], 200);
        
    }

    //  store dish
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:0',
            'summary' => 'nullable|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:active,block',
            'is_featured' => 'required|in:yes,no'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please fix the validation errors.',
                'data' => [
                    'errors' => $validator->errors()
                ]
            ], 422);
        }

        // Upload image
        $imageName = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');

            $imageName = time() . '.' .
                        $image->getClientOriginalExtension();

            $image->move(
                public_path('uploads/products'),
                $imageName
            );
        }

        // Store product in database
        $product = new Product();
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->price = $request->price;
        $product->qty = $request->qty;
        $product->summary = $request->summary;
        $product->image = $imageName;
        $product->status = $request->status;
        $product->is_featured = $request->is_featured;
        $product->save();

        return response()->json([
            'status' => true,
            'message' => 'Product added successfully.',
            'data' => [
                'product' => $product
            ]
        ], 201);
    }

    // dish show by id
    public function show($id){
         $products = Product::find($id);
       
       if($products == null){
        return response()->json([
            'status' => false,
            'message' => 'Product not found.',
            'data' => []
        ], 404);
        
       }
       return response()->json([
            'status' => true,
            'message' => 'Product found.',
            'data' => [
                'products' => $products
            ]
        ], 200);
        
    }

    // update dish
    public function update($id, Request $request)
    {
        $product = Product::find($id);

        if ($product == null) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.',
                'data' => []
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:0',
            'summary' => 'nullable|string',
            'image' => 'sometimes|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:active,block',
            'is_featured' => 'required|in:yes,no'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please fix the validation errors.',
                'data' => [
                    'errors' => $validator->errors()
                ]
            ], 422);
        }

        // Upload new image if provided
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            // Delete old image
            if ($product->image) {
                $oldImagePath = public_path(
                    'uploads/products/' . $product->image
                );

                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            // Save new image
            $imageName = time() . '.' .
                $image->getClientOriginalExtension();

            $image->move(
                public_path('uploads/products'),
                $imageName
            );

            $product->image = $imageName;
        }

        // Update product details
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->price = $request->price;
        $product->qty = $request->qty;
        $product->summary = $request->input('summary');
        $product->status = $request->status;
        $product->is_featured = $request->is_featured;

        $product->save();

        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully.',
            'data' => [
                'product' => $product
            ]
        ], 200);
    }


    // delete dish
    public function destroy($id)
    {
        
     $products = Product::find($id);
       
       if($products == null){
        return response()->json([
            'status' => false,
            'message' => 'Product not found.',
            'data' => []
        ], 404);
       }

       $products->delete();
        return response()->json([
            'status' => true,
            'message' => 'Product Delete successfully.',
            'data' => []
        ], 200);  
    }
        
    }