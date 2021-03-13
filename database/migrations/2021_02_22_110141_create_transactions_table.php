<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_account_id');
            $table->foreign('from_account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->unsignedBigInteger('to_account_id');
            $table->foreign('to_account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->tinyInteger('transaction_type')->comment("0 => Cash, 1 => Cheque, 2 => Transfer, 3 => Forex");
            $table->float('amount', 9, 2);
            $table->string('notes')->nullable();
            $table->timestamp('date')->nullable();
            $table->string('invoice_img')->nullable();
            $table->string('cheque_no')->nullable();
            $table->timestamp('cheque_deposited_date')->nullable();
            $table->string('transfer_no')->nullable();
            $table->string('bank_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
