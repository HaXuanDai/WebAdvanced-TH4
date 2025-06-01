<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class MasterCategoryController extends Controller
{
    public function storecat(Request $request)
    {
        $validate_data = $request->validate([
            'category_name' => 'unique:categories|max:100',
        ]);

        Category::create($validate_data);

        return redirect()->back();
    }
}
