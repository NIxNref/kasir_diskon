<div class="container">
    <div class="row my-4">
        <div class="col-md-12">
            <h3>Transaction Report</h3>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <label for="startDate" class="form-label">Start Date</label>
            <input type="date" id="startDate" class="form-control" wire:model="startDate">
        </div>
        <div class="col-md-4">
            <label for="endDate" class="form-label">End Date</label>
            <input type="date" id="endDate" class="form-control" wire:model="endDate">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-primary w-100" wire:click="loadReport">Load Report</button>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-info" wire:click="showTransactionDetails('totalSales')">
                <div class="card-body">
                    <h5 class="card-title">Total Sales</h5>
                    <p class="card-text">{{ $totalSales }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-success" wire:click="showTransactionDetails('totalRevenue')">
                <div class="card-body">
                    <h5 class="card-title">Total Revenue</h5>
                    <p class="card-text">Rp {{ number_format($totalRevenue, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Transaction ID</th>
                        <th>Date</th>
                        <th>Total Price</th>
                        <th>Items</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $index => $transaction)
                        <tr wire:click="showTransactionDetails({{ $transaction->id }})" style="cursor: pointer;">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $transaction->id }}</td>
                            <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                            <td>Rp {{ number_format($transaction->total_price, 2, ',', '.') }}</td>
                            <td>
                                <ul>
                                    @foreach ($transaction->items as $item)
                                        <li>{{ $item->product->name }} ({{ $item->quantity }} pcs)</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
