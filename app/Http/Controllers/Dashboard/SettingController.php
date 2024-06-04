<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateGeneralSettingRequest;
use App\Services\SettingService;
use Inertia\Inertia;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Dashboard/Settings/Index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateGeneral(UpdateGeneralSettingRequest $request, string $key, SettingService $settingService)
    {
        $settingService->updateGeneralSetting($key, $request->validated('value'));
    }
}
