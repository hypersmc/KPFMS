@extends('layouts.app')
@section('pagename', 'Printer Details')

@section('content')
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">{{ $printer->name }} - Details</h3>
                <a href="{{ route('printers.index') }}" class="btn btn-secondary">⬅️ Back to List</a>
            </div>

            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Name</dt>
                    <dd class="col-sm-9">{{ $printer->name }}</dd>

                    <dt class="col-sm-3">IP Address</dt>
                    <dd class="col-sm-9">{{ $printer->ip_address }}</dd>

                    <dt class="col-sm-3">Location</dt>
                    <dd class="col-sm-9">{{ $printer->location ?? '-' }}</dd>

                    <dt class="col-sm-3">Type</dt>
                    <dd class="col-sm-9">{{ $printer->type ?? '-' }}</dd>

                    <dt class="col-sm-3">Notes</dt>
                    <dd class="col-sm-9">{{ $printer->notes ?? '-' }}</dd>

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">
                        @if($printer->active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Last Seen At</dt>
                    <dd class="col-sm-9">
                        {{ $printer->last_seen_at ? $printer->last_seen_at->diffForHumans() : 'Never' }}
                    </dd>
                </dl>
            </div>
        </div>
    </div>
@endsection
