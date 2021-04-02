<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    const CASH_TYPE             = 0;
    const CHEQUE_TYPE           = 1;
    const TRANSFER_TYPE         = 2;
    const FOREX_TYPE            = 3;

    use HasFactory;
    protected $guarded = [];

    public function toAccount(){
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function fromAccount(){
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function expenses(){
        return $this->hasMany(Expense::class,'account_id');
    }
}
