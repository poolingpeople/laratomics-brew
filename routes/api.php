<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| GET /info
|--------------------------------------------------------------------------
| Get generic information about the main Laravel app, that should be used or
| displayed in the workshop
*/
Route::get('info', 'ApplicationController@info');

/*
|--------------------------------------------------------------------------
| POST /pattern
|--------------------------------------------------------------------------
| Store a newly created Pattern.
*/
Route::post('pattern', 'PatternController@store');

/*
|--------------------------------------------------------------------------
| GET /{pattern}
|--------------------------------------------------------------------------
| Get all all information for the preview screen.
| Except the actual rendered preview, that will be loaded by an iframe.
*/
Route::get('{pattern}', 'PreviewController@getPreview');

