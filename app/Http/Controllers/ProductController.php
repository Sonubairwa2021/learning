<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        // echo Helper::getStatus(0);
        //Helper::productQtyChange(1,289,'sub');
        // echo "<br>";
        // echo Helper::addCurrency(1000);
        // die;
        // Get latest products with pagination
        $products = Product::orderBy('id', 'asc')->paginate(20);
      
        $products = Product::expensive(1000)->paginate(20);

        // Send to view
        return view('products.index', compact('products'));
    }
}
