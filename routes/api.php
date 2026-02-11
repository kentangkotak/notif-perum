<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotificationController;

Route::middleware('notif.key')->group(function () {
    Route::post('/send-notification', [NotificationController::class, 'send']);
});

