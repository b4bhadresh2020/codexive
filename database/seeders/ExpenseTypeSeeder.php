<?php

namespace Database\Seeders;

use App\Models\ExpenseType;
use Illuminate\Database\Seeder;

class ExpenseTypeSeeder extends Seeder
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
                'name' => 'Office Expense'
            ],
            [
                'name' => 'Stationery Expense'
            ],
            [
                'name' => 'Maintenance Expense'
            ],
            [
                'name' => 'Other Expense'
            ]
        ];

        ExpenseType::insert($data);
    }
}
