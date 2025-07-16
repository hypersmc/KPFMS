<?php

namespace App\Http\Controllers;
use App\Models\Printer;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


    public function index()
    {
        $printers = Printer::orderBy('name')->get();

        // Top-level counters
        $totalPrinters = $printers->count();
        $currentlyPrinting = 0;
        $queueJobs = 0; // You can customize based on your system
        $completedToday = 0; // Optional - depends on available data

        // Augment printer data with Moonraker API
        $printers = $printers->map(function($printer) use (&$currentlyPrinting) {
            $baseUrl = rtrim($printer->ip_address, '/');

            try {
                $state = 'unknown';
                $hotend = null;
                $bed = null;

                $printStats = Http::timeout(3)->get($baseUrl . '/printer/objects/query?print_stats')->json();
                if (isset($printStats['result']['status']['print_stats'])) {
                    $state = $printStats['result']['status']['print_stats']['state'] ?? 'unknown';
                }
                $printer->current_file = !empty($status['filename']) ? basename($status['filename']) : null;

                $print_duration = $printStats['result']['status']['print_stats']['print_duration'] ?? 0;
                $total_duration = $printStats['result']['status']['print_stats']['total_duration'] ?? 0;

                if ($total_duration > 0) {
                    $printer->progress = round(($print_duration / $total_duration) * 100, 1);
                } else {
                    $printer->progress = null;
                }
                $extruderStats = Http::timeout(3)->get($baseUrl . '/printer/objects/query?extruder')->json();
                $hotend = $extruderStats['result']['status']['extruder']['temperature'] ?? null;

                $bedStats = Http::timeout(3)->get($baseUrl . '/printer/objects/query?heater_bed')->json();
                $bed = $bedStats['result']['status']['heater_bed']['temperature'] ?? null;

                if ($state === 'printing') {
                    $currentlyPrinting++;
                }
                $camResponse = Http::timeout(3)->get($baseUrl . '/server/webcams/list')->json();
                $printer->webcams = collect($camResponse['result']['webcams'] ?? [])
                    ->filter(fn($cam) => $cam['enabled'])
                    ->map(function ($cam) use ($printer) {
                        $cam['full_stream_url'] = rtrim($printer->ip_address, '/') . $cam['stream_url'];
                        return $cam;
                    })
                    ->values();

                $printer->state = $state;
                $printer->hotend_temp = $hotend;
                $printer->bed_temp = $bed;
                $printer->tags = explode(',', $printer->type);

            } catch (\Exception $e) {
                $printer->state = 'offline';
                $printer->hotend_temp = null;
                $printer->bed_temp = null;
                $printer->tags = [];
            }

            return $printer;
        });
        return view('home', compact(
            'printers',
            'totalPrinters',
            'currentlyPrinting',
            'queueJobs',
            'completedToday'
        ));
    }

}
