<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Expense;
use App\Models\MasterAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyticController extends Controller
{
    public function getMasterAccounts(){
        $masterAccounts = MasterAccount::with('accountType')->get();

        return ApiResponse::create([
            'masterAccounts' => $masterAccounts
        ]);
    }

    public function getAccountData(Request $request,$account){
        $dates =  $request->all()[0];
        $formatedDate = [];
        foreach ($dates as $date) {
            array_push($formatedDate, Carbon::createFromFormat('Y-m-d', $date));
        }
        if($formatedDate[0]->greaterThan($formatedDate[1])){
            $temp = $formatedDate[0];
            $formatedDate[0] = $formatedDate[1];
            $formatedDate[1] = $temp;
        }

        $carryFwd = 0;
        $prevToTransaction = Transaction::where('to_account_id',$account)->with('toAccount.masterAccount','fromAccount.masterAccount')->whereDate('created_at',"<",$formatedDate[0])->get();
        $prevFromTransaction = Transaction::where('from_account_id',$account)->with('fromAccount.masterAccount','toAccount.masterAccount')->whereDate('created_at',"<",$formatedDate[0])->get();
        $prevExpenses = Expense::where('account_id',$account)->with('account.masterAccount')->whereDate('created_at',"<",$formatedDate[0])->get();
        foreach ($prevToTransaction as $transaction) {
            $carryFwd += $transaction->amount;
        }
        foreach ($prevFromTransaction as $transaction) {
            $carryFwd -= $transaction->amount;
        }
        foreach ($prevExpenses as $expense) {
            $carryFwd -= $expense->amount;
        }
        // $toTransaction=Transaction::where('to_account_id',$account)->with('toAccount.masterAccount','fromAccount.masterAccount')->whereMonth('created_at', Carbon::now()->month)->get();
        $toTransaction=Transaction::where('to_account_id',$account)->with('toAccount.masterAccount','fromAccount.masterAccount')->whereDate('created_at',">=",$formatedDate[0])->whereDate('created_at',"<=",$formatedDate[1])->get();
        $fromTransaction=Transaction::where('from_account_id',$account)->with('fromAccount.masterAccount','toAccount.masterAccount')->whereDate('created_at',">=",$formatedDate[0])->whereDate('created_at',"<=",$formatedDate[1])->get();
        $expenses = Expense::where('account_id',$account)->with('account.masterAccount')->whereDate('created_at',">=",$formatedDate[0])->whereDate('created_at',"<=",$formatedDate[1])->get();


        return ApiResponse::create([
            'to_transaction' => $toTransaction,
            'from_transaction' => $fromTransaction,
            'expense' => $expenses,
            'carryFwd' => $carryFwd,

        ]);

    }
}
