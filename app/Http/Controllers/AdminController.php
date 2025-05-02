<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function dashboard(Request $request)
    {
        Log::info('Admin dashboard accessed', [
            'user' => session('user')
        ]);
        
        $user = session('user');
        
        // You can fetch additional data here if needed
        
        return view('admin.dashboard', compact('user'));
    }
    
    // Add other admin methods here
}