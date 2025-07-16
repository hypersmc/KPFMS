@extends('layouts.app')
@section('pagename', 'Edit Printer')

@section('content')
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Edit Printer - {{ $printer->name }}</h3>
                <a href="{{ route('printers.index') }}" class="btn btn-secondary">⬅️ Back to List</a>
            </div>

            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('printers.update', $printer) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-3">
                        <label for="name">Printer Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $printer->name) }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="ip_address">Printer API Address</label>
                        <input type="text" name="ip_address" class="form-control" value="{{ old('ip_address', $printer->ip_address) }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="location">Location</label>
                        <input type="text" name="location" class="form-control" value="{{ old('location', $printer->location) }}">
                    </div>

                    <div class="form-group mb-3">
                        <label for="type">Printer Type</label>
                        <input type="text" name="type" class="form-control" value="{{ old('type', $printer->type) }}">
                    </div>

                    <div class="form-group mb-3">
                        <label for="notes">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $printer->notes) }}</textarea>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="active" value="1" id="active"
                            {{ old('active', $printer->active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="active">Active</label>
                    </div>

                    <button type="submit" class="btn btn-primary">💾 Update Printer</button>
                </form>
            </div>
        </div>
    </div>
@endsection
