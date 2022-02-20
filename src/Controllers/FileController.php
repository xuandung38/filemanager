<?php

namespace Thainph\Filemanager\Controllers;

use Illuminate\Http\Request;
use Thainph\Filemanager\Requests\ChunkUploadRequest;
use Thainph\Filemanager\Services\FileService;

class FileController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function browser(Request $request)
    {
        $userConfig = $this->fileService->detectUser();

        return view('file-manager::browser', [
            'hasEditor' => $request->has('CKEditor'),
            'hasSelector' => $request->has('selector'),
            'userDirectory' => $userConfig['userDirectory'],
        ]);
    }

    public function discover(Request $request)
    {
        $userConfig = $this->fileService->detectUser();
        $directory = $request->input('dir', '');

        if (strpos($directory, $userConfig['userDirectory']) === false) {
            return abort(403, trans('file-manager::file-manger.dir_incorrect'));
        }

        return response()->json($this->fileService->browser($directory));
    }

    public function makeDirectory(Request $request)
    {
        $userConfig = $this->fileService->detectUser();
        $directory = $request->input('dir', '');
        $directoryName = $request->input('name', '');

        if (strpos($directory, $userConfig['userDirectory']) === false) {
            abort(403, trans('file-manager::file-manger.dir_incorrect'));
        }

        if ($directoryName === '') {
            abort(422, trans('file-manager::file-manger.name_required'));
        }

        return response()->json($this->fileService->makeDirectory($directory, $directoryName));
    }

    public function chunkUpload(ChunkUploadRequest $request)
    {
        $userConfig = $this->fileService->detectUser();
        $directory = $request->input('dir');

        if (strpos($directory, $userConfig['userDirectory']) === false) {
            abort(403, trans('file-manager::file-manger.dir_incorrect'));
        }

        return response()->json($this->fileService->uploadChunk($request->parameters(), $directory));
    }

    public function singleUpload(Request $request)
    {
        $userConfig = $this->fileService->detectUser();
        $directory = $request->input('dir', '');

        if (strpos($directory, $userConfig['userDirectory']) === false) {
            abort(403, trans('file-manager::file-manger.dir_incorrect'));
        }

        if (!$request->hasFile('file')) {
            abort(422, trans('file-manager::file-manger.file_required'));
        }

        return response()->json($this->fileService->uploadSingle($request, $directory));
    }

    public function anonymousUpload(Request $request)
    {
        if (!$request->hasFile('file')) {
            abort(422, trans('file-manager::file-manger.file_required'));
        }

        $directory = config('file-manager.directory').'/'.config('file-manager.anonymous_folder');

        return response()->json($this->fileService->uploadSingle($request, $directory));
    }

    public function delete(Request $request)
    {
        $userConfig = $this->fileService->detectUser();
        $directories = $request->input('directories', []);
        $files = $request->input('files', []);

        if(empty($files) && empty($directories)) {
            abort(422);
        }

        foreach ($directories as $directory) {
            if (strpos($directory, $userConfig['userDirectory']) === false) {
                abort(403, trans('file-manager::file-manger.dir_incorrect'));
            }
        }

        foreach ($files as $file) {
            if (strpos($file, $userConfig['userDirectory']) === false) {
                abort(403, trans('file-manager::file-manger.dir_incorrect'));
            }
        }

        if(!empty($files)) {
            $this->fileService->deleteFiles($files);
        }

        if(!empty($directories)) {
            $this->fileService->deleteDirectories($directories);
        }

        return response()->json([]);
    }
}
