<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'user:create-admin {email} {name} {password}';
    protected $description = 'Create a new admin user';

    public function handle()
    {
        return DB::transaction(function () {
            $email = $this->argument('email');
            
            $user = User::where('email', $email)->first();
            
            if ($user) {
                $this->error('User already exists!');
                return 1;
            }

            // Show available companies
            $companies = Company::all(['id', 'name']);
            if ($companies->isEmpty()) {
                $this->error('No companies found. Please create a company first.');
                return 1;
            }

            $this->info('Available companies:');
            foreach ($companies as $company) {
                $this->line("{$company->id}: {$company->name}");
            }

            $companyId = $this->ask('Enter company ID (numeric):');
            
            if (!Company::find($companyId)) {
                $this->error('Invalid company ID!');
                return 1;
            }

            $admin = User::create([
                'name' => $this->argument('name'),
                'email' => $this->argument('email'),
                'password' => Hash::make($this->argument('password')),
                'role' => 'Admin',
                'company_id' => $companyId
            ]);
            
            $this->info("User {$email} has been created as Admin!");
            return 0;
        });
    }
}