<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterAccount extends Model
{
    use HasFactory;
    protected $guarded  = [];

    const BANK_ACCOUNT       =  1;
    const CASH_ACCOUNT       =  2;
    const PARTY_ACCOUNT      =  3;
    const INDIVIDUAL_ACCOUNT =  4;
    const PARTNER_ACCOUNT    =  5;
    const EMPLOYEE_ACCOUNT   =  6;

    public function accountType(){
        return $this->belongsTo(AccountType::class);
    }

}
