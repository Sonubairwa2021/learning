<?php

namespace App\Helpers;

use App\Models\Product;

class Helper
{
	public static function getStatus($status)
	{
		return $status ? 'Active' : 'Inactive';
	}
    public static function productQtyChange($productId,$qty,$action='add')
	{
		$product = Product::find($productId);
        // dd($product);
        // die;
		if($action == 'add') {
			$product->stock += $qty;
		} else {
			$product->stock -= $qty;
		}
		$product->save();
		return $product->stock;
	}
    public static function addCurrency($amount)
    {
        return '₹' . number_format($amount, 2);
    }
    public static function getStatusLabel($status)
    {
        return $status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
    }
    public static function getOutofStockLabel($stock)
    {
        if($stock <= 0) {
            return '<span class="badge bg-danger">'.$stock.'</span>';
        }
        if($stock <= 10) {
            return '<span class="badge bg-warning">'.$stock.'</span>';
        }
        if($stock <= 20) {
            return '<span class="badge bg-warning">'.$stock.'</span>';
        }
        return $stock;
    }
}