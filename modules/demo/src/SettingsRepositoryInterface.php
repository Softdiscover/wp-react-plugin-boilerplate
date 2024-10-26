<?php

declare(strict_types=1);

namespace Me\Plugin\Demo;

interface SettingsRepositoryInterface
{
    /**
     * Read
     */
    public function read(): string;


    /**
     * Update
     * @param string $data Data to update
     */
    public function update(string $data): bool;
}
