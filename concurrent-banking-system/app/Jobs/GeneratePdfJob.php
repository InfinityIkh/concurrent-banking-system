<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\DocumentGeneratorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GeneratePdfJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Transaction $transaction)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(DocumentGeneratorService $documentGeneratorService): void
    {
        //
        $documentGeneratorService->generatePdf($this->transaction);
    }
}
