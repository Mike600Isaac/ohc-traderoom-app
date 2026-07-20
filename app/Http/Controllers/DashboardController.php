<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //

    // public function index()
    // {
    //     $user = auth()->user();

    //     // If not logged in, we can still show a 'Guest' version of the dashboard
    //     // or redirect to login. Based on your spec, let's assume they must 
    //     // login to see the custom dashboard.
    //     if (!$user) {
    //         return view('auth.login'); // Or a public landing page
    //     }

    //     return view('dashboard', compact('user'));
    // }

    public function index()
    {
        // Mock user for testing the UI
        $user = auth()->user();
        
        // If you want to test the standalone plan view, 
        // you can manually set the path here temporarily:
        // $user->current_path = 'Fixed Income'; 
    
        return view('dashboard', compact('user'));
    }
}
