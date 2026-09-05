<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\User;
use App\Services\AccountServices;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    //
    public function __construct(public AccountServices $accountServices){}

    public function activateAccount(int $id): JsonResponse
    {
        //
        $response = $this->accountServices->activateAccount($id);
        return response()->json(
            $response['body'],$response['status']
        );
    }

    public function closeAccount(int $id): JsonResponse
    {
        //
        $response = $this->accountServices->closeAccount($id);
        return response()->json(
            $response['body'],$response['status']
        );
    }

    public function deactivateAccount(int $id): JsonResponse
    {
        //
        $response = $this->accountServices->deactivateAccount($id);
        return response()->json(
            $response['body'],$response['status']
        );
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $accounts = Account::with(['user' ,'currency'])->paginate(10);
        return response()->json([
            'accounts' => AccountResource::collection($accounts),
            'pagination' => [
                'total' => $accounts->total(),
                'per_page' => $accounts->perPage(),
                'current_page' => $accounts->currentPage(),
                'last_page' => $accounts->lastPage()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AccountRequest $request ,User $user): JsonResponse
    {
        //
        $credentials = $request->validated();
        $response = $this->accountServices->createAccount($credentials ,$user);
        return response()->json(
            $response['body'],$response['status']
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        //
        $response = $this->accountServices->getAccount($account);
        return response()->json(
            $response['body'],$response['status']
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AccountRequest $request, Account $account)
    {
        //
        $credentials = $request->validated();
        $response = $this->accountServices->updateAccount($credentials ,$account);
        return response()->json(
            $response['body'],$response['status']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        //
        $response = $this->accountServices->deleteAccount($account);
        return response()->json(
            $response['body'],$response['status']
        );
    }
}
