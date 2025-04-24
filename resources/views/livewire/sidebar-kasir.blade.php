<div class="sidebar pe-4 pb-3">
    <nav class="navbar navbar-light">
        <a href="#" class="navbar-brand mx-4 mb-3">
            <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>{{ config('app.name', 'DiscountHub') }}</h3>
        </a>
        <h5 class="text-primary mx-4">Special Offers</h5>
        <div class="product-list">
            @foreach ($products as $product)
                <div class="card mb-3 shadow-sm product-card" style="width: 100%; border-radius: 10px; cursor: pointer;"
                    data-product-code="{{ $product->product_code }}">
                    <div class="row g-0 align-items-center">
                        <!-- Image Section -->
                        <div class="col-4 d-flex justify-content-center align-items-center">
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded"
                                    alt="{{ $product->name }}" style="max-height: 80px; object-fit: contain;">
                            @else
                                <img src="{{ asset('images/default-product.png') }}" class="img-fluid rounded"
                                    alt="Default Image" style="max-height: 80px; object-fit: contain;">
                            @endif
                        </div>
                        <!-- Details Section -->
                        <div class="col-8">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1 text-truncate" style="font-size: 14px;">{{ $product->name }}
                                </h6>
                                <p class="card-text mb-1 text-muted" style="font-size: 12px;">Price: Rp
                                    {{ number_format($product->price, 2, ',', '.') }}</p>
                                @if ($product->discount)
                                    <p class="card-text text-success" style="font-size: 12px;">
                                        Discount:
                                        @if ($product->discount->discount_type === 'percentage')
                                            {{ $product->discount->discount_percentage }}% Off
                                            <br>
                                            <span class="text-muted" style="font-size: 11px;">
                                                Buy at least {{ $product->discount->buy_quantity }} to apply.
                                            </span>
                                        @elseif ($product->discount->discount_type === 'buy_x_get_y')
                                            Buy {{ $product->discount->buy_quantity }} Get
                                            {{ $product->discount->free_quantity }}
                                            <br>
                                            <span class="text-muted" style="font-size: 11px;">
                                                Buy {{ $product->discount->buy_quantity }} to get
                                                {{ $product->discount->free_quantity }} free.
                                            </span>
                                        @endif
                                    </p>
                                @else
                                    <p class="card-text text-muted" style="font-size: 12px;">No Discount</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </nav>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach(card => {
            card.addEventListener('click', function() {
                const productCode = this.getAttribute('data-product-code');
                const barcodeInput = document.getElementById('barcodeInput');
                const quantityInput = document.querySelector('input[wire\\:model="quantity"]');

                if (barcodeInput) {
                    barcodeInput.value = productCode;
                    barcodeInput.dispatchEvent(new Event(
                        'input')); // Trigger Livewire input binding

                    // Set quantity to 1 if not already set or invalid
                    if (quantityInput && (!quantityInput.value || quantityInput.value <= 0)) {
                        quantityInput.value = 1;
                        quantityInput.dispatchEvent(new Event(
                            'input')); // Trigger Livewire input binding
                    }

                    const addToCartButton = document.querySelector(
                        '[wire\\:click="addToCart"]');
                    if (addToCartButton) {
                        addToCartButton.click(); // Simulate clicking the "Add to Cart" button
                    }
                }
            });
        });
    });
</script>
