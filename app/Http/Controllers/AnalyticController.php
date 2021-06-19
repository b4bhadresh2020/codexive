<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Account;
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

        if(!$request->rojmel){
            return ApiResponse::create([
                'to_transaction' => $toTransaction,
                'from_transaction' => $fromTransaction,
                'expense' => $expenses,
                'carryFwd' => $carryFwd,
                ]);
        }else{
            return [
                'to_transaction' => $toTransaction,
                'from_transaction' => $fromTransaction,
                'expense' => $expenses,
                'carryFwd' => $carryFwd,
                ];
        }
    }

    public function getRojmel(Request $request){
        // return auth()->user()->id;
        $request->merge(['rojmel'=>1]);
        $accounts = Account::with('masterAccount','accountType')->whereHas('accountType', function($q){
            return $q->where('name','!=','Employee Account');
        })->get()->toArray();
        $data = [];
        foreach ($accounts as $account) {
            if($account['master_account']['name'] != config('app.opening_balance_acc')){
                $data[$account['id']] = $this->getAccountData($request,$account);
                $data[$account['id']]['name'] = $account['master_account']['name'];
                $data[$account['id']]['type'] = $account['account_type']['name'];
            }
        }
        $totalAmount = 0;
        $totalCarryFwd = 0;
        $totalExpense = 0;
        $toTransaction = [];
        $fromTransaction = [];
        $expenses = [];
        foreach ($data as $id=>$account) {
            $amount = 0;
            foreach ($account['from_transaction'] as $transaction) {
                $amount -= (int)$transaction['amount'];
                array_push($fromTransaction,$transaction);
            }
            foreach ($account['to_transaction'] as $transaction) {
                $amount += (int)$transaction['amount'];
                array_push($toTransaction,$transaction);
            }
            foreach ($account['expense'] as $expense) {
                $amount -= (int)$expense['amount'];
                array_push($expenses,$expense);
            }
            $data[$id]['amount'] = $amount;
            $totalCarryFwd += $account['carryFwd'];
            $totalAmount += $amount;
        }

        return ApiResponse::create([
            'to_transaction' => $toTransaction,
            'from_transaction' => $fromTransaction,
            'expense' => $expenses,
            'rojmelData' => $data,
            'carryFwd' => $totalCarryFwd,
            'total' => $totalAmount
        ]);
    }
}

