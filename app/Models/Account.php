<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function accountType(){
        return $this->belongsTo(AccountType::class, 'type');
    }

    public function user(){
        return $this->hasOne(User::class);
    }
}
