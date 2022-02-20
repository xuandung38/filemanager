<?php
namespace Thainph\Filemanager\Repositories\Interfaces;


interface FileRepositoryInterface
{
    public function listDirectories(String $path);
    public function listFiles(String $path);
    public function fileUrl(String $path);
    public function fileSize(String $path);
    public function fileMimeType(String $path);
    public function lastModifiedDate(String $path);
    public function upload(String $destination, $fileData);
    public function deleteFiles(Array $filePaths);
    public function deleteDirectory(String $directoryPath);
    public function makeDirectory(String $directory);
    public function mergeChunkFiles(Array $chunkFiles, String $file);
}
