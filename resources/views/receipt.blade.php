<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .receipt {
            max-width: 600px;
            margin: auto;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
        }

        .receipt h2 {
            text-align: center;
        }

        .receipt table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .receipt table th,
        .receipt table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .receipt .total {
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>

<body onload="printAndRedirect()">
    <div class="receipt">
        <h2>Transaction Receipt</h2>
        <p><strong>Transaction ID:</strong> {{ $transaction->id }}</p>
        <p><strong>Member ID:</strong> {{ $transaction->member_id ?? 'N/A' }}</p>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->product->price, 2, ',', '.') }}</td>
                        <td>{{ number_format($item->total_price, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p class="total">Subtotal: Rp
            {{ number_format($transaction->total_price - $transaction->total_price * 0.1, 2, ',', '.') }}</p>
        <p class="total">Tax (10%): Rp {{ number_format($transaction->total_price * 0.1, 2, ',', '.') }}</p>
        <p class="total">Total: Rp {{ number_format($transaction->total_price, 2, ',', '.') }}</p>
    </div>

    <script>
        function printAndRedirect() {
            window.print();
            window.onafterprint = function() {
                window.location.href = '/transaction';
            };
        }
    </script>
</body>

</html>
