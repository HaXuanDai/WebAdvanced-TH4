<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() {
        return view('admin.category.create');
    }

    public function manage() {
        return view('admin.category.manage');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|unique:categories|max:100',
        ]);
        \App\Models\Category::create($validated);
        return redirect()->route('category.manage')->with('success', 'Category created!');
    }
}
