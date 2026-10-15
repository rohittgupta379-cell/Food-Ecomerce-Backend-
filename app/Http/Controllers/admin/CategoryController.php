<?php

namespace App\Http\Controllers\admin;
use App\Models\Category; 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class CategoryController extends Controller
{
    // this method will show all data
    public function index(){
        $categories = Category::orderBy('created_at','DESC')->get();
        return response()->json([
            'status' => true,
            'message' => 'Category Fetched successfully.',
            'data' => [
                'categories' => $categories
            ]
        ], 200);
        
    }

    // store category
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|unique:categories,name',
            'status' => 'sometimes|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please fix the validation errors.',
                'data' => [
                    'errors' => $validator->errors()
                ]
            ], 400);
        }

        // Store the data
        $category = new Category();
        $category->name = $request->name;
        $category->status = $request->status;
        $category->save();
        
        if(!empty($request->image)){
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $fileName = time().'.'.$ext;
            $image->move(public_path('/uploads/category'),$fileName);
            $category->image = $fileName; // ✅ Correct
            $category->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Category added successfully.',
            'data' => [
                'category' => $category
            ]
        ], 200);
    }

    // show single category
    public function show($id){
       $category = Category::find($id);
       
       if($category == null){
        return response()->json([
            'status' => false,
            'message' => 'Category not found.',
            'data' => []
        ], 404);
        
       }
       return response()->json([
            'status' => true,
            'message' => 'Category found.',
            'data' => [
                'category' => $category
            ]
        ], 200);
        
        
    }

    // update category
    public function update($id, Request $request){
         $category = Category::find($id);
       
       if($category == null){
        return response()->json([
            'status' => false,
            'message' => 'Category not found.',
            'data' => []
        ], 404);
        
       }

       $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|unique:categories,name',
            'status' => 'sometimes|nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please fix the validation errors.',
                'data' => [
                    'errors' => $validator->errors()
                ]
            ], 400);
        }

        // Store the data
        
        $category->name = $request->name;
        $category->status = $request->status;
        $category->save();

         if(!empty($request->image)){
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $fileName = time().'.'.$ext;
            $image->move(public_path('/uploads/category'),$fileName);
            $category->image = $fileName();
            $category->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Category Update successfully.',
            'data' => [
                'category' => $category
            ]
        ], 200);
 
        
    }

    // delete category
    public function destroy($id){
     $category = Category::find($id);
       
       if($category == null){
        return response()->json([
            'status' => false,
            'message' => 'Category not found.',
            'data' => []
        ], 404);
       }

       $category->delete();
        return response()->json([
            'status' => true,
            'message' => 'Category Delete successfully.',
            'data' => []
        ], 200);  
    }
}