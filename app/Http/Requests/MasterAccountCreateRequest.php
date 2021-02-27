<?php

namespace App\Http\Requests;

use App\Http\Requests\Api\StepRequest;
use App\Http\Responses\ApiResponse;
use App\Models\MasterAccount;

class MasterAccountCreateRequest extends StepRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        switch ($this->account_type_id) {
            case MasterAccount::BANK_ACCOUNT:
                $rules = array_merge(
                    $rules,
                    [
                        'name'                      => 'required',
                        'branch'                    => 'required',
                        'ifsc'                      => 'required'
                    ]
                );
                break;
            case MasterAccount::CASH_ACCOUNT:
                $rules = array_merge(
                    $rules,
                    [
                        'name'                      => 'required',
                    ]
                );
                break;
            case MasterAccount::PARTY_ACCOUNT:
            case MasterAccount::INDIVIDUAL_ACCOUNT:
            case MasterAccount::PARTNER_ACCOUNT:
                $rules = array_merge(
                    $rules,
                    [
                        'name'                      => 'required',
                        'email'                     => 'required|email'
                    ]
                );
                break;
            case MasterAccount::EMPLOYEE_ACCOUNT:
                $rules = array_merge(
                    $rules,
                    [
                        'name'                      =>  'required',
                        'email'                     =>   'required|email',
                        'mobile_no'                 =>  'required',
                        'profile_img'               =>  'sometimes|image|mimes:jpg,bmp,png',
                        'aadhar_img'               =>  'sometimes|image|mimes:jpg,bmp,png',
                        'pan_img'                   =>  'sometimes|image|mimes:jpg,bmp,png'

                    ]
                );
                break;
            default:
                # code...
                break;
        }
        return $rules;
    }

    public function messages()
    {
        return [];
    }

    public function response(array $errors)
    {
        return ApiResponse::create($errors, false, ApiResponse::VALIDATION);
    }
}
