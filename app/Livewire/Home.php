<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transactions;
use Carbon\Carbon;

class Home extends Component
{
    public $todaySales;
    public $todayRevenue;
    public $totalSales;
    public $totalRevenue;

    public function mount()
    {
        $today = Carbon::today();

        // Calculate today's sales and revenue
        $this->todaySales = Transactions::whereDate('created_at', $today)->count();
        $this->todayRevenue = Transactions::whereDate('created_at', $today)->sum('total_price');

        // Calculate total sales and revenue
        $this->totalSales = Transactions::count();
        $this->totalRevenue = Transactions::sum('total_price');
    }

    public function render()
    {
        return view('livewire.home');
    }
}
