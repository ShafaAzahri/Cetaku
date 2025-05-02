<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function dashboard(Request $request)
    {
        $user = session('user');
        
        // You can fetch additional data here if needed
        
        return view('admin.dashboard', compact('user'));
    }

    /**
     * Display product manager page
     */
    public function productManager(Request $request)
    {
        $user = session('user');
        
        return view('admin.product-manager', compact('user'));
    }
    
    // Add other admin methods here
}