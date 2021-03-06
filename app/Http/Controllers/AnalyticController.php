<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
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
        $toTransaction=Transaction::where('to_account_id',$account)->with('toAccount.masterAccount')->whereMonth('created_at', Carbon::now()->month)->get();
        $fromTransaction=Transaction::where('from_account_id',$account)->with('fromAccount.masterAccount')->whereMonth('created_at', Carbon::now()->month)->get();


        return ApiResponse::create([
            'to_transaction' => $toTransaction,
            'from_transaction' => $fromTransaction

        ]);

    }
}
