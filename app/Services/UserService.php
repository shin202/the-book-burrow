<?php

namespace App\Services;

use App\Enums\AccountStatusEnum;
use App\Exports\UserExport;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserListResource;
use App\Jobs\ExportJob;
use App\Jobs\NotifyUserOfCompletedExportJob;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function __construct(protected User $user, protected Role $role)
    {
    }

    public function list(): AnonymousResourceCollection
    {
        $users = Cache::remember('users_list', 60 * 60, function () {
            return $this->user->all(['id', 'username']);
        });

        return UserListResource::collection($users);
    }

    public function customerList(): AnonymousResourceCollection
    {
        $users = $this->user->customers()->get(['id', 'username']);

        return UserListResource::collection($users);
    }

    public function dashboardIndex(string $search = null, int $perPage = 5)
    {
        $users = $this->user->with('profile')
            ->customers()
            ->whereUsernameStartsWith($search)
            ->paginate($perPage);

        return UserCollection::make($users);
    }

    public function store(array $data)
    {
        $userData = [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'email_verified_at' => now(),
            'status' => AccountStatusEnum::ACTIVE,
        ];
        $profileData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'gender' => $data['gender'],
            'date_of_birth' => $data['date_of_birth'],
            'country' => $data['country'],
        ];

        $user = $this->user->create($userData);
        $role = $this->role->findOrFail($data['role_id']);
        $user->roles()->attach($role);
        $user->profile()->create($profileData);

        return $user;
    }

    public function update(string $username, array $data)
    {
        $user = $this->findByUsername($username);
        $profile = $user->profile;

        $userData = [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
        ];
        $profileData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'gender' => $data['gender'],
            'date_of_birth' => $data['date_of_birth'],
            'country' => $data['country'],
        ];

        !empty($data['role_id']) && $user->roles()->sync($data['role_id']);

        $user->updateOnly($userData);

        if (empty($profile)) {
            $user->profile()->create($profileData);
        } else {
            $profile->updateOnly($profileData);
        }

        return $user;
    }

    public function findByUsername(string $username)
    {
        return $this->user->whereUsernameEqual($username)->firstOrFail();
    }

    public function destroy(mixed $username): void
    {
        $username = explode(',', $username);
        $users = $this->user->whereIn('username', $username);

        foreach ($username as $each) {
            Storage::deleteDirectory('uploads/users/' . $each);
        }

        $users->delete();
    }

    public function export(string $filePath, User $user): void
    {
        Bus::chain([
            new ExportJob(new UserExport(), $filePath),
            new NotifyUserOfCompletedExportJob($user, $filePath),
        ])->dispatch();
    }
}
