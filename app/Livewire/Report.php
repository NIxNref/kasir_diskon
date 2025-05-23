<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transactions;
use Carbon\Carbon;

class Report extends Component
{
    public $startDate;
    public $endDate;
    public $transactions = [];
    public $totalSales = 0;
    public $totalRevenue = 0;
    public $selectedTransaction = null;

    
    public function showTransactionDetails($transactionId)
    {
        $this->selectedTransaction = Transactions::with('items.product')->find($transactionId);
    }

    public function closeModal()
    {
        $this->selectedTransaction = null;
    }

    public function mount()
    {
        $this->startDate = Carbon::today()->subMonth()->toDateString(); 
        $this->endDate = Carbon::today()->toDateString(); 
        $this->loadReport();
    }

    public function loadReport()
    {
        $query = Transactions::query();

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        $this->transactions = $query->with('items.product')->get();
        $this->totalSales = $this->transactions->count();
        $this->totalRevenue = $this->transactions->sum('total_price');
    }

    public function render()
    {
        return view('livewire.report');
    }
}
