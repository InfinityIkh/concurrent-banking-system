<?php

namespace App\Console\Commands;

use App\Models\Currency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currencies:update-exchange-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch latest exchange rates from API and update the database';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //
        $this->info('Starting exchange rates update...');

        $apiKey = config('services.exchange_rate.api_key');
        $response = $this->sendRequest($apiKey);
        if ($response->failed()) {
            $this->error('Failed to fetch data from Currency API.');
            return;
        }

        $rates = $response['conversion_rates'] ?? [];
        if (empty($rates)) {
            $this->error('No rates found.');
            return;
        }

        $this->withProgressBar(Currency::where('is_active' ,true)->cursor() ,function(Currency $currency) use ($rates){
            if(isset($rates[$currency->code])){
                $currency->update([
                    'exchange_rate' => $rates[$currency->code]
                ]);
            }
        });
        $this->newLine();
        $this->info('Exchange rates updated successfully!');
    }

    private function sendRequest(string $apiKey)
    {
        $baseCurrency = 'USD';
        $url = 'https://v6.exchangerate-api.com/v6/'.$apiKey.'/latest/'.$baseCurrency;
        return Http::retry(3 ,100)->get($url);
    }
}
