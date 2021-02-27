<?php

namespace App\Http\Controllers;

use App\Http\Requests\MasterAccountCreateRequest;
use App\Http\Responses\ApiResponse;
use App\Models\MasterAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MasterAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $masterAccounts = MasterAccount::select('id', 'name', 'account_type_id')->with('accountType')->get();
        return ApiResponse::create([
            'masterAccounts' => $masterAccounts
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
    public function store(MasterAccountCreateRequest $request)
    {
        // if ($request->has('profile_img')) {
        //     $filename = $request->profile_img->getClientOriginalName();
        //     $destinationPath = public_path() . '/uploads';
        //     $request->profile_img->move($destinationPath, $request->profile_img->getClientOriginalName());
        //     $request->profile_img = $filename;
        // }
        if ($request->hasFile('profile_img')) {
            $image = $request->file('profile_img');
            $request->profile_img = Storage::disk()->put('images', $image);
        }
        try {
            $masterAccount = MasterAccount::create($request->except('profile_img'));
        } catch (\Exception $e) {
            return ApiResponse::createServerError($e);
        }

        return ApiResponse::create($masterAccount);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MasterAccount  $masterAccount
     * @return \Illuminate\Http\Response
     */
    public function show(MasterAccount $masterAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MasterAccount  $masterAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(MasterAccount $masterAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MasterAccount  $masterAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MasterAccount $masterAccount)
    {
        return ApiResponse::create($masterAccount);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MasterAccount  $masterAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy($masterAccount)
    {
        try {
            $account = MasterAccount::find($masterAccount);
            $account->delete();
        } catch (\Exception $e) {
            return ApiResponse::createServerError($e);
        }

        return ApiResponse::__create("Account deleted successfully.");
    }
}
