<?php

namespace App\Http\Controllers\Api\Settings\Module\Automation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Automations\StoreRequest;
use App\Http\Requests\Automations\UpdateRequest;
use App\Http\Resources\AutomationResource;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AutomationController extends Controller
{

    /**
     * Display a listing of avaliable config automation.
     *
     * @return \Illuminate\Http\Response
     */
    public function getConfig()
    {
        $configs = config('automations');

        return $configs;
    }

    /**
     * Display a listing of the resource.
     *
     * @return AutomationResource
     */
    public function index()
    {
        $automations = Automation::all();

        return AutomationResource::collection($automations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest  $request
     * @return AutomationResource
     */
    public function store(StoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $dataAutomation = $request->only(['name', 'event_class', 'enabled', 'priority']);
            $automation = Automation::create($dataAutomation);
            $automation->conditions()->createMany($request->conditions);
            $automation->executions()->createMany($request->executions);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            abort(500, "Something wrong");
        }

        return new AutomationResource($automation);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Modules\Automations\src\Models\Automation  $automation
     * @return AutomationResource
     */
    public function show(Automation $automation)
    {
        $automation->load('conditions', 'executions');

        return new AutomationResource($automation);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  \App\Modules\Automations\src\Models\Automation  $automation
     * @return AutomationResource
     */
    public function update(UpdateRequest $request, Automation $automation)
    {
        try {
            DB::beginTransaction();
            $dataAutomation = $request->only(['name', 'event_class', 'enabled', 'priority']);
            $automation->update($dataAutomation);

            // Delete current data
            $automation->conditions()->delete();
            $automation->executions()->delete();

            // Insert new
            $automation->conditions()->createMany($request->conditions);
            $automation->executions()->createMany($request->executions);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            abort(500, "Something wrong");
        }

        return new AutomationResource($automation);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Modules\Automations\src\Models\Automation  $automation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Automation $automation)
    {
        $automation->delete();

        return true;
    }
}