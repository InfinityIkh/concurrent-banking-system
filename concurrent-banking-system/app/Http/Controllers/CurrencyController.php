<?php

namespace App\Http\Controllers;

use App\Http\Requests\CurrencyRequest;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use App\Services\CurrencyServices;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{
    //
    public function __construct(public CurrencyServices $currencyServices){}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $currency = Currency::all();
        return response()->json([
            'currencies' => CurrencyResource::collection($currency)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CurrencyRequest $request): JsonResponse
    {
        //
        $credentials = $request->validated();
        $response = $this->currencyServices->create($credentials);
        return response()->json(
            $response['body'],$response['status']
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Currency $currency): JsonResponse
    {
        //
        $response = $this->currencyServices->get($currency);
        return response()->json(
            $response['body'],$response['status']
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CurrencyRequest $request, Currency $currency): JsonResponse
    {
        //
        $credentials = $request->validated();
        $response = $this->currencyServices->update($credentials, $currency);
        return response()->json(
            $response['body'],$response['status']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Currency $currency):JsonResponse
    {
        //
        $response = $this->currencyServices->delete($currency);
        return response()->json(
            $response['body'],$response['status']
        );
    }
}
