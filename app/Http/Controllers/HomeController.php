<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transactions;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $today = Carbon::today();

        // Calculate today's sales and revenue
        $todaySales = Transactions::whereDate('created_at', $today)->count();
        $todayRevenue = Transactions::whereDate('created_at', $today)->sum('total_price');

        // Calculate total sales and revenue
        $totalSales = Transactions::count();
        $totalRevenue = Transactions::sum('total_price');

        return view('livewire.home', [
            'todaySales' => $todaySales,
            'todayRevenue' => $todayRevenue,
            'totalSales' => $totalSales,
            'totalRevenue' => $totalRevenue,
        ]);
    }
}
