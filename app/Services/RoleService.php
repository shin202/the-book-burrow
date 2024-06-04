<?php

namespace App\Services;

use App\Http\Resources\Role\RoleListResource;
use App\Models\Role;

class RoleService
{
    public function __construct(protected Role $role)
    {
        //
    }

    public function list()
    {
        $roles = $this->role->all();
        return RoleListResource::collection($roles);
    }
}
