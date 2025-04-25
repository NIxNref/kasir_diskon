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
    public $salesData = [];
    public $salesLabels = [];
    
    public function mount()
    {
        $today = Carbon::today();
    
        $this->todaySales = Transactions::whereDate('created_at', $today)->count();
        $this->todayRevenue = Transactions::whereDate('created_at', $today)->sum('total_price');
    
        $this->totalSales = Transactions::count();
        $this->totalRevenue = Transactions::sum('total_price');
    
        $this->prepareSalesData();
    }
    
    public function prepareSalesData()
    {
        $sales = Transactions::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->take(7)
            ->get();
    
        $this->salesLabels = $sales->pluck('date')->toArray();
        $this->salesData = $sales->pluck('count')->toArray();
    }
    
    public function render()
    {
        return view('livewire.home', [
            'salesData' => $this->salesData,
            'salesLabels' => $this->salesLabels,
        ]);
    }
}
