<?php


namespace Language\Persistence;


interface Persistence
{
    public function save(string $file_name, string $contents): bool;

    public function getLastSavedFilepath(): string;
}
