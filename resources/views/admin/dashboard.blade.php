@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row my-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Today's Sales</h5>
                        <p class="card-text">{{ $todaySales }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Today's Revenue</h5>
                        <p class="card-text">Rp {{ number_format($todayRevenue, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Total Sales</h5>
                        <p class="card-text">{{ $totalSales }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <p class="card-text">Rp {{ number_format($totalRevenue, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
