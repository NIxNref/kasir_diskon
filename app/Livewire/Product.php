<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Products;

class Product extends Component
{
    public $pilihanMenu = 'lihat';
    public $productId, $product_code, $name, $price, $stock, $discount_type, $discount_value, $expiration_date, $event_discount;
    public $produkTerpilih;

    public function resetForm()
    {
        $this->productId = null;
        $this->product_code = '';
        $this->name = '';
        $this->price = '';
        $this->stock = 1;
        $this->discount_type = 'none';
        $this->discount_value = null;
        $this->expiration_date = null;
        $this->event_discount = null;
    }

    public function tambahProduct()
    {
        $this->validate([
            'product_code' => 'required|unique:products,product_code',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:1',
            'discount_type' => 'required|in:none,buy_one_get_one,buy_two_get_one,percentage',
            'discount_value' => 'nullable|numeric|min:0|max:100',
            'expiration_date' => 'nullable|date',
            'event_discount' => 'nullable|string|max:255',
        ]);

        Products::create([
            'product_code' => $this->product_code,
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'expiration_date' => $this->expiration_date,
            'event_discount' => $this->event_discount,
        ]);

        $this->resetForm();
        $this->pilihMenu('lihat');
    }

    public function updateProduct()
    {
        $this->validate([
            'product_code' => 'required|unique:products,product_code,' . $this->productId,
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:1',
            'discount_type' => 'required|in:none,buy_one_get_one,buy_two_get_one,percentage',
            'discount_value' => 'nullable|numeric|min:0|max:100',
            'expiration_date' => 'nullable|date',
            'event_discount' => 'nullable|string|max:255',
        ]);

        Products::where('id', $this->productId)->update([
            'product_code' => $this->product_code,
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'expiration_date' => $this->expiration_date,
            'event_discount' => $this->event_discount,
        ]);

        $this->resetForm();
        $this->pilihMenu('lihat');
    }

    public function pilihEdit($id)
    {
        $product = Products::findOrFail($id);
        $this->productId = $product->id;
        $this->product_code = $product->product_code;
        $this->name = $product->name;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->discount_type = $product->discount_type;
        $this->discount_value = $product->discount_value;
        $this->expiration_date = $product->expiration_date;
        $this->event_discount = $product->event_discount;
        $this->pilihanMenu = 'edit';
    }

    public function pilihHapus($id)
    {
        $this->produkTerpilih = Products::find($id);
        $this->pilihanMenu = 'hapus';
    }

    public function hapusProduct()
    {
        if ($this->produkTerpilih) {
            $this->produkTerpilih->delete();
        }

        $this->resetForm();
        $this->pilihMenu('lihat');
    }

    public function pilihMenu($menu)
    {
        $this->resetForm();
        $this->pilihanMenu = $menu;
    }

    public function render()
    {
        return view('livewire.product', [
            'semuaProduk' => Products::all()
        ]);
    }
}
