<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notification\NotificationResource;
use App\Services\AuthUserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function __construct(protected AuthUserService $authUserService)
    {

    }

    public function index(Request $request)
    {
        $channel = ['admin.notification', "admin.{$request->user()->id}.notification"];
        $notifications = NotificationResource::collection($this->authUserService->notifications($channel));

        if ($request->wantsJson()) {
            return $notifications;
        }

        return Inertia::render('Dashboard/Notification/Index', [
            'notifications' => $notifications
        ]);
    }

    public function show(string $id)
    {
        $notification = $this->authUserService->notification($id);

        $notification->markAsRead();

        return Inertia::render('Dashboard/Notification/Show', [
            'notification' => NotificationResource::make($notification)
        ]);
    }

    public function markAllAsRead()
    {
        $this->authUserService->markAllNotificationsAsRead();
        return redirect()->back();
    }
}
