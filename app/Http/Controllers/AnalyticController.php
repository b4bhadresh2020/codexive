<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\MasterAccount;
use Illuminate\Http\Request;

class AnalyticController extends Controller
{
    public function getMasterAccounts(){
        $masterAccounts = MasterAccount::with('accountType')->get();

        return ApiResponse::create([
            'masterAccounts' => $masterAccounts
        ]);
    }

    public function getAccountData(Request $request){
        return $request->all();
    }
}
