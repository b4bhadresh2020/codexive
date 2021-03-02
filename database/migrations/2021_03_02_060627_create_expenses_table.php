<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade')->onUpdate('cascade');
            $table->tinyInteger('transaction_type')->comment("0 => Cash, 1 => Cheque, 2 => Transfer, 3 => Forex");
            $table->unsignedBigInteger('expense_type_id');
            $table->foreign('expense_type_id')->references('id')->on('expense_types')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('expenses');
    }
}
