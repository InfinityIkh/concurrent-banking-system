<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\TransactionServices;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    //
    public function __construct(public TransactionServices $transactionServices){}
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //
        $response = $this->transactionServices->getTransactions();
        return response()->json(
            $response['body'],$response['status']
        );
    }

    public function createDepositOperation(TransactionRequest $request): JsonResponse
    {
        $user = $request->user();
        $account = $user->account;
        $credentials = $request->validated();
        $response = $this->transactionServices->createDepositTransaction($credentials, $account);
        return response()->json(
            $response['body'],$response['status']
        );
    }

    public function createTransferOperation(TransactionRequest $request ,Account $toAccount): JsonResponse
    {
        //
        $currentUser = $request->user();
        $fromAccount = $currentUser->account;
        $credentials = $request->validated();
        $response = $this->transactionServices->createTransferTransaction($credentials ,$fromAccount ,$toAccount);
        return response()->json(
            $response['body'],$response['status']
        );
    }

    public function show(Transaction $transaction): JsonResponse
    {
        //
        $response = $this->transactionServices->getTransaction($transaction);
        return response()->json(
            $response['body'],$response['status']
        );
    }

    public function update(TransactionRequest $request, Transaction $transaction): JsonResponse
    {
        //
        $credentials = $request->validated();
        $response = $this->transactionServices->updateTransaction($credentials ,$transaction);
        return response()->json(
            $response['body'],$response['status']
        );
    }

    public function destroy(Transaction $transaction): JsonResponse
    {
        //
        $response = $this->transactionServices->removeTransaction($transaction);
        return response()->json(
            $response['body'],$response['status']
        );
    }
}
