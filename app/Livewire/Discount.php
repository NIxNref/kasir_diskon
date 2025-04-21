<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Products;
use App\Models\Discounts;

class Discount extends Component
{
    public $discountId, $name, $buy_product_id, $buy_quantity;
    public $free_product_id, $free_quantity;
    public $discount_type = 'buy_x_get_y'; // Default value
    public $discount_percentage;
    public $discounts;

    public function mount()
    {
        $this->resetForm();
        $this->discounts = Discounts::with(['buyProduct', 'freeProduct'])->get();
    }

    public function resetForm()
    {
        $this->discountId = null;
        $this->name = '';
        $this->buy_product_id = null;
        $this->buy_quantity = 1;
        $this->free_product_id = null;
        $this->free_quantity = null;
        $this->discount_type = 'buy_x_get_y'; // Default value
        $this->discount_percentage = null;
    }

    public function saveDiscount()
    {
        $baseRules = [
            'name' => 'required|string|max:255',
            'buy_product_id' => 'required|exists:products,id',
            'buy_quantity' => 'required|integer|min:1',
            'discount_type' => 'required|in:buy_x_get_y,percentage',
        ];

        if ($this->discount_type === 'buy_x_get_y') {
            $baseRules['free_product_id'] = 'required|exists:products,id';
            $baseRules['free_quantity'] = 'required|integer|min:1';
        } elseif ($this->discount_type === 'percentage') {
            $baseRules['discount_percentage'] = 'required|integer|min:1|max:100';
        }

        $this->validate($baseRules);

        Discounts::updateOrCreate(
            ['id' => $this->discountId],
            [
                'name' => $this->name,
                'buy_product_id' => $this->buy_product_id,
                'buy_quantity' => $this->buy_quantity,
                'free_product_id' => $this->free_product_id,
                'free_quantity' => $this->free_quantity,
                'discount_type' => $this->discount_type,
                'discount_percentage' => $this->discount_percentage,
            ]
        );

        $this->resetForm();
        $this->discounts = Discounts::with(['buyProduct', 'freeProduct'])->get();
        session()->flash('message', 'Discount saved successfully!');
    }

    public function editDiscount($id)
    {
        $discount = Discounts::findOrFail($id);
        $this->discountId = $discount->id;
        $this->name = $discount->name;
        $this->buy_product_id = $discount->buy_product_id;
        $this->buy_quantity = $discount->buy_quantity;
        $this->free_product_id = $discount->free_product_id;
        $this->free_quantity = $discount->free_quantity;
        $this->discount_type = $discount->discount_type;
        $this->discount_percentage = $discount->discount_percentage;
    }

    public function deleteDiscount($id)
    {
        Discounts::findOrFail($id)->delete();
        $this->discounts = Discounts::with(['buyProduct', 'freeProduct'])->get();
        session()->flash('message', 'Discount deleted successfully!');
    }

    public function render()
    {
        return view('livewire.discount', [
            'products' => Products::all(),
        ]);
    }
}