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

    public function account(){
        return $this->belongsTo(Account::class);
    }
}
