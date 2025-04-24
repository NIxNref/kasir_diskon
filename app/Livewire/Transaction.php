<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Products;
use App\Models\Discounts;
use App\Models\Transactions;

class Transaction extends Component
{
    public $member_id, $product_code, $quantity, $cart = [];
    public $taxRate = 10; // Tax rate in percentage
    public $receiptData = null;
    public $payment_method = 'cash'; // Default payment method
    public $availableDiscount = null; // To store the available discount for the product

    public function addToCart()
    {
        if (!$this->product_code) {
            session()->flash('error', 'Please scan or enter a product code.');
            return;
        }

        if (!$this->quantity || $this->quantity < 1) {
            session()->flash('error', 'Quantity must be at least 1.');
            return;
        }

        $product = Products::where('product_code', $this->product_code)->first();
        if ($product) {
            if ($product->stock < $this->quantity) {
                session()->flash('error', 'Insufficient stock for the selected product.');
                return;
            }

            // Check for applicable discounts
            $discount = Discounts::where('buy_product_id', $product->id)->first();
            $priceBeforeDiscount = $product->price * $this->quantity;
            $discountedPrice = $priceBeforeDiscount;
            $discountDescription = 'None';

            if ($discount) {
                if ($discount->discount_type === 'buy_x_get_y') {
                    if ($this->quantity >= $discount->buy_quantity) {
                        $freeItems = intdiv($this->quantity, $discount->buy_quantity) * $discount->free_quantity;
                        $discountedPrice = $priceBeforeDiscount;
                        $discountDescription = "Buy {$discount->buy_quantity} Get {$discount->free_quantity}";

                        // Add or update the free item in the cart
                        $this->addOrUpdateFreeItem($discount->free_product_id, $freeItems);
                    } else {
                        session()->flash('info', "Buy {$discount->buy_quantity} to get {$discount->free_quantity} free. Add more items to qualify for the discount.");
                    }
                } elseif ($discount->discount_type === 'percentage') {
                    // Check if the quantity meets the required buy_quantity
                    if ($this->quantity >= $discount->buy_quantity) {
                        $discountedPrice = $priceBeforeDiscount * (1 - ($discount->discount_percentage / 100));
                        $discountDescription = "{$discount->discount_percentage}% Off";
                    } else {
                        session()->flash('info', "Buy at least {$discount->buy_quantity} to qualify for a {$discount->discount_percentage}% discount.");
                    }
                }
            }

            // Check if the product already exists in the cart
            foreach ($this->cart as $index => $item) {
                if ($item['product_id'] === $product->id) {
                    // Update the quantity and recalculate the total price
                    $newQuantity = $item['quantity'] + $this->quantity;
                    if ($product->stock < $newQuantity) {
                        session()->flash('error', 'Insufficient stock for the selected product.');
                        return;
                    }

                    $priceBeforeDiscount = $product->price * $newQuantity;
                    $discountedPrice = $priceBeforeDiscount;

                    if ($discount) {
                        if ($discount->discount_type === 'buy_x_get_y') {
                            if ($newQuantity >= $discount->buy_quantity) {
                                $freeItems = intdiv($newQuantity, $discount->buy_quantity) * $discount->free_quantity;
                                $discountedPrice = $priceBeforeDiscount;
                                $discountDescription = "Buy {$discount->buy_quantity} Get {$discount->free_quantity}";

                                // Add or update the free item in the cart
                                $this->addOrUpdateFreeItem($discount->free_product_id, $freeItems);
                            } else {
                                $discountDescription = 'None';
                                session()->flash('info', "Buy {$discount->buy_quantity} to get {$discount->free_quantity} free. Add more items to qualify for the discount.");
                            }
                        } elseif ($discount->discount_type === 'percentage') {
                            if ($newQuantity >= $discount->buy_quantity) {
                                $discountedPrice = $priceBeforeDiscount * (1 - ($discount->discount_percentage / 100));
                                $discountDescription = "{$discount->discount_percentage}% Off";
                            } else {
                                $discountDescription = 'None';
                            }
                        }
                    }

                    $this->cart[$index]['quantity'] = $newQuantity;
                    $this->cart[$index]['price_before_discount'] = $priceBeforeDiscount;
                    $this->cart[$index]['total_price'] = $discountedPrice;
                    $this->cart[$index]['discount'] = $discountDescription;

                    $this->reset(['product_code', 'quantity']);
                    return;
                }
            }

            // Add a new item to the cart if it doesn't already exist
            $this->cart[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => $this->quantity,
                'price' => $product->price,
                'price_before_discount' => $priceBeforeDiscount,
                'discount' => $discountDescription,
                'total_price' => $discountedPrice,
            ];

            $this->reset(['product_code', 'quantity']);
        } else {
            session()->flash('error', 'Product not found.');
        }
    }

    public function increaseQuantity($index)
    {
        if (isset($this->cart[$index])) {
            $product = Products::find($this->cart[$index]['product_id']);

            // Check if stock is sufficient
            if ($product && $this->cart[$index]['quantity'] < $product->stock) {
                $this->cart[$index]['quantity']++;
                $this->updateCartItem($index, $product);
            } else {
                session()->flash('error', 'Insufficient stock for the selected product.');
            }
        }
    }

    public function decreaseQuantity($index)
    {
        if (isset($this->cart[$index]) && $this->cart[$index]['quantity'] > 1) {
            $product = Products::find($this->cart[$index]['product_id']);
            $this->cart[$index]['quantity']--;
            $this->updateCartItem($index, $product);

            // Check if the item has a discount and if the quantity falls below the required amount
            $discount = Discounts::where('buy_product_id', $product->id)->first();
            if ($discount && $discount->discount_type === 'buy_x_get_y') {
                $quantity = $this->cart[$index]['quantity'];
                if ($quantity < $discount->buy_quantity) {
                    // Remove the free item from the cart
                    foreach ($this->cart as $cartIndex => $item) {
                        if ($item['product_id'] === $discount->free_product_id) {
                            unset($this->cart[$cartIndex]);
                            $this->cart = array_values($this->cart); // Reindex the cart array
                            break;
                        }
                    }
                }
            }
        }
    }

    private function updateCartItem($index, $product)
    {
        $quantity = $this->cart[$index]['quantity'];
        $priceBeforeDiscount = $product->price * $quantity;
        $discountedPrice = $priceBeforeDiscount;
        $discountDescription = 'None';

        // Check for applicable discounts
        $discount = Discounts::where('buy_product_id', $product->id)->first();
        if ($discount) {
            if ($discount->discount_type === 'buy_x_get_y') {
                if ($quantity >= $discount->buy_quantity) {
                    $freeItems = intdiv($quantity, $discount->buy_quantity) * $discount->free_quantity;
                    $discountedPrice = $priceBeforeDiscount;
                    $discountDescription = "Buy {$discount->buy_quantity} Get {$discount->free_quantity}";

                    // Add or update the free item in the cart
                    $this->addOrUpdateFreeItem($discount->free_product_id, $freeItems);
                } else {
                    $discountDescription = 'None';
                }
            } elseif ($discount->discount_type === 'percentage') {
                $discountedPrice = $priceBeforeDiscount * (1 - ($discount->discount_percentage / 100));
                $discountDescription = "{$discount->discount_percentage}% Off";
            }
        }

        $this->cart[$index]['price_before_discount'] = $priceBeforeDiscount;
        $this->cart[$index]['total_price'] = $discountedPrice;
        $this->cart[$index]['discount'] = $discountDescription;
    }

    /**
     * Add or update the free item in the cart.
     *
     * @param int $freeProductId
     * @param int $freeQuantity
     */
    private function addOrUpdateFreeItem($freeProductId, $freeQuantity)
    {
        $freeProduct = Products::find($freeProductId);
        if ($freeProduct) {
            foreach ($this->cart as $index => $item) {
                // Check if the free item already exists in the cart
                if ($item['product_id'] === $freeProduct->id && strpos($item['name'], '(Free)') !== false) {
                    // Update the quantity of the free item
                    $this->cart[$index]['quantity'] = $freeQuantity;
                    return;
                }
            }

            // Add the free item to the cart if it doesn't already exist
            $this->cart[] = [
                'product_id' => $freeProduct->id,
                'name' => $freeProduct->name . ' (Free)',
                'quantity' => $freeQuantity,
                'price' => 0, // Free item, so price is 0
                'price_before_discount' => 0,
                'discount' => 'Free Item',
                'total_price' => 0,
            ];
        }
    }

    public function removeFromCart($index)
    {
        if (isset($this->cart[$index])) {
            $productId = $this->cart[$index]['product_id'];
            $product = Products::find($productId);

            // Remove the item from the cart
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart); // Reindex the cart array

            // Check if the removed item had a discount and if it affects a free item
            $discount = Discounts::where('buy_product_id', $productId)->first();
            if ($discount && $discount->discount_type === 'buy_x_get_y') {
                // Find and remove the free item from the cart
                foreach ($this->cart as $cartIndex => $item) {
                    if ($item['product_id'] === $discount->free_product_id) {
                        unset($this->cart[$cartIndex]);
                        $this->cart = array_values($this->cart); // Reindex the cart array
                        break;
                    }
                }
            }
        }
    }

    public function saveTransaction()
    {
        $this->validate([
            'payment_method' => 'required|in:cash,credit_card,debit_card,qris',
        ]);

        $subtotal = array_sum(array_column($this->cart, 'total_price'));
        $taxAmount = $subtotal * ($this->taxRate / 100);
        $totalPriceWithTax = $subtotal + $taxAmount;

        $transaction = Transactions::create([
            'cashier_id' => auth()->id(), // Set the cashier ID to the currently logged-in user
            'member_id' => $this->member_id,
            'total_price' => $totalPriceWithTax,
            'payment_method' => $this->payment_method,
        ]);

        foreach ($this->cart as $item) {
            $transaction->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'total_price' => $item['total_price'],
            ]);

            // Decrease the product stock
            $product = Products::find($item['product_id']);
            if ($product) {
                $product->decrement('stock', $item['quantity']);
            }
        }

        $this->reset(['member_id', 'cart']);
        session()->flash('message', 'Transaction saved successfully!');

        return redirect()->route('receipt', ['transaction_id' => $transaction->id]);
    }

    public function getCartTotalPriceProperty()
    {
        return array_sum(array_column($this->cart, 'total_price'));
    }

    public function render()
    {
        return view('livewire.transaction');
    }
}