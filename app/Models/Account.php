<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function accountType(){
        return $this->belongsTo(AccountType::class);
    }

    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function masterAccount(){
        return $this->belongsTo(MasterAccount::class);
    }
}
