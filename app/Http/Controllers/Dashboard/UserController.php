<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserDetailsResource;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request, UserService $userService)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 5);
        $users = $userService->dashboardIndex($search, $perPage);

        return Inertia::render('Dashboard/User/Index', [
            'users' => $users
        ]);
    }

    public function create(RoleService $roleService)
    {
        $roles = $roleService->list();
        return Inertia::render('Dashboard/User/Create', [
            'roles' => $roles
        ]);
    }

    public function store(StoreUserRequest $request, UserService $userService)
    {
        $userService->store($request->validated());

        return redirect()->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'User created successfully'
            ]);
    }

    public function edit(string $username, UserService $userService, RoleService $roleService)
    {
        $roles = $roleService->list();
        $user = $userService->findByUsername($username);
        $user->load(['roles' => function ($query) {
            $query->select('roles.id');
        }, 'profile']);

        return Inertia::render('Dashboard/User/Edit', [
            'roles' => $roles,
            'user' => UserDetailsResource::make($user),
        ]);
    }

    public function update(string $username, UpdateUserRequest $request, UserService $userService)
    {
        $user = $userService->update($username, $request->validated());

        return redirect()->route('dashboard.users.edit', $user->username)
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'User updated successfully'
            ]);
    }

    public function destroy(mixed $usernames, UserService $userService)
    {
        $userService->destroy($usernames);

        return redirect()->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'User deleted successfully'
            ]);
    }

    public function export(UserService $userService)
    {
        $filePath = 'private/exports/users.xlsx';

        $userService->export($filePath, Auth::user());

        return redirect()->back()
            ->with([
                'toast.severity' => 'info',
                'toast.message' => 'Please wait.'
            ]);
    }
}
