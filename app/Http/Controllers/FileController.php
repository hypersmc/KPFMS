<?php

namespace App\Http\Controllers;

use App\Models\Printer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index()
    {
        $files = collect(Storage::disk('private')->files())
            ->map(function($file) {
                return [
                    'name' => basename($file),
                    'size' => Storage::disk('private')->size($file),
                    'path' => $file,
                ];
            });

        $printers = Printer::orderBy('name')->get();

        return view('files.index', compact('files', 'printers'));
    }

    public function create()
    {
        return view('files.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:gcode,txt',
        ]);

        $request->file('file')->storeAs(
            '',
            $request->file('file')->getClientOriginalName(),
            'private'
        );

        return redirect()->route('files.index')->with('success', 'File uploaded successfully.');
    }
    public function destroy($filename)
    {

        if (Storage::disk('private')->exists($filename)) {
            Storage::disk('private')->delete($filename);
            return redirect()->route('files.index')->with('success', 'File deleted.');
        }

        return redirect()->route('files.index')->withErrors(['File not found.']);
    }


    public function send(Request $request, $filename)
    {
        $request->validate([
            'printer_id' => 'required|exists:printers,id',
        ]);

        $filePath = 'gcodes/' . $filename;

        if (!Storage::disk('private')->exists($filePath)) {
            return back()->withErrors(['file' => 'File does not exist.']);
        }

        $printer = Printer::findOrFail($request->printer_id);

        // Placeholder: Here you'd send file to Moonraker API

        return back()->with('success', "File '{$filename}' sent to printer '{$printer->name}'.");
    }

}
