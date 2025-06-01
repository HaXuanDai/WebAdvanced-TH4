<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    public function index()
    {
        return view('admin.productattribute.create');
    }

    public function manage()
    {
        return view('admin.productattribute.manage');
    }
}
