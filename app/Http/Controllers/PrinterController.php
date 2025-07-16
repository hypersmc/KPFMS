<?php

namespace App\Http\Controllers;

use App\Models\Printer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class PrinterController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:printer-list|printer-create|printer-edit|printer-delete', ['only' => ['index','show']]);
        $this->middleware('permission:printer-create', ['only' => ['create','store']]);
        $this->middleware('permission:printer-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:printer-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $printers = Printer::latest()->paginate(5);
        return view('printers.index',compact('printers'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create(): View
    {
        return view('printers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'ip_address' => 'required|string',
            // other fields...
        ]);

        $ip = $request->input('ip_address');
        $endpoint = rtrim($ip, '/') . '/api/version';

        try {
            $response = Http::timeout(3)->get($endpoint);

            if ($response->successful()) {
                $versionInfo = $response->json();
                if (!isset($versionInfo['server'])) {
                    return back()->withErrors(['ip_address' => 'Provided address is not a valid printer API.'])->withInput();
                }
            } else {
                return back()->withErrors(['ip_address' => 'Unable to reach printer API (non-200 response).'])->withInput();
            }

        } catch (\Exception $e) {
            return back()->withErrors(['ip_address' => 'Connection failed: ' . $e->getMessage()])->withInput();
        }

        Printer::create($request->all());

        return redirect()->route('printers.index')->with('success', 'Printer created and validated.');
    }
    public function show(Printer $printer): View
    {
        return view('printers.show',compact('printer'));
    }
    public function edit(Printer $printer): View
    {
        return view('printers.edit',compact('printer'));
    }
    public function update(Request $request, Printer $printer): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string',
            'ip_address' => 'required|string',
            'location' => 'nullable|string',
            'type' => 'nullable|string',
            'notes' => 'nullable|string',
            'active' => 'sometimes|boolean',
        ]);

        $ip = $request->input('ip_address');
        $endpoint = rtrim($ip, '/') . '/api/version';

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(3)->get($endpoint);

            if ($response->successful()) {
                $versionInfo = $response->json();

                if (!isset($versionInfo['server']) && !isset($versionInfo['api'])) {
                    return back()->withErrors(['ip_address' => 'Provided address does not appear to be a valid Moonraker/OctoPrint API.'])->withInput();
                }
            } else {
                return back()->withErrors(['ip_address' => 'Printer API unreachable (non-200 response).'])->withInput();
            }

        } catch (\Exception $e) {
            return back()->withErrors(['ip_address' => 'Connection failed: ' . $e->getMessage()])->withInput();
        }

        $printer->update($request->all());

        return redirect()->route('printers.index')
            ->with('success', 'Printer updated and validated successfully.');
    }
    public function destroy(Printer $printer): RedirectResponse
    {
        $printer->delete();
        return redirect()->route('printers.index')
            ->with('success','Printer deleted successfully');
    }
}
