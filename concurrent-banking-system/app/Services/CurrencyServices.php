<?php

namespace App\Services;

use App\Models\Currency;
use App\Http\Resources\CurrencyResource;

class CurrencyServices
{
    //
    public function get(?Currency $currency): array
    {
        if(!$currency) {
            return [
                'status' => 404,
                'body' => [
                    'message' => 'Currency not found',
                ]
            ];
        }
        return [
            'status' => 200,
            'body' => [
                'currency' => new CurrencyResource($currency),
            ]
        ];
    }

    public function create(array $credentials): array
    {
        $currency = Currency::create($credentials);
        return [
            'status' => 201,
            'body' => [
                'message' => 'Successfully created Currency',
                'currency' => new CurrencyResource($currency)
            ]
        ];
    }

    public function update(array $credentials ,?Currency $currency): array
    {
        if(!$currency) {
            return [
                'status' => 404,
                'body' => [
                    'message' => 'Currency not found',
                ]
            ];
        }
        $currency->update($credentials);
        return [
            'status' => 200,
            'body' => [
                'message' => 'Successfully updated Currency',
                'currency' => new CurrencyResource($currency),
            ]
        ];
    }

    public function delete(?Currency $currency): array
    {
        if(!$currency) {
            return [
                'status' => 404,
                'body' => [
                    'message' => 'Currency not found',
                ]
            ];
        }
        $currency->delete();
        return [
            'status' => 200,
            'body' => [
                'message' => 'Successfully deleted Currency',
            ]
        ];
    }
}
