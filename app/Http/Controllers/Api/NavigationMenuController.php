<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NavigationMenu\StoreRequest;
use App\Http\Requests\NavigationMenu\UpdateRequest;
use App\Http\Requests\NavigationMenuDestroyRequest;
use App\Http\Requests\NavigationMenuIndexRequest;
use App\Http\Resources\NavigationMenuResource;
use App\Models\NavigationMenu;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NavigationMenuController extends Controller
{
    public function index(NavigationMenuIndexRequest $request): AnonymousResourceCollection
    {
        $navigationMenu = NavigationMenu::getSpatieQueryBuilder();

        return NavigationMenuResource::collection($this->getPaginatedResult($navigationMenu));
    }

    public function store(StoreRequest $request): NavigationMenuResource
    {
        $navigationMenu = NavigationMenu::query()->create($request->validated());

        return new NavigationMenuResource($navigationMenu);
    }

    public function update(UpdateRequest $request, NavigationMenu $navigationMenu): NavigationMenuResource
    {
        $navigationMenu->update($request->validated());

        return new NavigationMenuResource($navigationMenu);
    }

    public function destroy(NavigationMenuDestroyRequest $request, NavigationMenu $navigationMenu): NavigationMenuResource
    {
        $navigationMenu->delete();

        return NavigationMenuResource::make($navigationMenu);
    }
}
