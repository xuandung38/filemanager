<?php

use Illuminate\Support\Facades\Route;

// Files
Route::group([
    'middleware' => ['web'],
    'prefix' => 'file-manager',
    'as' => 'file-manager.',
    'namespace' => 'Thainph\Filemanager\Controllers'
], function() {
    Route::get('/browser', 'FileController@browser')->name('browser');
    Route::get('/discover', 'FileController@discover')->name('discover');
    Route::post('/mkdir', 'FileController@makeDirectory')->name('mkdir');
    Route::post('/delete', 'FileController@delete')->name('delete');

    Route::group(['middleware' => ['file_manager_mimes']], function() {
        Route::post('/single-upload', 'FileController@singleUpload')->name('single_upload');
        Route::post('/chunk-upload', 'FileController@chunkUpload')->name('chunk_upload');

    });
});

if (config('file-manager.anonymous_upload')) {
    Route::group([
        'middleware' => [
            'file_manager_cors',
            'file_manager_encrypt_cookies',
            'file_manager_queued_cookies',
            'file_manager_start_session',
            'file_manager_share_session_errors',
            'file_manager_substitute_bindings',
        ],
        'prefix' => 'file-manager',
        'as' => 'file-manager.',
        'namespace' => 'Thainph\Filemanager\Controllers'
    ], function() {
        Route::options('/anonymous-upload', function () { return response()->json(); });

        Route::group(['middleware' => ['file_manager_mimes']], function() {
            Route::post('/anonymous-upload', 'FileController@anonymousUpload')->name('anonymous_upload');
        });

    });
}
