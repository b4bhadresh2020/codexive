<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Account;
use App\Models\AccountType;
use App\Models\MasterAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function getAccountTypes()
    {
        $accountType = AccountType::all();
        return ApiResponse::create(["accountType" => $accountType]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = Account::with('branch', 'masterAccount', 'accountType')->get();

        return ApiResponse::create([
            "accounts" => $accounts
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
        $request->validate([
            'branch_id' => 'required',
            'master_account_id' => 'required',
        ]);
        try {
            $accountType = MasterAccount::find($request->master_account_id)->account_type_id;
            $account = Account::create([
                'branch_id' => $request->branch_id,
                'master_account_id' => $request->master_account_id,
                'account_type_id' => $accountType
            ]);
        } catch (\Exception $e) {
            return ApiResponse::createServerError($e);
        }

        return ApiResponse::create($account);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function edit(Account $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            Account::whereId($id)->update($request->only([
                'branch_id', 'master_account_id'
            ]));
        } catch (\Exception $e) {
            return ApiResponse::createServerError($e);
        }
        return ApiResponse::__create("Account updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $account = Account::find($id);
            $account->delete();
        } catch (\Exception $e) {
            return ApiResponse::createServerError($e);
        }
        return ApiResponse::__create("Account deleted successfully");
    }
}
