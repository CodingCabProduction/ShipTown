<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\JsonResponse;

class ManifestController extends Controller
{
    public function index(): JsonResponse
    {
        /** @var Configuration $config */
        $config = Configuration::first();

        return response()->json([
            'name'       => 'ShipTown ' . $config->business_name,
            'short_name' => empty($config->business_name) ? 'ShipTown' : 'ST ' . $config->business_name,
            "description" => "Order and Inventory Management made simple",
            "categories" => ["ecommerce", "business", "productivity", "utilities"],
            'icons'      => [
                [
                    'src'   => '/img/icons/android-chrome-192x192.png',
                    'sizes' => '192x192',
                    'type'  => 'image/png',
                ],
                [
                    'src'   => '/img/icons/android-chrome-512x512.png',
                    'sizes' => '512x512',
                    'type'  => 'image/png',
                ],
            ],
            'start_url'        => '/index.php',
            'display'          => 'standalone',
            'background_color' => '#348fdc',
            'theme_color'      => '#348fdc',
        ]);
    }
}
