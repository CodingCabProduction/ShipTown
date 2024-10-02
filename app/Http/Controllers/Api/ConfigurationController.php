<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Configuration\StoreRequest;
use App\Http\Requests\ConfigurationIndexRequest;
use App\Http\Resources\ConfigurationResource;
use App\Models\Configuration;

class ConfigurationController extends Controller
{
    public function index(ConfigurationIndexRequest $request): ConfigurationResource
    {
        $configuration = Configuration::first();

        return new ConfigurationResource($configuration);
    }

    public function store(StoreRequest $request)
    {
        $configuration = Configuration::first();
        $configuration->update($request->validated());

        return new ConfigurationResource($configuration);
    }
}
