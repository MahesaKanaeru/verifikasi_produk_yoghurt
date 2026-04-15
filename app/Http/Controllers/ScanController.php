<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; 

class ScanController extends Controller
{
    public function index()
    {
        $products = Product::all(); 

        return view('welcome', compact('products'));
    }

    public function processScan(Request $request)
    {
        
    }
}