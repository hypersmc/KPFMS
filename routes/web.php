<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\PrintersController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return redirect()->route('home');
});
Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('printers', PrintersController::class);
    Route::resource('files', FileController::class);
    Route::post('/files/send/{filename}', [FileController::class, 'send'])->name('files.send');
    Route::get('/printer/{printer}', [PrinterController::class, 'show'])->name('printer.show');
    Route::get('/printer/{printer}/temperatures', [PrinterController::class, 'temperatures'])->name('printer.temperatures');
    Route::get('/printer/{printer}/temperature-data', [PrinterController::class, 'temperatureData'])->name('printer.temperature-data');

});

Route::get('/server/info', function () {
    return response()->json([
        "result" => [
            "klippy_connected" => true,
            "klippy_state" => "ready",
            "components" => [
                "secrets", "template", "klippy_connection", "jsonrpc",
                "internal_transport", "application", "websockets",
                "database", "dbus_manager", "file_manager", "authorization",
                "klippy_apis", "shell_command", "machine", "data_store",
                "proc_stats", "job_state", "job_queue", "history",
                "http_client", "announcements", "webcam", "extensions",
                "update_manager", "spoolman", "mmu_server", "timelapse",
                "octoprint_compat"
            ],
            "failed_components" => [],
            "registered_directories" => [
                "config", "logs", "gcodes", "timelapse", "timelapse_frames",
                "config_examples", "docs"
            ],
            "warnings" => [],
            "websocket_count" => 1,
            "moonraker_version" => "v0.9.3-25-gfad1a15",
            "missing_klippy_requirements" => [],
            "api_version" => [1, 5, 0],
            "api_version_string" => "1.5.0"
        ]
    ]);
});

