<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Transaction Receipt #{{ $transaction->id }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            background: #f5f5f5;
            color: #222;
            margin: 0;
            padding: 30px;
        }

        .receipt {
            width: 100%;
            max-width: 700px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border: 1px solid #ddd;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 12px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 6px;
        }

        .row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .label,
        .value {
            display: table-cell;
            width: 50%;
            font-size: 12px;
        }

        .label {
            color: #666;
        }

        .value {
            text-align: right;
            font-weight: bold;
        }

        .amount {
            text-align: center;
            margin: 25px 0;
        }

        .amount .label {
            display: block;
            width: 100%;
            font-size: 12px;
            color: #666;
        }

        .amount .value {
            display: block;
            width: 100%;
            font-size: 28px;
            margin-top: 5px;
        }

        .status {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 25px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .accounts {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .accounts th,
        .accounts td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .accounts th {
            background: #f0f0f0;
            text-align: left;
        }

        .accounts td:last-child,
        .accounts th:last-child {
            text-align: right;
        }

        .footer {
            border-top: 1px solid #ddd;
            margin-top: 30px;
            padding-top: 15px;
            text-align: center;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>

<body>

<div class="receipt">

    {{-- Header --}}
    <div class="header">
        <h1>Transaction Receipt</h1>
        <p>
            Concurrent Banking System
        </p>
    </div>


    {{-- Transaction Information --}}
    <div class="section">
        <div class="section-title">
            Transaction Information
        </div>

        <div class="row">
            <div class="label">
                Reference
            </div>

            <div class="value">
                {{ $transaction->reference }}
            </div>
        </div>

        <div class="row">
            <div class="label">
                Type
            </div>

            <div class="value">
                {{ ucfirst($transaction->type->value ?? $transaction->type) }}
            </div>
        </div>

        <div class="row">
            <div class="label">
                Status
            </div>

            <div class="value">
                {{ ucfirst($transaction->status->value ?? $transaction->status) }}
            </div>
        </div>

        <div class="row">
            <div class="label">
                Created At
            </div>

            <div class="value">
                {{ $transaction->created_at->format('Y-m-d H:i:s') }}
            </div>
        </div>

    </div>


    {{-- Amount --}}
    <div class="amount">

        <span class="label">
            Transaction Amount
        </span>

        <span class="value">
            {{ number_format($transaction->amount, 2) }}
        </span>

    </div>


    {{-- Accounts --}}
    <div class="section">

        <div class="section-title">
            Accounts
        </div>

        <table class="accounts">

            <thead>
            <tr>
                <th>Account</th>
                <th>Role</th>
                <th>Amount</th>
                <th>Balance Before</th>
                <th>Balance After</th>
            </tr>
            </thead>

            <tbody>

            @foreach ($transaction->accounts as $account)

                <tr>

                    <td>
                        {{ $account->account_number }}
                    </td>

                    <td>
                        {{ ucfirst($account->pivot->role->value ?? $account->pivot->role) }}
                    </td>

                    <td>
                        {{ number_format($account->pivot->balance_after - $account->pivot->balance_before, 2) }}
                    </td>

                    <td>
                        {{ number_format($account->pivot->balance_before, 2) }}
                    </td>

                    <td>
                        {{ number_format($account->pivot->balance_after, 2) }}
                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

    </div>


    {{-- Status --}}
    <div class="status">

        {{ ucfirst($transaction->status->value ?? $transaction->status) }}

    </div>


    {{-- Footer --}}
    <div class="footer">
        <p>
            Transaction Reference:
            {{ $transaction->reference }}
        </p>
    </div>
</div>

</body>
</html>
