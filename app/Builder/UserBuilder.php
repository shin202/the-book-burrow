<?php

namespace App\Builder;

use App\Enums\UserRoleEnum;
use App\Traits\TimeUnitToSqlFormat;
use Illuminate\Database\Eloquent\Builder;

class UserBuilder extends Builder
{
    use TimeUnitToSqlFormat;

    public function __construct($query)
    {
        parent::__construct($query);
    }

    public function whereUsernameEqual(string $username): self
    {
        return $this->where('username', $username);
    }

    public function whereUsernameStartsWith(string $username = null): self
    {
        return $this->when($username, function ($query) use ($username) {
            return $query->where('username', 'like', "$username%");
        });
    }

    public function whereGender(string $gender, string $operator = '='): self
    {
        return $this->whereHas('profile', function ($query) use ($gender, $operator) {
            $query->where('gender', $operator, $gender);
        });
    }

    public function customers(): self
    {
        return $this->whereDoesntHave('roles', function ($query) {
            $query->where('value', UserRoleEnum::ADMINISTRATOR->value);
        });
    }

    public function administrators(): self
    {
        return $this->whereHas('roles', function ($query) {
            $query->where('value', UserRoleEnum::ADMINISTRATOR->value);
        });
    }

    public function customersBy(string $unit = 'months', array $range = [])
    {
        $params = collect($range)->map(fn() => '?')->implode(',');
        $query = "DATE_FORMAT(created_at, '{$this->toSqlFormat($unit)}') IN ($params)";

        return $this->whereDoesntHave('roles', function ($query) {
            $query->where('value', UserRoleEnum::ADMINISTRATOR->value);
        })
            ->whereRaw($query, $range)
            ->selectRaw("DATE_FORMAT(created_at, '{$this->toSqlFormat($unit)}') as time")
            ->selectRaw('COUNT(*) as total_customers')
            ->groupBy('time')
            ->get();
    }
}
