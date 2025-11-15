<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    /**
     * Create Razorpay order
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'amount' => 'required|numeric|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        $amount = $request->amount * 100; // Convert to paise

        // Razorpay API credentials
        $keyId = env('RAZORPAY_KEY');
        $keySecret = env('RAZORPAY_SECRET');

        if (!$keyId || !$keySecret) {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay credentials not configured'
            ], 400);
        }

        // Create order via Razorpay API
        $response = Http::withBasicAuth($keyId, $keySecret)
            ->post('https://api.razorpay.com/v1/orders', [
                'amount' => $amount,
                'currency' => 'INR',
                'receipt' => 'receipt_' . $product->id . '_' . time(),
                'notes' => [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                ]
            ]);

        if ($response->successful()) {
            $orderData = $response->json();
            
            return response()->json([
                'success' => true,
                'order_id' => $orderData['id'],
                'amount' => $orderData['amount'],
                'key' => $keyId,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to create order'
        ], 400);
    }

    /**
     * Verify payment signature
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
            'product_id' => 'required|exists:products,id'
        ]);

        $keySecret = env('RAZORPAY_SECRET');
        
        $signature = hash_hmac('sha256', 
            $request->razorpay_order_id . '|' . $request->razorpay_payment_id, 
            $keySecret
        );

        if ($signature === $request->razorpay_signature) {
            // Payment verified successfully
            $product = Product::findOrFail($request->product_id);
            
            // Here you can save payment details to database, update order status, etc.
            
            return response()->json([
                'success' => true,
                'message' => 'Payment successful!',
                'payment_id' => $request->razorpay_payment_id,
                'order_id' => $request->razorpay_order_id
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment verification failed'
        ], 400);
    }
}

