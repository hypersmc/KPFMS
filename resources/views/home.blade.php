@extends('layouts.app')
@section('pagename', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Top Stats -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
{{--                        <h3>{{ $totalPrinters }}</h3>--}}
                        <p>Total Printers</p>
                    </div>
                </div>
            </div>
            <!-- Repeat for currently printing, queued jobs, completed -->
        </div>

        <!-- Printer Cards -->
        <div class="row">
            @foreach($printers as $printer)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    @php
                        $stateColors = [
                            'printing' => '#28a745',
                            'standby' => '#007bff',
                            'paused' => '#ffc107',
                            'error' => '#dc3545',
                            'complete' => '#17a2b8',
                        ];
                        $color = $stateColors[$printer->state] ?? '#6c757d';
                    @endphp

                    <div class="card rounded shadow-sm overflow-hidden">
                        <!-- Top Header -->
                        <div class="p-2 fw-bold d-flex justify-content-between align-items-center text-white"
                             style="background-color: {{ $color }}">
                            <span>{{ $printer->name }}</span>
                            <span class="badge bg-light text-dark rounded-pill px-3 small">
            {{ strtoupper($printer->state) }}
        </span>
                        </div>

                        <!-- Body -->
                        <div class="p-3 bg-body-tertiary">
                            <!-- Model Name -->
                            <p class="fw-semibold mb-2">

                                {{ $printer->current_file ? str_replace('.gcode', '', $printer->current_file) : 'No file printing' }}
                            </p>

                            <!-- Progress Bar -->
                            @if($printer->progress !== null)
                                <div class="progress mb-3" style="height: 12px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                         style="width: {{ $printer->progress }}%;">
                                        {{ $printer->progress }}%
                                    </div>
                                </div>
                            @endif

                            <!-- Temperatures -->
                            <p class="mb-1"><strong>Hotend:</strong> {{ $printer->hotend_temp ?? 'N/A' }}°C</p>
                            <p class="mb-3"><strong>Bed:</strong> {{ $printer->bed_temp ?? 'N/A' }}°C</p>

                            <!-- Tags -->
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($printer->tags as $tag)
                                    <span class="badge rounded-pill bg-secondary">{{ $tag }}</span>
                                @endforeach
                            </div>
                            <div class="d-flex gap-2 mt-3">
                                @if(!empty($printer->webcams) && count($printer->webcams))
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#camModal-{{ $printer->id }}">
                                        Cam
                                    </button>
                                @endif
                                <a href="{{ route('printer.show', $printer->id) }}" class="btn btn-primary btn-sm">Enter</a>
                            </div>
                        </div>
                    </div>
                    <!-- Modal HTML -->
                    <div class="modal fade" id="camModal-{{ $printer->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ $printer->name }} - Camera</h5>
                                    <select class="form-select w-auto ms-3" id="camSelect-{{ $printer->id }}">
                                        @foreach($printer->webcams as $index => $cam)
                                            <option value="{{ $index }}">{{ $cam['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img id="camStream-{{ $printer->id }}"
                                         src="{{ $printer->webcams[0]['full_stream_url'] }}"
                                         class="img-fluid rounded border"
                                         style="transform: rotate({{ $printer->webcams[0]['rotation'] }}deg)
                              scaleX({{ $printer->webcams[0]['flip_horizontal'] ? '-1' : '1' }})
                              scaleY({{ $printer->webcams[0]['flip_vertical'] ? '-1' : '1' }});">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    </div>
@endsection

@section('script')
    <script>
        const ws = new WebSocket('ws://mainsail.zennodes.dk/websocket');

        ws.onopen = () => {
            ws.send(JSON.stringify({
                method: "server.connection.subscribe",
                params: { topics: ["server:terminal:output"] }
            }));
        };

        ws.onmessage = (event) => {
            const msg = JSON.parse(event.data);
            if (msg.method === "server:terminal:output") {
                console.log("Console Output:", msg.params.lines);
            }
        };
    </script>
    <script>
        const webcams{{ $printer->id }} = @json($printer->webcams);

        document.getElementById('camSelect-{{ $printer->id }}').addEventListener('change', function() {
            const camIndex = parseInt(this.value);
            const cam = webcams{{ $printer->id }}[camIndex];

            const stream = document.getElementById('camStream-{{ $printer->id }}');
            stream.src = cam.full_stream_url;

            const rotation = cam.rotation ?? 0;
            const flipX = cam.flip_horizontal ? -1 : 1;
            const flipY = cam.flip_vertical ? -1 : 1;

            stream.style.transform = `rotate(${rotation}deg) scaleX(${flipX}) scaleY(${flipY})`;
        });
    </script>
@endsection
