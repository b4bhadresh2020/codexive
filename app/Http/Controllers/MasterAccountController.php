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
        $profile_img_path = ($request->hasFile('profile_img')) ? Storage::disk()->put('images', $request->file('profile_img')) : "";
        try {
            $masterAccount = MasterAccount::create(array_merge($request->except('profile_img'), [
                "profile_img" => $profile_img_path,
            ]));
            return ApiResponse::create($masterAccount);
        } catch (\Exception $e) {
            return ApiResponse::createServerError($e);
        }
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
    public function edit($id)
    {
        try {
            $account = MasterAccount::find($id);
            $type = $account->account_type_id;
            if ($type == MasterAccount::BANK_ACCOUNT) {
                $data = $account->only(['name', 'branch', 'ifsc']);
            } else if ($type == MasterAccount::CASH_ACCOUNT) {
                $data = $account->only(['name']);
            } else if (
                $type == MasterAccount::PARTY_ACCOUNT ||
                $type == MasterAccount::INDIVIDUAL_ACCOUNT ||
                $type == MasterAccount::PARTNER_ACCOUNT
            ) {
                $data = $account->only([
                    'name', 'middle_name', 'last_name', 'email', 'pan_no', 'gst_no'
                ]);
            } else {
                $data = $account->only([
                    'name', 'middle_name', 'last_name', 'email', 'profile_img', 'dob', 'doj', 'mobile_no', 'emergency_no', 'address', 'ctc', 'position', 'pan_no', 'pan_img', 'aadhar_no', 'aadhar_img'
                ]);
            }
        } catch (\Exception $e) {
            return ApiResponse::createServerError($e);
        }

        return ApiResponse::create([
            "masterAccount" => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MasterAccount  $masterAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $profile_img_path = ($request->hasFile('profile_img')) ? Storage::disk()->put('images', $request->file('profile_img')) : "";
            $account = MasterAccount::find($id);
            if ($profile_img_path != "") {
                if ($account->profile_img && Storage::disk()->exists($account->profile_img)) {
                    @Storage::disk()->delete($account->profile_img);
                }
            }
            MasterAccount::where('id', $id)->update(
                array_merge($request->except('profile_img', '_method'), [
                    "profile_img" => $profile_img_path,
                ])
            );
        } catch (\Exception $e) {
            return ApiResponse::createServerError($e);
        }

        return ApiResponse::__create("Account update successfully.");
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
