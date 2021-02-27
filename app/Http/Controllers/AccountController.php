<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Account;
use App\Models\AccountType;
use App\Models\User;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function getAccountTypes()
    {
        $accountType = AccountType::all();
        return ApiResponse::create([ "accountType" => $accountType]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = Account::with('accountType', 'user')->get();

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
            'type' => 'required',
            'name' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);
        try{
            $account = Account::create([
                'type' => $request->type,
                'name' => $request->name
                ]);
            $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'account_id' => $account->id,
            ]);

            $user->save();
        }catch(\Exception $e){
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
    public function update(Request $request, Account $account)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account)
    {
        //
    }
}
