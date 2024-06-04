<?php

namespace App\Traits;

trait GetModelColumns
{
    public function columns(): array
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
