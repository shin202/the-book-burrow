<?php

use App\Broadcasting\AdminNotificationChannel;
use App\Broadcasting\CurrentAdminNotificationChannel;
use App\Broadcasting\OrderNotificationChannel;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

Broadcast::channel('admin.notification', AdminNotificationChannel::class);
Broadcast::channel('admin.{id}.notification', CurrentAdminNotificationChannel::class);

Broadcast::channel('orders.{orderNumber}', OrderNotificationChannel::class);

Broadcast::channel('exporter.{id}.{userId}', function (User $user, $id, $userId) {
    return (int)$user->id === (int)$userId;
});
Broadcast::channel('importer.{id}.{userId}', function (User $user, $id, $userId) {
    return (int)$user->id === (int)$userId;
});
