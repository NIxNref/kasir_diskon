<?php

namespace App\Http\Controllers;

use App\Models\Transactions;

class ReceiptController extends Controller
{
    public function show($transaction_id)
    {
        $transaction = Transactions::with('items.product')->findOrFail($transaction_id);

        return view('receipt', [
            'transaction' => $transaction,
        ]);
    }
}
