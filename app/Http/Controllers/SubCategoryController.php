<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{


    public function displaySubCategory()
    {
        return SubCategory::all();
    }

    public function displaySpecificSubCategory($id)
    {
        return SubCategory::find($id);
    }

}