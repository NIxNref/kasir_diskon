<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Products;

class SidebarKasir extends Component
{
    public function render()
    {
        $products = Products::whereHas('discount')->get();
        return view('livewire.sidebar-kasir', compact('products'));
    }
}
