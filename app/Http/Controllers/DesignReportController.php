<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Design;
use App\Models\DesignVersion;

class DesignReportController extends Controller
{
    public function index(Request $request)
    {
        $designs = Design::with(['project', 'latestVersion'])->get();
        // Fallback to designs.index if report view doesn't exist
        return view('designs.index', compact('designs'));
    }
}
