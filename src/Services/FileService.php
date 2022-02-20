<?php
namespace HXD\Filemanager\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use HXD\Filemanager\Repositories\LocalRepository;

class FileService
{
    private $repository;

    public function __construct(LocalRepository $repository)
    {
        $this->repository =  $repository;
    }

    public function detectUser()
    {
        $guards = config('file-manager.guards');
        $user = null;
        $userDirectory = config('file-manager.directory').'/'.config('file-manager.anonymous_folder');

        foreach ($guards as $guard) {
            if(auth($guard)->check()) {
                $user = auth($guard)->user();
                $userDirectory = config('file-manager.directory').'/'.md5($guard.'-'.$user->id);
                break;
            }
        }

        if($user === null) {
            abort(403, trans('file-manager::file-manger.you_have_not_logged'));
        }

        return [
            'user' => $user,
            'userDirectory' => $userDirectory
        ];
    }

    public function browser(string $directoryPath)
    {
        $directories = array_map(function ($path) {
            $separates = explode('/', $path);

            return [
                'name' => end($separates),
                'path' => $path
            ];

        }, $this->repository->listDirectories($directoryPath));

        $files = array_map(function ($file) use ($directoryPath) {
            return [
                'name' => str_replace($directoryPath . '/', '', $file),
                'url' => $this->repository->fileUrl($file),
                'path' => $file,
                'size' => $this->repository->fileSize($file),
                'mime' => $this->repository->fileMimeType($file),
                'last_modified' => $this->repository->lastModifiedDate($file),
            ];
        }, $this->repository->listFiles($directoryPath));

        return [
            'directories' => $directories,
            'files' => $files,
        ];
    }

    public function ckeditorUpload(Request $request, String $destination)
    {
        $file = $request->file('upload'); // Ckeditor using it

        if ($file->isValid()) {

            $filePath = $this->repository->upload($destination, $file);
            $url = $this->repository->fileUrl($filePath);
            $name = $file->hashName();

            return [
                'uploaded' => 1,
                'fileName' => $name,
                'url' => $url,
            ];

        }

        return abort(400, 'Cannot upload file.');
    }

    public function uploadSingle(Request $request, String $destination)
    {
        $file = $request->file('file');

        if ($file->isValid()) {

            $filePath = $this->repository->upload($destination, $file);

            return [
                'name' => $file->hashName(),
                'url' => $this->repository->fileUrl($filePath),
                'path' => $filePath,
                'size' => $this->repository->fileSize($filePath),
                'mime' => $this->repository->fileMimeType($filePath),
                'last_modified' => $this->repository->lastModifiedDate($filePath),
            ];

        }

        return abort(400, trans('file-manager::file-manger.cannot_upload_file'));
    }

    public function uploadChunk(Array $params, String $destination)
    {
        $hashName =  empty($params['hash']) ? Str::random(25) : $params['hash'];
        $chunkFilePath = $destination.'/'.$hashName.'.'.$params['offset'].'.chunk';
        $this->repository->upload($chunkFilePath, $params['data']);

        if($params['eof']) {
            $chunkFilePaths = $this->buildChunkFilePaths($hashName, $destination, $params['offset']);
            $destinationFile = $destination.'/'.$hashName.'.'. $params['type'];
            $this->repository->mergeChunkFiles($chunkFilePaths,  $destinationFile);

            return [
                'name' => $hashName.'.'. $params['type'],
                'url' => $this->repository->fileUrl($destinationFile),
                'path' => $destinationFile,
                'size' => $this->repository->fileSize($destinationFile),
                'mime' => $this->repository->fileMimeType($destinationFile),
                'last_modified' => $this->repository->lastModifiedDate($destinationFile),
                'complete' => true,
            ];
        }

        return [
            'complete' => false,
            'hashName' =>  $hashName
        ];
    }

    protected function buildChunkFilePaths($hashName, $destination, $totalPaths)
    {
        $data = [];

        for($i = 0; $i <= $totalPaths; $i++) {
            $data[] = $destination. '/' . $hashName . '.' . $i . '.chunk';
        }

        return $data;
    }

    public function deleteFiles($files)
    {
        $this->repository->deleteFiles($files);
    }

    public function deleteDirectories($directories)
    {
        if(is_array($directories)) {
            foreach ($directories as $directory) {
                $this->repository->deleteDirectory($directory);
            }
        } else {
            $this->repository->deleteDirectory($directories);
        }
    }

    public function makeDirectory(String $destination, String $directoryName)
    {
       $directory = $destination . '/' . $directoryName;
       $this->repository->makeDirectory($directory);

       return [
            'name' => $directoryName,
            'path' => $directory,
       ];
    }

}
