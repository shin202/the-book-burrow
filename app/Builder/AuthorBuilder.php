<?php

namespace App\Builder;

use Illuminate\Database\Eloquent\Builder;

class AuthorBuilder extends Builder
{
    public function __construct($query)
    {
        parent::__construct($query);
    }

    public function whereSlug(string $slug)
    {
        return $this->where('slug', $slug);
    }

    public function whereFirstNameOrLastNameStartsWith(string $value = null): self
    {
        return $this->when($value, function ($query) use ($value) {
            return $query->where('first_name', 'like', "%$value%")
                ->orWhere('last_name', 'like', "%$value%");
        });
    }
}
