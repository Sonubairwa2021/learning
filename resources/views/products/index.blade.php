<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <h1 class="mb-4 text-center">Product List</h1>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                {{-- <th>Thumbnail</th> --}}
                <th>Price</th>
                <th>Stock</th>
               
                <th>SKU</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    {{-- <td>
                        @if($product->thmnal)
                            <img src="{{ $product->thmnal }}" alt="thumbnail" class="img-thumbnail" width="80">
                        @else
                            <span class="text-muted">No Image</span>
                        @endif
                    </td> --}}
                    <td>{{ Helper::addCurrency($product->price) }}</td>
                   
                    <td>
                        {!! Helper::getOutofStockLabel($product->stock) !!}
                    </td>
                    <td>{{ $product->sku }}</td>
                    <td>
                        {!! Helper::getStatusLabel($product->status) !!}
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm buy-now-btn" 
                                data-product-id="{{ $product->id }}" 
                                data-amount="{{ $product->price }}">
                            Buy Now
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">No products found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $products->links() }}
    </div>
</div>

<!-- Bootstrap JS (optional for dropdowns, modals, etc.) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Razorpay Checkout Script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const buyButtons = document.querySelectorAll('.buy-now-btn');
    
    buyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const amount = this.getAttribute('data-amount');
            
            // Disable button to prevent multiple clicks
            this.disabled = true;
            this.textContent = 'Processing...';
            
            // Create order
            fetch('/payment/create-order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: productId,
                    amount: amount
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Initialize Razorpay checkout
                    const options = {
                        key: data.key,
                        amount: data.amount,
                        currency: 'INR',
                        name: 'Product Purchase',
                        description: data.product.name,
                        order_id: data.order_id,
                        handler: function(response) {
                            // Verify payment
                            verifyPayment(response, productId);
                        },
                        prefill: {
                            name: 'Customer Name',
                            email: 'customer@example.com',
                            contact: '9999999999'
                        },
                        theme: {
                            color: '#3399cc'
                        },
                        modal: {
                            ondismiss: function() {
                                // Re-enable button if payment cancelled
                                button.disabled = false;
                                button.textContent = 'Buy Now';
                            }
                        }
                    };
                    
                    const razorpay = new Razorpay(options);
                    razorpay.open();
                } else {
                    alert('Error: ' + data.message);
                    button.disabled = false;
                    button.textContent = 'Buy Now';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                button.disabled = false;
                button.textContent = 'Buy Now';
            });
        });
    });
    
    function verifyPayment(paymentResponse, productId) {
        fetch('/payment/verify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                razorpay_order_id: paymentResponse.razorpay_order_id,
                razorpay_payment_id: paymentResponse.razorpay_payment_id,
                razorpay_signature: paymentResponse.razorpay_signature,
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Payment successful! Payment ID: ' + data.payment_id);
                // You can redirect to success page or reload
                // window.location.href = '/payment/success';
            } else {
                alert('Payment verification failed: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error verifying payment. Please contact support.');
        });
    }
});
</script>
</body>
</html>
