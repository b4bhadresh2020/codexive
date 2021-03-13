<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'Bank Account'
            ],
            [
                'name' => 'Cash Account'
            ],
            [
                'name' => 'Party Account'
            ],
            [
                'name' => 'Individual Account'
            ],
            [
                'name' => 'Partner Account'
            ],
            [
                'name' => 'Employee Account'
            ]
        ];

        AccountType::insert($data);
    }
}
