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
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No products found</td>
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
</body>
</html>
