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
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Total Sales</h5>
                    <p class="card-text">{{ $totalSales }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-success">
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
                        <th>Transaction Code</th>
                        <th>Date</th>
                        <th>Total Price</th>
                        <th>Cashier</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $index => $transaction)
                        <tr wire:click="showTransactionDetails({{ $transaction->id }})" style="cursor: pointer;">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $transaction->transaction_code }}</td>
                            <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                            <td>Rp {{ number_format($transaction->total_price, 2, ',', '.') }}</td>
                            <td>{{ $transaction->cashier->name }}</td>
                            <td>
                                click to view details
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Transaction Details -->
    @if ($selectedTransaction)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Transaction Details</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Transaction Code:</strong> {{ $selectedTransaction->transaction_code }}</p>
                        <p><strong>Date:</strong> {{ $selectedTransaction->created_at->format('Y-m-d') }}</p>
                        <p><strong>Cashier:</strong> {{ $selectedTransaction->cashier->name }}</p>
                        <p><strong>Total Price:</strong> Rp
                            {{ number_format($selectedTransaction->total_price, 2, ',', '.') }}</p>
                        <p><strong>Items:</strong></p>
                        <ul>
                            @foreach ($selectedTransaction->items as $item)
                                <li>{{ $item->product->name }} - {{ $item->quantity }} pcs (Rp
                                    {{ number_format($item->total_price, 2, ',', '.') }})</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
