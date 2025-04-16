<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function creating(User $user)
    {
        if ($user->role !== 'Super_Admin' && is_null($user->company_id)) {
            throw new \Exception('Only Super Admin can exist without a company');
        }
    }

    public function updating(User $user)
    {
        if ($user->role !== 'Super_Admin' && is_null($user->company_id)) {
            throw new \Exception('Only Super Admin can exist without a company');
        }
    }
}