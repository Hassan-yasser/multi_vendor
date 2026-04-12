<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class DashboardController extends Controller
{
    // 
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user(); 
    

        return View::make('dashboard.index', [
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}
