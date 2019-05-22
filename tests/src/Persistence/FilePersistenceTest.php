<?php


namespace Test\Language\Persistence;


use Language\Persistence\FilePersistence;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class FilePersistenceTest extends TestCase
{
    public function test_should_create_file(): void
    {
        $filePersistence = new FilePersistence(__DIR__);
        $saved = $filePersistence->save('/somedir/myfile.txt', 'test');

        Assert::assertTrue($saved);
        Assert::assertEquals(__DIR__ . '/somedir/myfile.txt', $filePersistence->getLastSavedFilepath());
        Assert::assertFileExists(__DIR__ . '/somedir/myfile.txt');

        unlink(__DIR__ . '/somedir/myfile.txt');
        rmdir(__DIR__ . '/somedir/');

        Assert::assertFileNotExists(__DIR__ . '/somedir/myfile.txt');
    }
}
