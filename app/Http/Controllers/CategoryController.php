<?php

namespace App\Http\Controllers;

use App\Models\Category;
use APP\Models\Product;
use APP\Models\Color; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function addCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|string',
   
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $request->all()], 422);
        }

        $category = new Category();
        $category->category = $request->input('category');
        $category->save();

        return response()->json(['success'], 200);
    }
    
    public function updateCategory(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required',
       
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }
    
        $category->category = $request->input('category');
    
        $category->save();
    
        return response()->json(['success'], 200);
    }

    public function displayCategory()
    {
        return Category::all();
    }

    public function displaySpecificCategory($id)
    {
        return Category::find($id);
    }

    public function showCategoriesWithProductsForMen()
    {
        $categories = Category::with(['products' => function ($query) {
            // Filter products where sub_category_id is 2 (for women)
            $query->where('sub_category_id', 1);
        }])->get();
        
        // Filter out categories without products
        $categories = $categories->filter(function ($category) {
            return $category->products->isNotEmpty();
        });
    
        // Convert collection to array and reset keys
        $categories = $categories->values()->toArray();
    
        return $categories;
    }

    

    public function showCategoriesWithProductsForWomen()
    {
        $categories = Category::with(['products' => function ($query) {
            // Filter products where sub_category_id is 2 (for women)
            $query->where('sub_category_id', 2);
        }])->get();
        
        // Filter out categories without products
        $categories = $categories->filter(function ($category) {
            return $category->products->isNotEmpty();
        });
    
        // Convert collection to array and reset keys
        $categories = $categories->values()->toArray();
    
        return $categories;
    }

    
    public function showCategoriesWithProductsForNutrition()
    {
        $categories = Category::with(['products' => function ($query) {
            // Filter products where sub_category_id is 2 (for women)
            $query->where('sub_category_id', 4);
        }])->get();
        
        // Filter out categories without products
        $categories = $categories->filter(function ($category) {
            return $category->products->isNotEmpty();
        });
    
        // Convert collection to array and reset keys
        $categories = $categories->values()->toArray();
    
        return $categories;
    }
    
 

    public function showCategoriesWithProductsForAccessory()
    {
        $categories = Category::with(['products' => function ($query) {
            // Filter products where sub_category_id is 2 (for women)
            $query->where('sub_category_id', 3);
        }])->get();
        
        // Filter out categories without products
        $categories = $categories->filter(function ($category) {
            return $category->products->isNotEmpty();
        });
    
        // Convert collection to array and reset keys
        $categories = $categories->values()->toArray();
    
        return $categories;
    }


    public function showCategoriesWithProductsForAll($id)
    {
        $categories = Category::with(['products' => function ($query) use ($id) {
            // Filter products where sub_category_id matches the provided $id
            $query->where('sub_category_id', $id);
        }])->get();
        
        // Filter out categories without products
        $categories = $categories->filter(function ($category) {
            return $category->products->isNotEmpty();
        });
    
        // Convert collection to array and reset keys
        $categories = $categories->values()->toArray();
    
        return $categories;
    }
    
   

}