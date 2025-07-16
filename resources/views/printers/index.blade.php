@extends('layouts.app')
@section('pagename', 'Printer List')

@section('content')
    <div class="container-fluid">
        <!-- Success Message -->
        @if ($message = Session::get('success'))
            <div class="alert alert-success">{{ $message }}</div>
        @endif

        <div class="card card-primary card-outline">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Printer List</h3>
                @can('printer-create')
                    <a class="btn btn-success" href="{{ route('printers.create') }}">➕ Add Printer</a>
                @endcan
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>IP Address</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th width="200px">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($printers as $printer)
                            <tr>
                                <td>{{ $loop->iteration + $i }}</td>
                                <td>{{ $printer->name }}</td>
                                <td>{{ $printer->ip_address }}</td>
                                <td>{{ $printer->location }}</td>
                                <td>
                                    @if ($printer->active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-info btn-sm" href="{{ route('printers.show', $printer) }}">View</a>
                                    @can('printer-edit')
                                        <a class="btn btn-primary btn-sm" href="{{ route('printers.edit', $printer) }}">Edit</a>
                                    @endcan
                                    @can('printer-delete')
                                        <form action="{{ route('printers.destroy', $printer) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                        @if($printers->isEmpty())
                            <tr><td colspan="6" class="text-center">No printers found.</td></tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer clearfix">
                {{ $printers->links() }}
            </div>
        </div>
    </div>
@endsection
