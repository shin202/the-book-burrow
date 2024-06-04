<?php

namespace App\Traits;

trait UpdateOnlyColumn
{
    /**
     * Update only the specified columns.
     *
     * @param array $data
     * @return void
     */
    public function updateOnly(array $data): void
    {
        foreach ($data as $key => $value) {
            if (empty($value) || $this->$key === $value) {
                continue;
            }

            $this->$key = $value;
        }

        $this->save();
    }
}
