<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 20px;
            margin: 20px;
        }

        .receipt {
            width: 100%;
            max-width: 400px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .receipt-header,
        .receipt-footer {
            text-align: center;
            margin-bottom: 10px;
        }

        .receipt-header h2 {
            margin: 0;
            font-size: 16px;
        }

        .receipt-header p {
            margin: 0;
            font-size: 10px;
        }

        .receipt-body table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .receipt-body table th,
        .receipt-body table td {
            text-align: left;
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }

        .receipt-summary {
            margin-top: 10px;
        }

        .receipt-summary p {
            margin: 5px 0;
            text-align: right;
        }

        .receipt-footer p {
            font-size: 10px;
            margin: 5px 0;
        }

        .receipt-footer .thank-you {
            font-weight: bold;
        }

        .buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="receipt">
            <div class="receipt-header">
                <h2>DiscountHub</h2>
                <p>Jl. Pekapuran, RT.02/RW.06, Curug</p>
                <p>Depok, Jawa Barat</p>
                <p>Telp: 0811-9892-324</p>
            </div>

            <div class="receipt-body">
                <p><strong>Transaction Code:</strong> {{ $transaction->transaction_code }}</p>
                <p><strong>Date:</strong> {{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Cashier:</strong> {{ $transaction->cashier->name }}</p>
                @if ($transaction->member_id)
                    <p><strong>Member ID:</strong> {{ $transaction->member_id }}</p>
                @endif

                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transaction->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->product->price, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->total_price, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="receipt-summary">
                <p>Subtotal: Rp {{ number_format($transaction->total_price / 1.1, 0, ',', '.') }}</p>
                <p>Tax (10%): Rp
                    {{ number_format($transaction->total_price - $transaction->total_price / 1.1, 0, ',', '.') }}</p>

                <p><strong>Total: Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</strong></p>
                @if ($transaction->items->sum('discount_amount') > 0)
                    <p>Anda Hemat: Rp {{ number_format($transaction->items->sum('discount_amount'), 0, ',', '.') }}</p>
                @endif
            </div>

            <div class="receipt-footer">
                <p class="thank-you">Thank You for Shopping!</p>
                <p>Visit Again!</p>
            </div>
        </div>

        <div class="buttons">
            <a href="{{ route('download-receipt', ['transaction' => $transaction->transaction_code]) }}"
                class="btn btn-primary">
                Download Receipt
            </a>
            <a href="{{ route('transaction') }}" class="btn btn-secondary">
                Back to Transactions
            </a>
        </div>
    </div>
</body>

</html>
