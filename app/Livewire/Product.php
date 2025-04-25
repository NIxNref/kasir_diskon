<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Products;
use App\Models\Category;

class Product extends Component
{
    use WithFileUploads;

    public $pilihanMenu = 'lihat';
    public $productId, $product_code, $name, $price, $stock, $category_id, $image;
    public $produkTerpilih;

    public function resetForm()
    {
        $this->productId = null;
        $this->product_code = '';
        $this->name = '';
        $this->price = '';
        $this->stock = 1;
        $this->category_id = null;
        $this->image = null;
    }

    public function tambahProduct()
    {
        $this->validate([
            'product_code' => 'required|unique:products,product_code',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048', 
        ]);

        $imagePath = $this->image ? $this->image->store('products', 'public') : null;

        Products::create([
            'product_code' => $this->product_code,
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
            'category_id' => $this->category_id,
            'image' => $imagePath,
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
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $product = Products::findOrFail($this->productId);

        $imagePath = $this->image ? $this->image->store('products', 'public') : $product->image;

        $product->update([
            'product_code' => $this->product_code,
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
            'category_id' => $this->category_id,
            'image' => $imagePath,
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
        $this->category_id = $product->category_id;
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
            'semuaProduk' => Products::with('category')->get(), 
            'categories' => Category::all(), 
        ]);
    }
}