<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Products;
use App\Models\Discounts;
use App\Models\Transactions;

class Transaction extends Component
{
    public $member_id, $product_code, $quantity, $cart = [];
    public $taxRate = 10; 
    public $receiptData = null;
    public $payment_method = 'cash'; 
    public $availableDiscount = null; 

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

                        $this->addOrUpdateFreeItem($discount->free_product_id, $freeItems);
                    } else {
                        session()->flash('info', "Buy {$discount->buy_quantity} to get {$discount->free_quantity} free. Add more items to qualify for the discount.");
                    }
                } elseif ($discount->discount_type === 'percentage') {
                    if ($this->quantity >= $discount->buy_quantity) {
                        $discountedPrice = $priceBeforeDiscount * (1 - ($discount->discount_percentage / 100));
                        $discountDescription = "{$discount->discount_percentage}% Off";
                    } else {
                        session()->flash('info', "Buy at least {$discount->buy_quantity} to qualify for a {$discount->discount_percentage}% discount.");
                    }
                }
            }

            foreach ($this->cart as $index => $item) {
                if ($item['product_id'] === $product->id) {
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

            $discount = Discounts::where('buy_product_id', $product->id)->first();
            if ($discount && $discount->discount_type === 'buy_x_get_y') {
                $quantity = $this->cart[$index]['quantity'];
                if ($quantity < $discount->buy_quantity) {
                    foreach ($this->cart as $cartIndex => $item) {
                        if ($item['product_id'] === $discount->free_product_id) {
                            unset($this->cart[$cartIndex]);
                            $this->cart = array_values($this->cart); 
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

        $discount = Discounts::where('buy_product_id', $product->id)->first();
        if ($discount) {
            if ($discount->discount_type === 'buy_x_get_y') {
                if ($quantity >= $discount->buy_quantity) {
                    $freeItems = intdiv($quantity, $discount->buy_quantity) * $discount->free_quantity;
                    $discountedPrice = $priceBeforeDiscount;
                    $discountDescription = "Buy {$discount->buy_quantity} Get {$discount->free_quantity}";

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

    
    private function addOrUpdateFreeItem($freeProductId, $freeQuantity)
    {
        $freeProduct = Products::find($freeProductId);
        if ($freeProduct) {
            foreach ($this->cart as $index => $item) {
                if ($item['product_id'] === $freeProduct->id && strpos($item['name'], '(Free)') !== false) {
                    $this->cart[$index]['quantity'] = $freeQuantity;
                    return;
                }
            }

            $this->cart[] = [
                'product_id' => $freeProduct->id,
                'name' => $freeProduct->name . ' (Free)',
                'quantity' => $freeQuantity,
                'price' => 0,
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

            unset($this->cart[$index]);
            $this->cart = array_values($this->cart);

            $discount = Discounts::where('buy_product_id', $productId)->first();
            if ($discount && $discount->discount_type === 'buy_x_get_y') {
                foreach ($this->cart as $cartIndex => $item) {
                    if ($item['product_id'] === $discount->free_product_id) {
                        unset($this->cart[$cartIndex]);
                        $this->cart = array_values($this->cart); 
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

        // Generate a unique transaction code
        $transactionCode = 'TRX-' . strtoupper(uniqid());


        $transaction = Transactions::create([
            'transaction_code' => $transactionCode,
            'cashier_id' => auth()->id(),
            'member_id' => $this->member_id,
            'total_price' => $totalPriceWithTax,
            'payment_method' => $this->payment_method,
        ]);

        foreach ($this->cart as $item) {
            $discountAmount = ($item['price_before_discount'] ?? 0) - ($item['total_price'] ?? 0);
            
            \Log::info("Item: {$item['name']}, Harga sebelum: {$item['price_before_discount']}, Setelah: {$item['total_price']}, Diskon: $discountAmount");
        
            $transaction->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'total_price' => $item['total_price'],
                'discount_amount' => $discountAmount,
            ]);
        
        

            $product = Products::find($item['product_id']);
            if ($product) {
                $product->decrement('stock', $item['quantity']);
            }
        }

        $this->reset(['cart', 'member_id', 'payment_method']);
        session()->flash('message', 'Transaction saved successfully!');

        return redirect()->route('receipt', ['transaction' => $transactionCode]);
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