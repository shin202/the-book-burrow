<?php

namespace App\Builder;

use Illuminate\Database\Eloquent\Builder;

class PublisherBuilder extends Builder
{
    public function __construct($query)
    {
        parent::__construct($query);
    }

    public function search(string $search = null): self
    {
        return $this->where('name', 'LIKE', $search . '%');
    }

    public function whereSlug(string $slug): self
    {
        return $this->where('slug', $slug);
    }
}
