@extends('layouts.app')
@section('pagename', 'File Browser')

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card card-primary card-outline">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Uploaded GCODE Files</h3>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadModal">➕ Upload File</button>
            </div>

            <div class="card-body p-0" id="file-list">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Filename</th>
                            <th>Size (KB)</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody id="file-table-body">
                        @forelse ($files as $index => $file)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $file['name'] }}</td>
                                <td>{{ round($file['size'] / 1024, 2) }}</td>
                                <td>
                                    <form action="{{ route('files.destroy', $file['name']) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>

                                    <form action="{{ route('files.send', $file['name']) }}" method="POST" class="d-inline-block ms-2">
                                        @csrf
                                        <select name="printer_id" class="form-select form-select-sm d-inline-block w-auto" required>
                                            <option value="">Printer</option>
                                            @foreach($printers as $printer)
                                                <option value="{{ $printer->id }}">{{ $printer->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm">Send</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center">No files found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data" action="{{ route('files.store') }}" id="uploadForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Upload GCODE</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const uploadForm = document.getElementById('uploadForm');

            uploadForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(uploadForm);

                fetch('{{ route('files.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Upload failed');
                        return response.text();
                    })
                    .then(() => {
                        // Refresh the file list without page reload
                        fetch('{{ route('files.index') }}')
                            .then(res => res.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newFileList = doc.querySelector('#file-list').innerHTML;
                                document.querySelector('#file-list').innerHTML = newFileList;
                                uploadForm.reset();
                                bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
                            });
                    })
                    .catch(err => alert(err.message));
            });
        });
    </script>
    <script>
        setInterval(function() {
            fetch('{{ route('files.index') }}')
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newFileList = doc.querySelector('#file-list').innerHTML;
                    document.querySelector('#file-list').innerHTML = newFileList;
                });
        }, 3000); // every 30 seconds
    </script>
@endsection
