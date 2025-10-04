<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        // Get latest products with pagination
        $products = Product::active()->orderBy('id', 'desc')->paginate(20);
      
        $products = Product::active()->expensive(1000)->paginate(20);

        // Send to view
        return view('products.index', compact('products'));
    }
}
