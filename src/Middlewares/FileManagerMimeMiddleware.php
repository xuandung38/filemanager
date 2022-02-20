<?php

namespace Thainph\Filemanager\Middlewares;

use Closure;
use Illuminate\Http\Request;

class FileManagerMimeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $mimes = config('file-manager.mimes');
        $mime = $request->input('mime', '');

        if(!in_array($mime, $mimes)) {
            return abort(403, trans('file-manager::file-manger.mime_not_allowed'));
        }

        return $next($request);
    }
}
