<?php

namespace App\Services;

use Illuminate\Routing\PendingResourceRegistration;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteService
{
    public static function apiResource(string $uri, string $controller = null, array $only = ['index', 'store', 'update', 'destroy']): PendingResourceRegistration
    {
        $endpoints = collect(explode('/', $uri));

        $proposedControllerName = $endpoints->map(function ($endpoint) {
            return Str::of($endpoint)
                ->camel()
                ->ucfirst()
                ->singular();
        })->implode('\\');

        $controller = $controller ?? 'App\\Http\\Controllers\\Api\\' . $proposedControllerName . 'Controller';

        $endpoints->pop();

        return Route::apiResource($uri, $controller, ['as' => $endpoints->implode('.')])->only($only);
    }
}
