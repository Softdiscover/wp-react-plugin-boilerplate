<?php

declare(strict_types=1);

namespace Me\Plugin\Demo;

class SettingsRepository implements SettingsRepositoryInterface
{
    private $option_name;

    public function __construct(string $option_name)
    {
        $this->option_name = $option_name;
    }

    public function read(): string
    {
        return get_option($this->option_name, '');
    }

    public function update(string $data): bool
    {
        return update_option($this->option_name, $data);
    }
}
