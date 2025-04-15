<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create a demo company
        $company = Company::create([
            'name' => 'Admin Company',
            'email' => 'emergencymoss@gmail.com',
        ]);

        // // Create an employee user
        // $employee = User::create([
        //     'name' => 'Demo Employee',
        //     'email' => 'employee@demo.com',
        //     'password' => Hash::make('password'),
        //     'company_id' => $company->id,
        // ]);

        // // Create some sample expenses
        // Expense::create([
        //     'company_id' => $company->id,
        //     'user_id' => $employee->id,
        //     'title' => 'Office Supplies',
        //     'amount' => 150.00,
        //     'category' => 'Supplies'
        // ]);

        // Expense::create([
        //     'company_id' => $company->id,
        //     'user_id' => $employee->id,
        //     'title' => 'Team Lunch',
        //     'amount' => 75.50,
        //     'category' => 'Meals'
        // ]);
    }
}