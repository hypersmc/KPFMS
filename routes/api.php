<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// Faking slicer upload system to believe that we're a true 3d-printer with OctoPrint (Moonraker) but we're not to allow for upload of files
Route::get('/version', function () {
    return response()->json([
        "server" => "1.5.0",
        "api" => "0.1",
        "text" => "OctoPrint (Moonraker v0.9.3-25-gfad1a15)"
    ]);
});
Route::post('/files/local', function (Request $request) {
    if (!$request->hasFile('file')) {
        return response()->json(['error' => 'No file uploaded'], 400);
    }

    $file = $request->file('file');
    $path = $file->storeAs('gcodes', $file->getClientOriginalName());

    return response()->json([
        "done" => true,
        "files" => [[
            "name" => $file->getClientOriginalName(),
            "path" => $path,
            "type" => "machinecode",
            "size" => $file->getSize(),
            "origin" => "local"
        ]]
    ]);
});
