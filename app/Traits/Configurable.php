<?php

namespace App\Traits;

use Illuminate\Support\Arr;

trait Configurable
{
    /**
     * Set the user settings.
     *
     * @param array $settings
     * @return $this
     */
    public function settings(array $settings = []): static
    {
        foreach ($settings as $key => $value) {
            Arr::set($this->settings, $key, $value);
        }

        $this->save();

        return $this;
    }

    /**
     * Get the user setting by given key.
     *
     * @param string $key
     * @param $default
     * @return mixed|null
     */
    public function setting(string $key, $default = null): mixed
    {
        return Arr::get($this->settings ?? [], $key, $default);
    }
}
