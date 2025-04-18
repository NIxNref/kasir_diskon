<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Products;
use App\Models\Transactions;

class Transaction extends Component
{
    public $member_id, $product_id, $quantity, $cart = [];
    public $taxRate = 10; // Tax rate in percentage
    public $receiptData = null;
    public $payment_method = 'cash'; // Default payment method

    public function addToCart()
    {
        if (!$this->quantity || $this->quantity < 1) {
            session()->flash('error', 'Quantity must be at least 1.');
            return;
        }

        $product = Products::find($this->product_id);
        if ($product) {
            if ($product->stock < $this->quantity) {
                session()->flash('error', 'Insufficient stock for the selected product.');
                return;
            }

            // Calculate the discounted price
            $priceBeforeDiscount = $product->price * $this->quantity;
            $discountedPrice = $product->getDiscountedPrice($this->quantity, $priceBeforeDiscount);
            $discountAmount = $priceBeforeDiscount - $discountedPrice;
            $discountPercentage = $discountAmount > 0 ? round(($discountAmount / $priceBeforeDiscount) * 100, 2) : 0;

            // Determine the discount description
            $discountDescription = ucfirst(str_replace('_', ' ', $product->discount_type ?? 'none')); // Format discount type
            if ($product->event_discount !== 'none' && $product->event_discount) {
                $discountDescription = ucfirst($product->event_discount); // Prioritize event discount
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
                    $discountedPrice = $product->getDiscountedPrice($newQuantity, $priceBeforeDiscount);
                    $discountAmount = $priceBeforeDiscount - $discountedPrice;
                    $discountPercentage = $discountAmount > 0 ? round(($discountAmount / $priceBeforeDiscount) * 100, 2) : 0;

                    $this->cart[$index]['quantity'] = $newQuantity;
                    $this->cart[$index]['price_before_discount'] = $priceBeforeDiscount;
                    $this->cart[$index]['total_price'] = $discountedPrice;
                    $this->cart[$index]['discount'] = $discountDescription; // Update discount description
                    $this->cart[$index]['discount_percentage'] = $discountPercentage;

                    $this->reset(['product_id', 'quantity']);
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
                'discount_type' => $product->discount_type ?? 'none',
                'discount_value' => $product->discount_value ?? 0,
                'discount' => $discountDescription, // Add formatted discount description
                'discount_percentage' => $discountPercentage,
                'total_price' => $discountedPrice,
            ];

            $this->reset(['product_id', 'quantity']);
        } else {
            session()->flash('error', 'Product not found.');
        }
    }

    public function removeFromCart($index)
    {
        if (isset($this->cart[$index])) {
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart); // Reindex the cart array
        }
    }

    public function saveTransaction()
    {
        $this->validate([
            'payment_method' => 'required|in:cash,credit_card,debit_card,qris', // Validate payment method
        ]);

        $subtotal = array_sum(array_column($this->cart, 'total_price'));
        $taxAmount = $subtotal * ($this->taxRate / 100);
        $totalPriceWithTax = $subtotal + $taxAmount;

        $transaction = Transactions::create([
            'member_id' => $this->member_id,
            'total_price' => $totalPriceWithTax,
            'payment_method' => $this->payment_method, // Save payment method
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

        // Redirect to the receipt view
        return redirect()->route('receipt', ['transaction_id' => $transaction->id]);
    }

    public function getCartTotalPriceProperty()
    {
        return array_sum(array_column($this->cart, 'total_price'));
    }

    public function render()
    {
        return view('livewire.transaction', [
            'products' => Products::all(), // Pass products to the view
        ]);
    }
}
