<?php

namespace App\Http\Controllers\admin;
use App\Models\Category; 
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
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
    public function update($id, Request $request)
{
    $category = Category::find($id);

    if ($category == null) {
        return response()->json([
            'status' => false,
            'message' => 'Category not found.',
            'data' => []
        ], 404);
    }

    $validator = Validator::make($request->all(), [
        'name' => [
            'required',
            'min:3',
            Rule::unique('categories', 'name')->ignore($category->id),
        ],
        'status' => 'sometimes|nullable|in:active,block',
        'image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
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

    // Update category name
    $category->name = $request->name;

    // Update status only if provided
    if ($request->has('status')) {
        $category->status = $request->status;
    }

    // Upload new image and delete old image
    if ($request->hasFile('image')) {

        $image = $request->file('image');
        $uploadPath = public_path('uploads/category');

        // Create folder if it doesn't exist
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        // Delete old image if it exists
        if (!empty($category->image)) {
            $oldImagePath = $uploadPath . '/' . $category->image;

            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }
        }

        // Save new image
        $ext = $image->getClientOriginalExtension();
        $fileName = time() . '.' . $ext;

        $image->move($uploadPath, $fileName);

        $category->image = $fileName;
    }

    // Save updated category
    $category->save();

    return response()->json([
        'status' => true,
        'message' => 'Category updated successfully.',
        'data' => [
            'category' => $category
        ]
    ], 200);

}
    // delete category
    


// Delete category and its image
public function destroy($id)
{
    $category = Category::find($id);

    if ($category == null) {
        return response()->json([
            'status' => false,
            'message' => 'Category not found.',
            'data' => []
        ], 404);
    }

    // Delete category image from public/uploads/category
    if (!empty($category->image)) {
        $imagePath = public_path(
            'uploads/category/' . $category->image
        );

        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }
    }

    // Delete category from database
    $category->delete();

    return response()->json([
        'status' => true,
        'message' => 'Category deleted successfully.',
        'data' => []
    ], 200);
}

}