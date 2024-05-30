<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use App\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $configuration = Configuration::first();

        if ($configuration->ecommerce_connected === false) {
            return redirect('quick-connect');
        }

        /** @var User $user */
        $user = $request->user();

        if (empty($user->default_dashboard_uri)) {
            return view('dashboard');
        }

        return redirect($user->default_dashboard_uri);
    }
}
