<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\ProductColor;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Color;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;

class ProductController extends Controller
{
    //add product
    public function addProducts(Request $request)
    {
        $formFormat = $request->input('formFormat');
        $colors = $request->input('color');
        $sizes = $request->input('sizes');
        $images = $request->file('images');

        if ($formFormat == 'form1') {
            // Validate the form data
            $validator = Validator::make($request->all(), [
                'productName' => 'required',
                'price' => 'required',
                'description' => 'required',
                'selectedCategoryOption' => 'required',
                'selectedSubCategoryOption'=>'required',
                'color.*' => 'required',
                'sizes.*' => 'required',
              
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $product = new Product([
                'name' => $request->input('productName'),
                'price' => $request->input('price'),
                'category_id' => $request->input('selectedCategoryOption'),
                'sub_category_id' => $request->input('selectedSubCategoryOption'),
                'description' => $request->input('description'),
               
            ]);

            if (!$product->save()) {
                return response()->json(['error' => 'Failed to save the product. Please try again.'], 500);
            }

            // Save colors
            $colors = $request->input('color');
            foreach ($colors as $colorChoice) {
                $color = $product->colors()->create([
                    'color_id' => $colorChoice,
                ]);

                // Save sizes
                $sizes = $request->input('sizes.' . $colorChoice, []);
                foreach ($sizes as $size) {
                    $color->sizes()->create([
                        'size' => $size,
                    ]);
                }

                // Save images
                $images = $request->file('images.' . $colorChoice, []);
                foreach ($images as $image) {
                    if ($image instanceof UploadedFile) {
                        $imagePath = $image->store('productImages');
                        $color->images()->create([
                            'image' => $imagePath,
                        ]);
                    } else {
                        return response()->json(['error' => 'Invalid image data.'], 422);
                    }
                }
            }

            return response()->json(['message' => 'Product added successfully'], 200);
        } elseif ($formFormat == 'form2') {
            $validator = Validator::make($request->all(), [
                'productName' => 'required',
                'price' => 'required',
                'description' => 'required',
                'selectedCategoryOption' => 'required',
                'selectedSubCategoryOption'=>'required',
                'sizeInput' => 'required',
                'imageInput' => 'required',
              
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $product = new Product([
                'name' => $request->input('productName'),
                'price' => $request->input('price'),
                'category_id' => $request->input('selectedCategoryOption'),
                'sub_category_id' => $request->input('selectedSubCategoryOption'),
                'description' => $request->input('description'),
                'is_recommended' => $request->input('is_recommended'),
            ]);

            if (!$product->save()) {
                return response()->json(['error' => 'Failed to save the product. Please try again.'], 500);
            }

            // Save sizes
            $sizes = $request->input('sizeInput');
            if (!is_array($sizes)) {
                return response()->json(['error' => $sizes], 422);
            }

            foreach ($sizes as $size) {
                $product->sizes()->create([
                    'size' => $size,
                ]);
            }

            // Save images
            $images = $request->file('imageInput');
            foreach ($images as $image) {
                if ($image instanceof UploadedFile) {
                    $imagePath = $image->store('productImages');
                    $product->images()->create([
                        'image' => $imagePath,
                    ]);
                } else {
                    return response()->json(['error' => 'Invalid image data.'], 422);
                }
            }

            return response()->json(['message' => 'Product added successfully'], 200);
        } else {
            return response()->json(['message' => 'Invalid form format!!'], 422);
        }
    }



    public function displayAllProduct()
    {
        $products = Product::all();
        return $products;
    }


    public function displayProduct()
    {
        $products = Product::all();

        $result = [];
        foreach ($products as $product) {
            $product->images;
            $product->sizes;

            $colors = $product->colors;
            $colorData = [];
            if(isset($colors)) {
                foreach ($colors as $color) {
                    $color->images->pluck('image')->toArray();
                    $color->sizes->pluck('size')->toArray();

                    $colorData[] = [
                        'colorName' =>  Color::find($color->color_id)->color,

                    ];
                }

            }

            $result[] = [
                'product' => $product,
                'colorData' => $colorData,

            ];
        }

        return response()->json($result);
        //return ("hi");
    }

    public function displaySpecificProduct($id)
    {
        $productId = $id;
        $product = Product::find($productId);

        if ($product) {
            $product->images;
            $product->sizes;

            $colors = $product->colors;
            $colorData = [];
            if (isset($colors)) {
                foreach ($colors as $color) {
                    $color->images->pluck('image')->toArray();
                    $color->sizes->pluck('size')->toArray();

                    $colorData[] = [
                        'colorName' => Color::find($color->color_id)->color,
                        'color_id' => $color->color_id,
                    ];
                }
            }

            $result = [
                'product' => $product,
                'colorData' => $colorData,
            ];

            return response()->json($result);
        } else {
            return response()->json(['error' => 'Product not found'], 404);
        }
    }

    public function displaySpecificCategoryProducts($id)
    {
        $products = Product::where('categoryId', $id)->get();

        $result = [];
        foreach ($products as $product) {
            $product->images;
            $product->sizes;
            $product->category->category;

            $colors = $product->colors;
            $colorData = [];
            if(isset($colors)) {
                foreach ($colors as $color) {
                    $color->images->pluck('image')->toArray();
                    $color->sizes->pluck('size')->toArray();

                    $colorData[] = [
                        'colorName' => Color::find($color->color_id)->color,

                    ];

                }

            }

            $result[] = [
                'product' => $product,
                'colorData' => $colorData,


            ];
        }

        return response()->json($result);
    }
    public function updateProduct1(Request $request, $id)
    {
        
        $isPublishes = $request->input('SizePublished');
        
        $validator = Validator::make($request->all(), [
            'productName' => 'required',
            'price' => 'required',
            'description' => 'required',
            'selectedCategoryOption' => 'required',
            'selectedSubCategoryOption'=>'required',
          
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $isPublishes], 422);
        }

        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found.'], 404);
        }

        $product->name = $request->input('productName');
        $product->price = $request->input('price');
        $product->category_id = $request->input('selectedCategoryOption');
        $product->sub_category_id = $request->input('selectedSubCategoryOption');
        $product->description = $request->input('description');
  
        // Check if 'isPublished' is provided and update the value
        if ($request->filled('published')) {
            $product->published = $request->input('published');
        }

        if (!$product->save()) {
            return response()->json(['error' => 'Failed to update the product. Please try again.'], 500);
        }

        $sizes = $request->input('sizeInput');
        if (is_array($sizes) && !empty(array_filter($sizes))) {
            // Update or create sizes
            foreach ($sizes as $size) {
                if (!empty($size)) {
                    $product->sizes()->updateOrCreate(['size' => $size]);
                }
            }
        }

        $sizes = $product->sizes()->get();
        foreach ($sizes as $index => $size) {
            $isPublish = isset($isPublishes[$index]) ? $isPublishes[$index] : true;
            $size->update(['published' => $isPublish]);
        }

        $selectedImages = $request->file('selectedImages');
        $selectedIndexes = $request->input('selectedIndexes');
        $product = Product::find($id);

        if (is_array($selectedImages) && !empty($selectedImages)) {
            $imagesFromDatabase = $product->images()->get()->toArray();

            foreach ($selectedIndexes as $index => $selectedIndex) {
                // Validate that the index is within the range of imagesFromDatabase
                if ($selectedIndex >= 0 && $selectedIndex < count($imagesFromDatabase)) {
                    $selectedImage = $selectedImages[$index];

                    if ($selectedImage !== null && $selectedImage instanceof UploadedFile && $selectedImage->isValid()) {
                        $imagePath = $selectedImage->store('productImages');
                        $existingImageModel = $product->images()->find($imagesFromDatabase[$selectedIndex]['id']);

                        if ($existingImageModel) {
                            // Update the existing image with the new file path
                            $existingImageModel->update(['image' => $imagePath]);
                        }
                    }
                } else {
                    // Handle the case where the selected index is out of range
                    // You can choose to ignore or log the issue, or handle it accordingly
                }
            }
        }

        //add new image
        $images = $request->file('imageInput');
        if (is_array($images) && !empty(array_filter($images))) {
            foreach ($images as $image) {
                if ($image instanceof UploadedFile) {
                    $imagePath = $image->store('productImages');
                    $product->images()->create([
                        'image' => $imagePath,
                    ]);
                } else {
                    return response()->json(['error' => 'Invalid image data.'], 422);
                }
            }
        }

        return response()->json(['message' => "you successfully updated your product!!!"], 200);
    }


    public function updateProduct2(Request $request, $productId)
    {
        $colors = $request->input('color');
        $sizes = $request->input('sizes');
        $images = $request->file('images');
        $isColorPublished = $request->input('isColorPublished');
        $publishedSizes = $request->input('isSizePublished');
        $newColors = $request->input('newColor');
        $newSizes = $request->input('newSizes');
        $newImages = $request->file('newImages');
    
        // Validate the form data
        $validator = Validator::make($request->all(), [
            'productName' => 'required',
            'price' => 'required',
            'description' => 'required',
            'selectedCategoryOption' => 'required',
            'selectedSubCategoryOption'=>'required',
          
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $product = Product::find($productId);
    
        if (!$product) {
            return response()->json(['error' => 'Product not found.'], 404);
        }
    
        $product->name = $request->input('productName');
        $product->price = $request->input('price');
        $product->category_id = $request->input('selectedCategoryOption');
        $product->sub_category_id = $request->input('selectedSubCategoryOption');
        $product->description = $request->input('description');
     
        // Check if 'isPublished' is provided and update the value
        if ($request->filled('published')) {
            $product->published = $request->input('published');
        }
    
        if (!$product->save()) {
            return response()->json(['error' => 'Failed to update the product. Please try again.'], 500);
        }
    
        foreach ($colors as $colorIndex => $colorChoice) {
            // Check if the color exists
            $color = $product->colors()->where('color_id', $colorChoice)->first();
            
            if ($color) {
                // Save changes to the database
                $color->published = $isColorPublished[$colorIndex];
                $color->save();
                // Update sizes for the color
                $existingSizes = $color->sizes;
        
                if (isset($sizes[$colorIndex]) && is_array($sizes[$colorIndex])) {
                    $sizeData = $sizes[$colorIndex];
                    foreach ($sizeData as $sizeIndex => $size) {
                        $sizeModel = $existingSizes->firstWhere('size', $size);
                
                        // Assuming $publishedSizes is the array of published values for sizes
                        $publishedValues = $publishedSizes[$colorIndex] ?? null;
                
                        if ($sizeModel) {
                            // Update existing size
                            $sizeModel->size = $size;
                
                            // Check if the key exists before accessing it
                            if (isset($publishedValues[$sizeIndex])) {
                                $sizeModel->published = $publishedValues[$sizeIndex] ?? 0; // Set 'published' value or default to 0
                            }
                
                            $sizeModel->save();
                        } else {
                            // Create new size
                            $color->sizes()->create([
                                'size' => $size,
                
                                // Check if the key exists before accessing it
                                'published' => $publishedValues[$sizeIndex] ?? 0, // Set 'published' value or default to 0
                            ]);
                        }
                    }
                }
                
                
        
                // Update images for the color
                if (isset($images[$colorIndex]) && is_array($images[$colorIndex])) {
                    $existingImages = $color->images;
        
                    foreach ($images[$colorIndex] as $imageIndex => $image) {
                        if ($image instanceof UploadedFile) {
                            $imagePath = $image->store('productImages');
        
                            if ($existingImages->count() > $imageIndex) {
                                // Update existing image
                                $existingImages[$imageIndex]->image = $imagePath;
                                $existingImages[$imageIndex]->save();
                            } else {
                                // Create new image
                                $color->images()->create([
                                    'image' => $imagePath,
                                ]);
                            }
                        }
                    }
                }
            }
        }
    
        // Add new colors, images, and sizes
        if (!empty($newColors)) {
               // Save colors
               foreach ($newColors as $colorChoice) {
                   $color = $product->colors()->create([
                       'color_id' => $colorChoice,
                   ]);
   
                   // Save sizes
                   $newSizes = $request->input('newSizes.' . $colorChoice, []);
                   foreach ($newSizes as $size) {
                       $color->sizes()->create([
                           'size' => $size,
                       ]);
                   }
   
                   // Save images
                   $newImages = $request->file('newImages.' . $colorChoice, []);
                   foreach ($newImages as $image) {
                       if ($image instanceof UploadedFile) {
                           $imagePath = $image->store('productImages');
                           $color->images()->create([
                               'image' => $imagePath,
                           ]);
                       } else {
                           return response()->json(['error' => 'Invalid image data.'], 422);
                       }
                   }
               }
        }
    
        return response()->json(['message' => 'Product updated successfully'], 200);
    }


    function searchProduct($key) {
        return Product::where('name', 'LIKE', '%' . $key . '%')->get();
    }
    public function SearchProductForProductTable($key = null)
{
    $query = Product::query();

    // If $key is provided, filter products based on the searchProduct function
    if ($key) {
        $searchedProducts = $this->searchProduct($key);
        $productIds = $searchedProducts->pluck('id');
        $query->whereIn('id', $productIds);
    }

    $products = $query->get();

    $result = [];
    foreach ($products as $product) {
        $product->images;
        $product->sizes;

        $colors = $product->colors;
        $colorData = [];
        if (isset($colors)) {
            foreach ($colors as $color) {
                $color->images->pluck('image')->toArray();
                $color->sizes->pluck('size')->toArray();

                $colorData[] = [
                    'colorName' => Color::find($color->color_id)->color,

                ];
            }
        }

        $result[] = [
            'product' => $product,
            'colorData' => $colorData,
        ];
    }

    return response()->json($result);
}




public function displayRecentProduct()
{
    // Fetch products created within the last 30 days
    $recentProducts = Product::where('created_at', '>=', Carbon::now()->subDays(30))->get();

    $result = [];
    foreach ($recentProducts as $product) {
        $product->images;
        $product->sizes;

        $colors = $product->colors;
        $colorData = [];
        if (isset($colors)) {
            foreach ($colors as $color) {
                $color->images->pluck('image')->toArray();
                $color->sizes->pluck('size')->toArray();

                $colorData[] = [
                    'colorName' => Color::find($color->color_id)->color,
                ];
            }
        }

        $result[] = [
            'product' => $product,
            'colorData' => $colorData,
        ];
    }

    return response()->json($result);
}



public function displayMenProduct()
{
    $products = Product::where('sub_category_id', 1)->get();

    $result = [];
    foreach ($products as $product) {
        $product->images;
        $product->sizes;

        $colors = $product->colors;
        $colorData = [];
        if(isset($colors)) {
            foreach ($colors as $color) {
                $color->images->pluck('image')->toArray();
                $color->sizes->pluck('size')->toArray();

                $colorData[] = [
                    'colorName' => Color::find($color->color_id)->color,
                ];
            }
        }

        $result[] = [
            'product' => $product,
            'colorData' => $colorData,
        ];
    }

    return response()->json($result);
}


public function displayWomenProduct()
{
    $products = Product::where('sub_category_id', 2)->get();

    $result = [];
    foreach ($products as $product) {
        $product->images;
        $product->sizes;

        $colors = $product->colors;
        $colorData = [];
        if(isset($colors)) {
            foreach ($colors as $color) {
                $color->images->pluck('image')->toArray();
                $color->sizes->pluck('size')->toArray();

                $colorData[] = [
                    'colorName' => Color::find($color->color_id)->color,
                ];
            }
        }

        $result[] = [
            'product' => $product,
            'colorData' => $colorData,
        ];
    }

    return response()->json($result);
}



public function displayAccessoryProduct()
{
    $products = Product::where('sub_category_id', 3)->get();

    $result = [];
    foreach ($products as $product) {
        $product->images;
        $product->sizes;

        $colors = $product->colors;
        $colorData = [];
        if(isset($colors)) {
            foreach ($colors as $color) {
                $color->images->pluck('image')->toArray();
                $color->sizes->pluck('size')->toArray();

                $colorData[] = [
                    'colorName' => Color::find($color->color_id)->color,
                ];
            }
        }

        $result[] = [
            'product' => $product,
            'colorData' => $colorData,
        ];
    }

    return response()->json($result);
}



public function displayNutritionProduct()
{
    $products = Product::where('sub_category_id', 4)->get();

    $result = [];
    foreach ($products as $product) {
        $product->images;
        $product->sizes;

        $colors = $product->colors;
        $colorData = [];
        if(isset($colors)) {
            foreach ($colors as $color) {
                $color->images->pluck('image')->toArray();
                $color->sizes->pluck('size')->toArray();

                $colorData[] = [
                    'colorName' => Color::find($color->color_id)->color,
                ];
            }
        }

        $result[] = [
            'product' => $product,
            'colorData' => $colorData,
        ];
    }

    return response()->json($result);
}

public function showMenCategoryWithSpecificSubCategory($id)
{
  
    $products = Product::where('sub_category_id', 1)->where('category_id', $id)->get();
   

    $result = [];
    foreach ($products as $product) {
        $product->images;
        $product->sizes;

        $colors = $product->colors;
        $colorData = [];
        if(isset($colors)) {
            foreach ($colors as $color) {
                $color->images->pluck('image')->toArray();
                $color->sizes->pluck('size')->toArray();

                $colorData[] = [
                    'colorName' => Color::find($color->color_id)->color,
                ];
            }
        }

        $result[] = [
            'product' => $product,
            'colorData' => $colorData,
        ];
    }

    return response()->json($result);
}

public function showWomenCategoryWithSpecificSubCategory($id)
{
  
    $products = Product::where('sub_category_id', 2)->where('category_id', $id)->get();
   

    $result = [];
    foreach ($products as $product) {
        $product->images;
        $product->sizes;

        $colors = $product->colors;
        $colorData = [];
        if(isset($colors)) {
            foreach ($colors as $color) {
                $color->images->pluck('image')->toArray();
                $color->sizes->pluck('size')->toArray();

                $colorData[] = [
                    'colorName' => Color::find($color->color_id)->color,
                ];
            }
        }

        $result[] = [
            'product' => $product,
            'colorData' => $colorData,
        ];
    }

    return response()->json($result);
}

public function showAccessoryCategoryWithSpecificSubCategory($id)
{
  
    $products = Product::where('sub_category_id', 3)->where('category_id', $id)->get();
   

    $result = [];
    foreach ($products as $product) {
        $product->images;
        $product->sizes;

        $colors = $product->colors;
        $colorData = [];
        if(isset($colors)) {
            foreach ($colors as $color) {
                $color->images->pluck('image')->toArray();
                $color->sizes->pluck('size')->toArray();

                $colorData[] = [
                    'colorName' => Color::find($color->color_id)->color,
                ];
            }
        }

        $result[] = [
            'product' => $product,
            'colorData' => $colorData,
        ];
    }

    return response()->json($result);
}

public function showNutritionCategoryWithSpecificSubCategory($id)
{
  
    $products = Product::where('sub_category_id', 4)->where('category_id', $id)->get();
   

    $result = [];
    foreach ($products as $product) {
        $product->images;
        $product->sizes;

        $colors = $product->colors;
        $colorData = [];
        if(isset($colors)) {
            foreach ($colors as $color) {
                $color->images->pluck('image')->toArray();
                $color->sizes->pluck('size')->toArray();

                $colorData[] = [
                    'colorName' => Color::find($color->color_id)->color,
                ];
            }
        }

        $result[] = [
            'product' => $product,
            'colorData' => $colorData,
        ];
    }

    return response()->json($result);
}



    }

    




       
   


