<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Account;
use App\Models\Cheque;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function getAccounts(Request $request)
    {
        $accounts = Account::with('masterAccount')->where('account_type_id', $request->accountTypeId)->get();
        return ApiResponse::create([
            "accounts" => $accounts
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = Transaction::with('toAccount.masterAccount', 'fromAccount.masterAccount')->get();
        return ApiResponse::create([
            "transactions" => $transactions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $invoice_path = ($request->hasFile('invoice_img')) ? Storage::disk()->put('images', $request->file('invoice_img')) : "";
        try {
            $transaction = Transaction::create(array_merge($request->except('invoice_img'), [
                "invoice_img" => $invoice_path,
            ]));

            //If transaction type is cheque
            if ($request->transaction_type == 1) {
                Cheque::create([
                    'transaction_id' => $transaction->id,
                    'from' => $transaction->from_account_id,
                    'to' => $transaction->to_account_id,
                    'amount' => $transaction->amount,
                    'deposited_at' => $transaction->cheque_deposited_date,
                ]);
            }
        } catch (\Exception $e) {
            return ApiResponse::createServerError($e);
        }
        return ApiResponse::__create("Transaction created successfully.");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $transaction = Transaction::find($id);
            $invoice_path = ($request->hasFile('invoice_img')) ? Storage::disk()->put('images', $request->file('invoice_img')) : "";
            if ($invoice_path != "") {
                if ($transaction->invoice_img && Storage::disk()->exists($transaction->invoice_img)) {
                    @Storage::disk()->delete($transaction->invoice_img);
                }
            }
            Transaction::where('id', $id)->update(
                array_merge($request->except('invoice_img', '_method'), [
                    "invoice_img" => $invoice_path,
                ])
            );
        } catch (\Exception $e) {
            return ApiResponse::createServerError($e);
        }

        return ApiResponse::__create("Transaction update successfully.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $transaction = Transaction::find($id);
            $transaction->delete();
        } catch (\Exception $e) {
            return ApiResponse::createServerError($e);
        }
        return ApiResponse::__create("Transaction deleted successfully.");
    }
}
