<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRoleIndexRequest;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    public function index(UserRoleIndexRequest $request): ResourceCollection
    {
        return new ResourceCollection(Role::all());
    }
}
