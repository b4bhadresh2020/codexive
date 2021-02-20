<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Junges\ACL\Http\Models\Group;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin
        $adminGroup = Group::updateOrCreate(
            [
                'name' => 'Admin',
                'slug' => 'admin'
            ]
        );
        $adminGroup->assignAllPermissions();

        $admin = User::updateOrCreate(
            [
                'name' => 'Admin',
                'email' => ADMIN_EMAIL,
            ],
            [
                'password' => Hash::make(ADMIN_PASSWD),
            ]
        );
        $admin->assignGroup('admin');

        // Company
        $companyGroup = Group::updateOrCreate(
            [
                'name' => 'Company',
                'slug' => 'company'
            ]
        );
        $companyGroup->assignAllPermissions();
        $companyGroup->revokePermissions(['add-account', 'edit-account', 'delete-account', 'view-account']);

        $company = User::updateOrCreate(
            [
                'name' => 'company',
                'email' => COMPANY_EMAIL,
            ],
            [
                'password' => Hash::make(ADMIN_PASSWD),
            ]
        );
        $company->assignGroup('company');

        // Employee
        $employeeGroup = Group::updateOrCreate(
            [
                'name' => 'Employee',
                'slug' => 'employee'
            ]
        );
        $employeeGroup->revokeAllPermissions();
        $employee = User::updateOrCreate(
            [
                'name' => 'employee',
                'email' => 'employee@gmail.com',
            ],
            [
                'password' => Hash::make(ADMIN_PASSWD),
            ]
        );
        $employee->assignGroup('employee');
    }
}
