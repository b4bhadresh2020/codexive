<?php

namespace App\Http\Controllers;

use App\Http\Requests\MasterAccountCreateRequest;
use App\Http\Responses\ApiResponse;
use App\Models\MasterAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MasterAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $masterAccounts = MasterAccount::select('id', 'name', 'account_type_id', 'acc_number')->with('accountType')->get();
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
        $files = [
            'profile_img',
            'aadhar_img',
            'pan_img'
        ];
        $paths = [];
        foreach ($files as $file) {
            if ($request->hasFile($file) && $request->$file != "") {
                $paths[$file] = Storage::disk()->put('images', $request->file($file));
            } else {
                $paths[$file] = "";
            }
        }

        try {
            $masterAccount = MasterAccount::create(array_merge($request->except('profile_img', 'aadhar_img', 'pan_img'), $paths));
        } catch (\Exception $e) {
            return ApiResponse::createServerError($e);
        }
        return ApiResponse::__create($masterAccount->name . " Account created successfully");
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
                $data = $account->only(['acc_number', 'name', 'branch', 'ifsc']);
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
            $account = MasterAccount::find($id);

            $files = [
                'profile_img',
                'aadhar_img',
                'pan_img'
            ];
            $paths = [
                'profile_img' => $account->profile_img,
                'aadhar_img' => $account->aadhar_img,
                'pan_img' => $account->pan_img
            ];
            foreach ($files as $file) {
                if ($request->hasFile($file) && $request->$file != "") {
                    $paths[$file] = Storage::disk()->put('images', $request->file($file));
                }
            }
            $profile_img_path = ($request->hasFile('profile_img')) ? Storage::disk()->put('images', $request->file('profile_img')) : $account->profile_img;
            if ($profile_img_path != "") {
                if ($account->profile_img && Storage::disk()->exists($account->profile_img)) {
                    @Storage::disk()->delete($account->profile_img);
                }
            }
            MasterAccount::where('id', $id)->update(
                array_merge($request->except('profile_img', '_method'), $paths)
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
