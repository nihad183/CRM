<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(DashboardService $service)
    {
        return view('dashboard', $service->build(auth()->user()));
    }

    public function data(DashboardService $service)
    {
        return response()->json(
            $service->build(auth()->user())
        );
    }
}