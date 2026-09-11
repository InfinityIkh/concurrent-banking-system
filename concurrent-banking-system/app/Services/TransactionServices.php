<?php

namespace App\Services;

use App\Enums\TransactionAccountRole;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\InvalidTransactionAmountException;
use App\Http\Resources\TransactionResource;
use App\Jobs\GeneratePdfJob;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionAccount;
use Illuminate\Support\Facades\DB;

class TransactionServices
{
    //
    public function getTransactions(): array
    {
        //
        $transactions = Transaction::with(['account','destinationAccount'])->paginate(20);
        return [
            'status' => 200,
            'body' => [
                'message' => 'Transactions retrieved',
                'transactions' => TransactionResource::collection($transactions),
                'pagination' => [
                    'total' => $transactions->total(),
                    'current_page' => $transactions->currentPage(),
                    'per_page' => $transactions->perPage(),
                    'last_page' => $transactions->lastPage(),
                ]
            ]
        ];
    }


    public function createRefundTransaction(Transaction $transaction)
    {
        //
        if (!$transaction->destinationAccount()->exists()) {
            return [
                'status' => 422,
                'body' => [
                    'message' => 'You can\'t refund a deposit or withdrawal transaction',
                ],
            ];
        }

        if ($transaction->type === TransactionType::REFUND) {
            return [
                'status' => 422,
                'body' => [
                    'message' => 'This transaction has already been refunded',
                ],
            ];
        }

        return DB::transaction(function () use ($transaction) {
            $accountsIds = [$transaction->account_id, $transaction->destination_account_id];
            sort($accountsIds); // consistent lock order avoids deadlocks - kept as-is, good practice

            $accounts = Account::whereIn('id', $accountsIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $lockedSenderAccount = $accounts[$transaction->account_id];
            $lockedReceiverAccount = $accounts[$transaction->destination_account_id];

            $senderTransactionAccount = TransactionAccount::where('transaction_id', $transaction->id)
                ->where('role', TransactionAccountRole::SENDER)
                ->first(['balance_before', 'balance_after']);

            if (!$senderTransactionAccount) {
                abort(422, 'Sender transaction account record not found.');
            }

            $amount = $senderTransactionAccount->balance_before - $senderTransactionAccount->balance_after;

            if ($amount <= 0) {
                abort(422, 'Unable to determine a valid refund amount.');
            }

            if ($lockedReceiverAccount->balance < $amount) {
                return [
                    'status' => 422,
                    'body' => [
                        'message' => 'Receiver balance is insufficient to reverse this transaction.',
                    ],
                ];
            }

            $lockedSenderAccount->increment('balance', $amount);
            $lockedReceiverAccount->decrement('balance', $amount);

            $transaction->update([
                'type' => TransactionType::REFUND,
            ]);
            GeneratePdfJob::dispatch($transaction)->afterCommit();

            return [
                'status' => 200,
                'body' => [
                    'message' => 'Transaction refunded',
                    'transaction' => new TransactionResource($transaction),
                ],
            ];
        });
    }

    public function createTransferTransaction(array $credentials ,Account $fromAccount ,Account $toAccount): array
    {
        //
        return DB::transaction(function () use ($credentials , $fromAccount, $toAccount) {
            //
            $amount = $credentials['amount'];
            if($amount % 50 != 0){
                throw new InvalidTransactionAmountException();
            }
            $transaction = Transaction::create([
                'account_id' => $fromAccount->id,
                'destination_account_id' => $toAccount->id,
                'amount' => $amount,
                'type' => TransactionType::TRANSFER
            ]);
            $accountsIds = [$transaction->destination_account_id ,$transaction->account_id];
            sort($accountsIds);

            $accounts = Account::whereIn('id', $accountsIds)->lockForUpdate()->get()->keyBy('id');
            $lockedSenderAccount = $accounts[$transaction->account_id];
            $lockedReceiverAccount = $accounts[$transaction->destination_account_id];

            if($lockedSenderAccount->balance < $transaction->amount){
                $transaction->update([
                    'status' => TransactionStatus::FAILED
                ]);
                throw new InsufficientBalanceException();
            }

            $senderBalanceBefore = $lockedSenderAccount->balance;
            $receiverBalanceBefore = $lockedReceiverAccount->balance;
            $lockedSenderAccount->decrement('balance', $transaction->amount);
            $lockedReceiverAccount->increment('balance', $transaction->amount);

            $transaction->accounts()->attach($lockedReceiverAccount->id ,[
                'role' => TransactionAccountRole::RECEIVER,
                'balance_after' => $lockedReceiverAccount->balance,
                'balance_before' => $receiverBalanceBefore,
            ]);
            $transaction->accounts()->attach($lockedSenderAccount->id ,[
                'role' => TransactionAccountRole::SENDER,
                'balance_after' => $lockedSenderAccount->balance,
                'balance_before' => $senderBalanceBefore,
            ]);
            $transaction->update([
                'status' => TransactionStatus::COMPLETED
            ]);
            GeneratePdfJob::dispatch($transaction)->afterCommit();
            return [
                'status' => 201,
                'body' => [
                    'message' => 'Transaction created successfully',
                    'transaction' => new TransactionResource($transaction),
                    'data' => [
                        $transaction->accounts,
                    ]
                ]
            ];
        });
    }

    public function createWithdrawTransaction(array $credentials ,Account $account)
    {
        //
        return DB::transaction(function () use ($credentials , $account) {
            //
            $amount = $credentials['amount'];
            if($amount % 50 != 0){
                throw new InvalidTransactionAmountException();
            }

            $lockedAccount = Account::where('id' ,$account->id)->lockForUpdate()->first();
            $transaction = Transaction::create([
                'account_id' => $lockedAccount->id,
                'amount' => $amount,
                'type' => TransactionType::WITHDRAW
            ]);

            if($lockedAccount->balance < $amount){
                $transaction->status = TransactionStatus::FAILED;
                $transaction->save();
                throw new InsufficientBalanceException();
            }

            $accountBalanceBefore = $lockedAccount->balance;
            $lockedAccount->decrement('balance', $amount);
            $accountBalanceAfter = $lockedAccount->balance;

            $transaction->accounts()->attach($lockedAccount->id ,[
                'role' => TransactionAccountRole::RECEIVER,
                'balance_before' => $accountBalanceBefore,
                'balance_after' => $accountBalanceAfter,
            ]);
            $transaction->update([
                'status' => TransactionStatus::COMPLETED
            ]);
            GeneratePdfJob::dispatch($transaction)->afterCommit();

            return [
                'status' => 201,
                'body' => [
                    'message' => 'Transaction created successfully',
                    'transaction' => new TransactionResource($transaction),
                    'data' => [
                        $transaction->accounts,
                    ]
                ]
            ];
        });
    }

    public function createDepositTransaction(array $credentials ,Account $account)
    {
        //
        return DB::transaction(function () use ($credentials , $account) {
            //
            $amount = $credentials['amount'];
            if($amount % 50 != 0){
                throw new InvalidTransactionAmountException();
            }

            $lockedAccount = Account::where('id' ,$account->id)->lockForUpdate()->first();
            $transaction = Transaction::create([
                'account_id' => $lockedAccount->id,
                'amount' => $amount,
                'type' => TransactionType::DEPOSIT
            ]);

            if($lockedAccount->balance < $amount){
                $transaction->status = TransactionStatus::FAILED;
                $transaction->save();
                throw new InsufficientBalanceException();
            }

            $accountBalanceBefore = $lockedAccount->balance;
            $lockedAccount->increment('balance', $amount);
            $accountBalanceAfter = $lockedAccount->balance;

            $transaction->accounts()->attach($lockedAccount->id ,[
                'role' => TransactionAccountRole::RECEIVER,
                'balance_before' => $accountBalanceBefore,
                'balance_after' => $accountBalanceAfter,
            ]);
            $transaction->update([
                'status' => TransactionStatus::COMPLETED
            ]);
            GeneratePdfJob::dispatch($transaction)->afterCommit();

            return [
                'status' => 201,
                'body' => [
                    'message' => 'Transaction created successfully',
                    'transaction' => new TransactionResource($transaction),
                    'data' => [
                        $transaction->accounts,
                    ]
                ]
            ];
        });
    }

    public function getTransaction(Transaction $transaction): array
    {
        //
        return [
            'status' => 200,
            'body' => [
                'message' => 'Transaction retrieved',
                'transaction' => new TransactionResource($transaction),
            ]
        ];
    }

    public function updateTransaction(array $credentials ,Transaction $transaction): array
    {
        //
        return [];
    }

    public function removeTransaction(Transaction $transaction): array
    {
        //
        $transaction->delete();
        return [
            'status' => 200,
            'body' => [
                'message' => 'Transaction deleted successfully',
                'transaction' => new TransactionResource($transaction),
            ]
        ];
    }
}
