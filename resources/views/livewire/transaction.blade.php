<div>
    <!-- Add Product and Member ID Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-body">
                    <h5>Add Product and Member Information</h5>
                    <div>
                        @if (session()->has('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="product_id" class="form-label">Product</label>
                            <select class="form-control" wire:model="product_id">
                                <option value="">Select Product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }} - {{ ucfirst($product->discount_type) }}
                                        @if ($product->discount_type === 'percentage')
                                            ({{ $product->discount_value }}%)
                                        @endif
                                        @if ($product->event_discount !== 'none')
                                            - {{ ucfirst($product->event_discount) }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" wire:model="quantity" min="1">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="member_id" class="form-label">Member ID (Optional)</label>
                            <input type="text" class="form-control" wire:model="member_id">
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-primary" wire:click="addToCart">Add to Cart</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-body">
                    <h5>Cart</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price Before Discount</th>
                                    <th>Discount</th>
                                    <th>Discount (%)</th>
                                    <th>Total Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cart as $index => $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item['name'] }}</td>
                                        <td>{{ $item['quantity'] }}</td>
                                        <td>{{ number_format($item['price_before_discount'], 2, ',', '.') }}</td>
                                        <td>{{ $item['discount'] }}</td>
                                        <!-- Display event discount or other discount -->
                                        <td>{{ $item['discount_percentage'] }}%</td>
                                        <td>{{ number_format($item['total_price'], 2, ',', '.') }}</td>
                                        <td>
                                            <button class="btn btn-danger"
                                                wire:click="removeFromCart({{ $index }})">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-control" wire:model="payment_method">
                                <option value="cash">Cash</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="qris">qris</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <h5>Total Price: {{ number_format($this->cartTotalPrice, 2, ',', '.') }}</h5>
                    </div>
                    <button type="button" class="btn btn-success mt-2" wire:click="saveTransaction">Save
                        Transaction</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Receipt Section -->
    @if ($receiptData)
        <div id="receipt" style="display: none;">
            <h2>Transaction Receipt</h2>
            <p><strong>Transaction ID:</strong> {{ $receiptData['transaction_id'] }}</p>
            <p><strong>Member ID:</strong> {{ $receiptData['member_id'] ?? 'N/A' }}</p>
            <table border="1" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($receiptData['items'] as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>{{ number_format($item['price'], 2, ',', '.') }}</td>
                            <td>{{ number_format($item['total_price'], 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p><strong>Subtotal:</strong> Rp {{ number_format($receiptData['subtotal'], 2, ',', '.') }}</p>
            <p><strong>Tax ({{ $receiptData['tax_rate'] }}%):</strong> Rp
                {{ number_format($receiptData['tax_amount'], 2, ',', '.') }}</p>
            <p><strong>Total:</strong> Rp {{ number_format($receiptData['total_price'], 2, ',', '.') }}</p>
        </div>
    @endif
</div>
