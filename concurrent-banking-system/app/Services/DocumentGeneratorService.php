<?php

namespace App\Services;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DocumentGeneratorService
{
    public function generatePdf(Transaction $transaction): string
    {
        $pdf = Pdf::loadView('transaction.receipt',
            [
                'transaction' => $transaction,
            ]);
        $fileName = 'receipts/'.$transaction->id.'.pdf';
        Storage::disk('public')->put($fileName, $pdf->output());
        return Storage::disk('public')->url($fileName);
    }
}
