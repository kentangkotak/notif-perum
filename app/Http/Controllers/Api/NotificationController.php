<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationController extends Controller
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function send(Request $request): JsonResponse
    {
        $extraData = $request->input('data', []);
        $request->validate([
            'tokens' => 'required|array',
            'title' => 'required|string',
            'body'  => 'required|string',
            'data'  => 'nullable|array',
        ]);

        try {
                $report = $this->firebase->sendToTokens(
                $request->tokens ?? [],
                $request->title,
                $request->body,
                $extraData
            );

            return response()->json([
                'status'  => true,
                'message' => 'Notifications processed',
                'success_count' => $report->successes()->count(),
                'failure_count' => $report->failures()->count(),
                'errors' => collect($report->failures()->getItems())->map(function ($failure) {
        return [
            'token' => $failure->target()->value(),
            'error' => $failure->error()->getMessage(),
        ];
    }),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
