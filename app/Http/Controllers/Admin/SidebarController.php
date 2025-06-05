<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TokoInfo;
use Illuminate\Http\Request;

class SidebarController extends Controller
{
    public function index()
    {
        // Retrieve the toko info data
        $tokoInfo = TokoInfo::first(); // Assuming you have a TokoInfo model
        if (!$tokoInfo) {
            // Handle the case where no TokoInfo is found
            return redirect()->route('admin.dashboard')->with('error', 'Toko Info not found');
        }

        

        return view('admin.components.index', compact('tokoInfo'));
    }
}
