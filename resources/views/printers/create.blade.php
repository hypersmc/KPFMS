@extends('layouts.app')
@section('pagename', 'Add Printer')

@section('content')
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Add New Printer</h3>
                <a href="{{ route('printers.index') }}" class="btn btn-secondary float-end">⬅️ Back</a>
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

                <form action="{{ route('printers.store') }}" method="POST">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="name">Printer Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="ip_address">Printer API Address (http://ip or https://domain)</label>
                        <input type="text" name="ip_address" class="form-control" value="{{ old('ip_address') }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="location">Location</label>
                        <input type="text" name="location" class="form-control" value="{{ old('location') }}">
                    </div>

                    <div class="form-group mb-3">
                        <label for="type">Printer Type</label>
                        <input type="text" name="type" class="form-control" value="{{ old('type') }}">
                    </div>

                    <div class="form-group mb-3">
                        <label for="notes">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="active" id="active" value="1" {{ old('active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="active">Active</label>
                    </div>

                    <button type="submit" class="btn btn-success">✅ Create Printer</button>
                </form>
            </div>
        </div>
    </div>
@endsection
