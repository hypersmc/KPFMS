@extends('layouts.app')
@section('pagename', $printer->name . ' - Printer Dashboard')

@section('content')
    <div class="container-fluid">

        <div class="row mb-3">
            <!-- Printer State Box -->
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <h5>{{ $printer->name }}</h5>
                        <span class="badge bg-primary">{{ ucfirst($printStats['state']) }}</span>
                        <p class="mt-2 mb-0"><strong>File:</strong> {{ $printStats['filename'] ?: 'Idle' }}</p>
                        <p class="mb-0"><strong>Progress:</strong> {{ $progress }}%</p>
                    </div>
                </div>
            </div>

            <!-- Hotend Temperature -->
            <div class="col-lg-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5>Hotend</h5>
                        <h3 id="hotend-temp">{{ $latestHotendTemp }}°C</h3>

                    </div>
                </div>
            </div>

            <!-- Bed Temperature -->
            <div class="col-lg-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5>Bed</h5>
                        <h3 id="bed-temp">{{ $latestBedTemp }}°C</h3>
                    </div>
                </div>
            </div>

            <!-- Duration -->
            <div class="col-lg-3">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        <h5>Print Duration</h5>
                        <h3>{{ gmdate('H:i:s', $printStats['print_duration']) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-6">
                <!-- Temperature Graph -->
                <div class="card mb-3">
                    <div class="card-header">Temperature Graph</div>
                    <div class="card-body">
                        <canvas id="temperatureChart" ></canvas>

                    </div>
                </div>

                <!-- Controls -->
                <div class="card mb-3">
                    <div class="card-header">Controls</div>
                    <div class="card-body d-flex flex-wrap gap-2">
                        <button onclick="sendAction('start')" class="btn btn-success flex-fill">Start</button>
                        <button onclick="sendAction('pause')" class="btn btn-warning flex-fill">Pause</button>
                        <button onclick="sendAction('stop')" class="btn btn-danger flex-fill">Stop</button>
                        <button onclick="sendAction('emergency_stop')" class="btn btn-outline-danger flex-fill">EM-STOP</button>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-6">
                <!-- Webcam -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ $printer->name }} - Camera</h5>
                        <select id="camSelector" class="form-select form-select-sm" style="width: auto;">
                            @foreach ($webcams as $index => $cam)
                                <option value="{{ $index }}">{{ $cam['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="card-body">
                        <img id="printerCamStream" src="{{ $webcams[0]['full_stream_url'] }}" class="img-fluid rounded border">
                    </div>
                </div>
            </div>


        </div>
@endsection
@section('script')
    <script>
        setInterval(() => {
            fetch('{{ route('printer.temperatures', $printer->id) }}')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('hotend-temp').innerText = data.hotend.toFixed(2) + '°C';
                    document.getElementById('bed-temp').innerText = data.bed.toFixed(2) + '°C';
                });
        }, 2500);
        // every 2,5 seconds
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let chart;

        // Initialize Chart
        function initChart(tempData) {
            const ctx = document.getElementById('temperatureChart').getContext('2d');
            const labels = Array(tempData[Object.keys(tempData)[0]].history.length).fill('');

            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: Object.entries(tempData).map(([probe, values], index) => ({
                        label: probe,
                        data: values.history,
                        borderWidth: 2,
                        fill: false
                    }))
                },
                options: {
                    animation: false,
                    responsive: true
                }
            });
        }

        // Polling
        function updateChart() {
            fetch('{{ route('printer.temperature-data', $printer->id) }}')
                .then(response => response.json())
                .then(({ temperatures }) => {
                    if (!chart) {
                        initChart(temperatures);
                    } else {
                        Object.keys(temperatures).forEach((probe, idx) => {
                            chart.data.datasets[idx].data = temperatures[probe].history;
                        });
                        chart.update('none');
                    }
                });
        }

        // Initial call
        updateChart();
        setInterval(updateChart, 3000);
    </script>
            <script>
                const webcams = @json($webcams);

                function updateCamera(index) {
                    const cam = webcams[index];
                    const img = document.getElementById('printerCamStream');
                    img.src = cam.full_stream_url;

                    // Handle transform
                    let transform = '';
                    if (cam.flip_horizontal) transform += ' scaleX(-1)';
                    if (cam.flip_vertical) transform += ' scaleY(-1)';
                    if (cam.rotation) transform += ` rotate(${cam.rotation}deg)`;
                    img.style.transform = transform.trim();
                }

                document.getElementById('camSelector').addEventListener('change', function () {
                    updateCamera(this.value);
                });

                updateCamera(0); // Default cam
            </script>
@endsection
