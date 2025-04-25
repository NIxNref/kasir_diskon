<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function generateReceipt($transactionCode)
    {
        // Find the transaction by transaction_code
        $transaction = Transactions::where('transaction_code', $transactionCode)->with('items.product')->firstOrFail();

        // Return the receipt view
        return view('receipt-page', compact('transaction'));
    }

    public function downloadReceipt($transactionCode)
    {
        // Find the transaction by transaction_code
        $transaction = Transactions::where('transaction_code', $transactionCode)->with('items.product')->firstOrFail();

        // Generate the PDF
        $pdf = Pdf::loadView('receipt', compact('transaction'));

        // Stream the PDF as a download
        return $pdf->download('receipt_' . $transaction->transaction_code . '.pdf');
    }
}