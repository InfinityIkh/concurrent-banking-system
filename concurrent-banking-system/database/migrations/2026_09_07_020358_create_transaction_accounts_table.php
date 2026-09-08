<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('transaction_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions');
            $table->foreignId('account_id')->constrained('accounts');
            $table->enum('role' ,['sender','receiver']);
            $table->decimal('balance_before',15,2);
            $table->decimal('balance_after',15,2);
            $table->timestamps();

            $table->unique(['transaction_id','account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
