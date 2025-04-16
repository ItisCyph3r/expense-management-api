<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdmin extends Command
{
    protected $signature = 'admin:create-super {name} {email} {password}';
    protected $description = 'Create a new super admin user with specified details';

    public function handle()
    {
        $email = $this->argument('email');
        
        if (User::where('email', $email)->exists()) {
            $this->error('User already exists!');
            return 1;
        }

        try {
            User::create([
                'name' => $this->argument('name'),
                'email' => $email,
                'password' => Hash::make($this->argument('password')),
                'role' => 'Super_Admin',
                'company_id' => null
            ]);

            $this->info("Super Admin {$email} created successfully!");
            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to create Super Admin: " . $e->getMessage());
            return 1;
        }
    }
}