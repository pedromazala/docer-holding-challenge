<?php


namespace Language\Persistence;


class FilePersistence implements Persistence
{
    /** @var string */
    private $last_saved_filepath;
    /** @var string */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function save(string $file_name, string $contents): bool
    {
        $this->last_saved_filepath = $this->path . $file_name;
        $this->createDirectory();

        $result = file_put_contents($this->last_saved_filepath, $contents);

        return !($result === false);
    }

    public function getLastSavedFilepath(): string
    {
        return $this->last_saved_filepath;
    }

    private function createDirectory(): void
    {
        if (!is_dir(dirname($this->last_saved_filepath))) {
            mkdir(dirname($this->last_saved_filepath), 0755, true);
        }
    }
}
