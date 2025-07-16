@extends('layouts.app')
@section('pagename', $printer->name . ' - Printer Dashboard')

@section('content')
    <div class="container-fluid">

        <!-- Printer Overview -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>{{ $printer->name }}</h3>
                <span class="badge bg-primary">{{ ucfirst($printStats['state'] ?? 'unknown') }}</span>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>File:</strong> {{ $printStats['filename'] ?: 'Idle' }}</p>
{{--                <p class="mb-0"><strong>Progress:</strong> {{ $progress }}%</p>--}}
            </div>
        </div>

        <!-- Stats Row -->
        <div class="row mb-3">
{{--            <div class="col-md-3"><div class="info-box bg-info text-white">Hotend: {{ $latestHotendTemp }}°C</div></div>--}}
{{--            <div class="col-md-3"><div class="info-box bg-warning text-dark">Bed: {{ $latestBedTemp }}°C</div></div>--}}
{{--            <div class="col-md-3"><div class="info-box bg-success text-white">Progress: {{ $progress }}%</div></div>--}}
            <div class="col-md-3"><div class="info-box bg-secondary text-white">Duration: {{ gmdate('H:i:s', $printStats['print_duration'] ?? 0) }}</div></div>
        </div>

        <!-- Temperature Chart -->
        <div class="card mb-3">
            <div class="card-header">Temperature Graph (Extruder & Bed)</div>
            <div class="card-body">
                <canvas id="temperatureChart"></canvas>
            </div>
        </div>

        <!-- Webcam -->
        @if(!empty($webcams))
            <div class="card mb-3">
                <div class="card-header">Webcam</div>
                <div class="card-body text-center">
{{--                    <img id="webcamStream" src="{{ $webcams[0]['full_stream_url'] }}" class="img-fluid rounded border">--}}
                </div>
            </div>
        @endif

        <!-- Controls -->
        <div class="card mb-3">
            <div class="card-header">Printer Controls</div>
            <div class="card-body d-flex gap-3 flex-wrap">
                <button onclick="sendAction('start')" class="btn btn-success">Start</button>
                <button onclick="sendAction('pause')" class="btn btn-warning">Pause</button>
                <button onclick="sendAction('stop')" class="btn btn-danger">Stop</button>
                <button onclick="sendAction('emergency_stop')" class="btn btn-outline-danger">EM-STOP</button>
            </div>
        </div>

    </div>
@endsection
