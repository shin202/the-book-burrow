<?php

namespace App\Traits;

use App\Enums\UserRoleEnum;

trait UserRoleTrait
{
    public function isAdministrator(): bool
    {
        return $this->hasRole(UserRoleEnum::ADMINISTRATOR);
    }

    public function hasRole(UserRoleEnum $role): bool
    {
        return $this->roles->pluck('value')->contains($role);
    }
}
