<div class="container">
    <h3>Manage Discounts</h3>

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <form wire:submit.prevent="saveDiscount">
        <div class="mb-3">
            <label for="name" class="form-label">Discount Name</label>
            <input type="text" class="form-control" wire:model="name">
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="buy_product_id" class="form-label">Buy Product</label>
            <select class="form-control" wire:model="buy_product_id">
                <option value="">Select Product</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
            @error('buy_product_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="buy_quantity" class="form-label">Buy Quantity</label>
            <input type="number" class="form-control" wire:model="buy_quantity" min="1">
            @error('buy_quantity')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="discount_type" class="form-label">Discount Type</label>
            <select id="discount_type" class="form-control" wire:model="discount_type"
                onchange="toggleDiscountFields()">
                <option id="buy_x_get_y" value="buy_x_get_y">Buy X Get Y</option>
                <option id="percentage" value="percentage">Percentage</option>
            </select>
            @error('discount_type')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3" id="free_product_section">
            <label for="free_product_id" class="form-label">Free Product</label>
            <select class="form-control" wire:model="free_product_id">
                <option value="">Select Product</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
            @error('free_product_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3" id="free_quantity_section">
            <label for="free_quantity" class="form-label">Free Quantity</label>
            <input type="number" class="form-control" wire:model="free_quantity" min="1">
            @error('free_quantity')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3" id="percentage_section" style="display: none;">
            <label for="discount_percentage" class="form-label">Discount Percentage</label>
            <input type="number" class="form-control" wire:model="discount_percentage" min="1" max="100">
            @error('discount_percentage')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            {{ $discountId ? 'Update Discount' : 'Save Discount' }}
        </button>
        <button type="button" class="btn btn-secondary" wire:click="resetForm">Cancel</button>
    </form>

    <h4 class="mt-5">Existing Discounts</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Buy Product</th>
                <th>Buy Quantity</th>
                <th>Free Product</th>
                <th>Free Quantity</th>
                <th>Discount Type</th>
                <th>Discount Percentage</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($discounts as $discount)
                <tr>
                    <td>{{ $discount->name }}</td>
                    <td>{{ $discount->buyProduct->name }}</td>
                    <td>{{ $discount->buy_quantity }}</td>
                    <td>{{ $discount->freeProduct->name ?? 'N/A' }}</td>
                    <td>{{ $discount->free_quantity ?? 'N/A' }}</td>
                    <td>{{ ucfirst($discount->discount_type) }}</td>
                    <td>{{ $discount->discount_percentage ?? 'N/A' }}</td>
                    <td>
                        <button wire:click="editDiscount({{ $discount->id }})"
                            class="btn btn-warning btn-sm">Edit</button>
                        <button wire:click="deleteDiscount({{ $discount->id }})"
                            class="btn btn-danger btn-sm">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    function toggleDiscountFields() {
        const discountType = document.getElementById('discount_type').value;
        const percentageSection = document.getElementById('percentage_section');
        const freeProductSection = document.getElementById('free_product_section');
        const freeQuantitySection = document.getElementById('free_quantity_section');

        if (discountType === 'percentage') {
            percentageSection.style.display = 'block';
            freeProductSection.style.display = 'none';
            freeQuantitySection.style.display = 'none';
        } else {
            percentageSection.style.display = 'none';
            freeProductSection.style.display = 'block';
            freeQuantitySection.style.display = 'block';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleDiscountFields();
    });
</script>
