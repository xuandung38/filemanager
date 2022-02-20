<?php
namespace HXD\Filemanager\Repositories;

use Illuminate\Support\Facades\Storage;
use HXD\Filemanager\Repositories\Interfaces\FileRepositoryInterface;

class LocalRepository implements FileRepositoryInterface
{
    const DISK = 'local';

    public function listDirectories(string $path)
    {
        return  Storage::disk(self::DISK)->directories($path);
    }

    public function listFiles(string $path)
    {
        return  Storage::disk(self::DISK)->files($path);
    }

    public function fileUrl(string $path)
    {
        return  Storage::disk(self::DISK)->url($path);
    }

    public function fileMimeType(string $path)
    {
        return Storage::disk(self::DISK)->mimeType($path);
    }

    public function fileSize(string $path)
    {
        return Storage::disk(self::DISK)->size($path);
    }

    public function lastModifiedDate(string $path)
    {
        return Storage::disk(self::DISK)->lastModified($path);
    }

    public function upload(string $destination, $fileData)
    {
        return Storage::disk(self::DISK)->put($destination, $fileData);
    }

    public function deleteFiles(array $filePaths)
    {
        return Storage::disk(self::DISK)->delete($filePaths);
    }

    public function deleteDirectory(string $directoryPath)
    {
        return Storage::disk(self::DISK)->deleteDirectory($directoryPath);
    }

    public function makeDirectory(string $directory)
    {
        return Storage::disk(self::DISK)->makeDirectory($directory);
    }

    public function getRealPath($path) {
        return storage_path('app/'.$path);
    }

    public function mergeChunkFiles(Array $chunkFiles, string $fileName)
    {
        $file = fopen($this->getRealPath($fileName), 'a+');

        for($i = 0; $i < count($chunkFiles); $i++) {
            $chunkFile = $this->getRealPath($chunkFiles[$i]);
            $chunkData = base64_decode(file_get_contents($chunkFile));
            fwrite($file, $chunkData);
            unlink($chunkFile);
        }

        fclose($file);
    }
}
