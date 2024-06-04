<?php

namespace App\Builder;

use App\Enums\BannerStatusEnum;
use Illuminate\Database\Eloquent\Builder;

class BannerBuilder extends Builder
{
    public function __construct($query)
    {
        parent::__construct($query);
    }

    public function whereTitleStartsWith(string $title = null)
    {
        return $this->when($title, function ($query) use ($title) {
            return $query->where('title', 'like', "$title%");
        });
    }

    public function whereStatus(BannerStatusEnum $status): self
    {
        return $this->where('status', $status->value);
    }
}
