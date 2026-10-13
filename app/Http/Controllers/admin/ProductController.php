<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Products; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class ProductController extends Controller
{
    public function index(){
        
    }


    // store product in db
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'category_id' =>'required',
            'price' => 'required',
            'qty' => 'required',
            'image' => 'required',
            'status' => 'required',
            
        ]);
        
        
    }


    public function show(){
        
    }


    public function update(){
        
    }


    public function destroy(){
        
    }
}