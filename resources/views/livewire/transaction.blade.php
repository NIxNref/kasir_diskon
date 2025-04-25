<div>
    <div class="row mb-4">
        <div class="col-4">
            <div class="card border-primary">
                <div class="card-body">
                    <h5>Scan Barcode</h5>
                    <div id="scanner-container"
                        style="width: 100%; height: 150px; border: 1px solid #ccc; position: relative;">
                        <video id="scanner-video" style="width: 100%; height: 100%;"></video>
                        <div id="scanner-overlay"
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); display: none;">
                            <p style="color: white; text-align: center; margin-top: 50%;">Scanning...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-body">
                    <h5>Add Product</h5>
                    <div>
                        @if (session()->has('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="product_code" class="form-label">Product Code</label>
                            <input type="text" id="barcodeInput" class="form-control" wire:model="product_code"
                                placeholder="Enter Product Code">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" wire:model="quantity" min="1"
                                value="1">
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-primary" wire:click="addToCart">Add to Cart</button>
                    </div>
                    @if ($availableDiscount)
                        <div class="alert alert-info mt-3">
                            <strong>Available Discount:</strong> {{ $availableDiscount }}
                        </div>
                    @endif
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
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Discount</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cart as $index => $item)
                                    <tr>
                                        <td>{{ $item['name'] }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <button class="btn btn-sm btn-outline-secondary me-2"
                                                    wire:click="decreaseQuantity({{ $index }})">
                                                    -
                                                </button>
                                                <span>{{ $item['quantity'] }}</span>
                                                <button class="btn btn-sm btn-outline-secondary ms-2"
                                                    wire:click="increaseQuantity({{ $index }})">
                                                    +
                                                </button>
                                            </div>
                                        </td>
                                        <td>Rp {{ number_format($item['price'], 2, ',', '.') }}</td>
                                        <td>{{ $item['discount'] }}</td>
                                        <td>Rp {{ number_format($item['total_price'], 2, ',', '.') }}</td>
                                        <td>
                                            <button class="btn btn-danger btn-sm"
                                                wire:click="removeFromCart({{ $index }})">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="member_id" class="form-label">Member ID (Optional)</label>
                            <input type="text" class="form-control" wire:model="member_id">
                        </div>
                        <div class="col-md-6">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-control" wire:model="payment_method">
                                <option value="cash">Cash</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="qris">QRIS</option>
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

    <div class="text-end mt-3">
        <button class="btn btn-primary" wire:click="previewTransaction" data-bs-toggle="modal"
            data-bs-target="#transactionPreviewModal">
            Preview Transaction
        </button>
    </div>

    <!-- Transaction Preview Modal -->
    <div class="modal fade" id="transactionPreviewModal" tabindex="-1" aria-labelledby="transactionPreviewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transactionPreviewModalLabel">Transaction Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($receiptData)
                        <p><strong>Transaction Code:</strong> {{ $receiptData['transaction_code'] }}</p>
                        <p><strong>Subtotal:</strong> Rp {{ number_format($receiptData['subtotal'], 0, ',', '.') }}</p>
                        <p><strong>Tax ({{ $taxRate }}%):</strong> Rp
                            {{ number_format($receiptData['tax_amount'], 0, ',', '.') }}</p>
                        <p><strong>Total Price:</strong> Rp
                            {{ number_format($receiptData['total_price'], 0, ',', '.') }}</p>
                        <p><strong>Payment Method:</strong> {{ ucfirst($receiptData['payment_method']) }}</p>

                        <table class="table table-bordered mt-3">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Discount</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($receiptData['cart'] as $item)
                                    <tr>
                                        <td>{{ $item['name'] }}</td>
                                        <td>{{ $item['quantity'] }}</td>
                                        <td>Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                                        <td>{{ $item['discount'] }}</td>
                                        <td>Rp {{ number_format($item['total_price'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" wire:click="saveTransaction" data-bs-dismiss="modal">Save
                        Transaction</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Load QuaggaJS -->
<script src="https://cdn.jsdelivr.net/npm/quagga2@1.2.6/dist/quagga.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const barcodeInput = document.getElementById('barcodeInput');
        const quantityInput = document.querySelector('input[wire\\:model="quantity"]');
        let debounceTimeout = null; // Variable to store the debounce timeout
        barcodeInput.focus();

        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.querySelector('#scanner-container'),
                constraints: {
                    facingMode: {
                        ideal: "environment"
                    }
                }
            },
            decoder: {
                readers: ["code_128_reader", "ean_reader", "ean_8_reader", "upc_reader"]
            }
        }, function(err) {
            if (err) {
                console.error("Quagga init error:", err);
                return;
            }
            Quagga.start();
        });

        Quagga.onDetected(function(data) {
            const barcode = data.codeResult.code;

            // Debounce to prevent spamming addToCart
            if (debounceTimeout) {
                clearTimeout(debounceTimeout);
            }

            debounceTimeout = setTimeout(() => {
                // Update the product_code property in Livewire
                @this.set('product_code', barcode);

                // Set quantity to 1 if not already set
                if (!quantityInput.value || quantityInput.value <= 0) {
                    quantityInput.value = 1;
                    quantityInput.dispatchEvent(new Event('input'));
                }

                // Call the addToCart method in Livewire
                @this.call('addToCart');

                // Clear the input field after a short delay to allow for continuous scanning
                setTimeout(() => {
                    barcodeInput.value = '';
                    barcodeInput.dispatchEvent(new Event('input'));
                }, 500);
            }, 500); // Debounce delay of 500ms
        });
    });
</script>
