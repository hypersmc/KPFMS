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

        // Print Stats
        $printStats = Http::timeout(3)->get($baseUrl . '/printer/objects/query?print_stats')->json()['result']['status']['print_stats'] ?? [];

        // Temperature History
        $tempStore = Http::timeout(3)->get($baseUrl . '/server/temperature_store')->json()['result'] ?? [];

        // Extract Extruder Temps
        $extruderTemps = $tempStore['extruder']['temperatures'] ?? [];
        $extruderTargets = $tempStore['extruder']['targets'] ?? [];
        $latestHotendTemp = end($extruderTemps) ?: 0;
        $latestHotendTarget = end($extruderTargets) ?: 0;

        // Extract Bed Temps
        $bedTemps = $tempStore['heater_bed']['temperatures'] ?? [];
        $bedTargets = $tempStore['heater_bed']['targets'] ?? [];
        $latestBedTemp = end($bedTemps) ?: 0;
        $latestBedTarget = end($bedTargets) ?: 0;

        // Progress
        $progress = 0;
        if (!empty($printStats['total_duration']) && $printStats['total_duration'] > 0) {
            $progress = round(($printStats['print_duration'] / $printStats['total_duration']) * 100, 1);
        }
        if (($printStats['state'] ?? '') === 'complete') {
            $progress = 100;
        }

        // Webcams
        $webcamsRaw = Http::timeout(3)->get($baseUrl . '/server/webcams/list')->json()['result']['webcams'] ?? [];

        $webcams = collect($webcamsRaw)->map(function ($cam) use ($baseUrl) {
            $cam['full_stream_url'] = $baseUrl . $cam['stream_url'];
            return $cam;
        })->toArray();
        return view('printer.show', compact(
            'printer',
            'printStats',
            'progress',
            'extruderTemps', 'latestHotendTemp', 'latestHotendTarget',
            'bedTemps', 'latestBedTemp', 'latestBedTarget',
            'webcams'
        ));
    }
    public function temperatures(Printer $printer)
    {
        $baseUrl = rtrim($printer->ip_address, '/');
        $tempStore = Http::timeout(3)->get($baseUrl . '/server/temperature_store')->json()['result'] ?? [];

        $array = $tempStore['extruder']['temperatures'] ?? [0];
        $array1 = $tempStore['heater_bed']['temperatures'] ?? [0];
        return response()->json([
            'hotend' => end($array),
            'bed' => end($array1),
        ]);
    }
    public function temperatureData(Printer $printer)
    {
        $baseUrl = rtrim($printer->ip_address, '/');
        $tempStore = Http::timeout(3)->get($baseUrl . '/server/temperature_store')->json()['result'] ?? [];

        $temperatures = [];
        foreach ($tempStore as $probe => $data) {
            if (isset($data['temperatures'])) {
                $temperatures[$probe] = [
                    'latest' => end($data['temperatures']),
                    'history' => $data['temperatures']
                ];
            }
        }

        return response()->json(['temperatures' => $temperatures]);
    }
}
