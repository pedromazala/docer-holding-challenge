<?php


namespace Test\Support\Language\Persistence;


use Language\Persistence\Persistence;

class FakeErrorPersistence implements Persistence
{
    /** @var string */
    private $path;
    /** @var string */
    private $last_saved_filepath;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function save(string $file_name, string $contents): bool
    {
        $this->last_saved_filepath = $this->path . $file_name;
        return false;
    }

    public function getLastSavedFilepath(): string
    {
        return $this->last_saved_filepath;
    }
}
