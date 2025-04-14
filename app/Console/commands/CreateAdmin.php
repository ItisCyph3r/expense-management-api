<?php

namespace App\Console\Commands;

use App\Models\User;
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
            
            if (!$user) {
                $this->error("User with email {$email} not found!");
                return 1;
            }
            
            $user->role = 'Admin';
            $user->save();
            
            $this->info("User {$email} has been promoted to Admin!");
            return 0;
        });
    }
}