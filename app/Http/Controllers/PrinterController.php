<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Printer;
use Illuminate\Support\Facades\Http;

class PrinterController extends Controller
{
    public function show(Printer $printer)
    {
        $baseUrl = rtrim($printer->ip_address, '/');

        // Example: Pull current print stats, temperatures, webcam
        $printStats = Http::timeout(3)->get($baseUrl . '/printer/objects/query?print_stats')->json()['result']['status']['print_stats'] ?? [];
        $extruder = Http::timeout(3)->get($baseUrl . '/printer/objects/query?extruder')->json()['result']['status']['extruder'] ?? [];
        $bed = Http::timeout(3)->get($baseUrl . '/printer/objects/query?heater_bed')->json()['result']['status']['heater_bed'] ?? [];
        $webcams = Http::timeout(3)->get($baseUrl . '/server/webcams/list')->json()['result']['webcams'] ?? [];

        return view('printer.show', compact('printer', 'printStats', 'extruder', 'bed', 'webcams'));
    }
}
