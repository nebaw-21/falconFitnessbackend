<?php

namespace App\Http\Controllers;
use App\Models\color;
use Faker\Core\Color as CoreColor;
use Faker\Provider\ar_EG\Color as Ar_EGColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ColorController extends Controller
{
    public function addColor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'color' => 'required|string',
           
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $color = new Color();
        $color->color = $request->input('color');
        $color->save();

        return response()->json(['success'], 200);
    }

    function displayColor(){
        return Color::all();
    
    }

    public function updateColor(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'color' => 'required|string',
    
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
         $color = Color::find($id);
        if (!$color) {
            return response()->json(['error' => 'Category not found'], 404);
        }
    
        $color->color = $request->input('color');
    
        $color->save();
    
        return response()->json(['success'], 200);
    }


    public function displaySpecificColor($id)
    {
        return color::find($id);
    }

    
}
