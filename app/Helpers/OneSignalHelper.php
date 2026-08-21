<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OneSignalHelper
{
    public static function sendPushNotification($deviceTokens, $title, $message, $data = [])
    {
        if (empty($deviceTokens)) {
            return false;
        }

        if (!is_array($deviceTokens)) {
            $deviceTokens = [$deviceTokens];
        }

        // Filter out empty tokens
        $deviceTokens = array_filter($deviceTokens);
        
        if (empty($deviceTokens)) {
            return false;
        }

        $appId = env('ONESIGNAL_APP_ID', 'YOUR_ONESIGNAL_APP_ID');
        $restApiKey = env('ONESIGNAL_REST_API_KEY', 'YOUR_ONESIGNAL_REST_API_KEY');

        if ($appId == 'YOUR_ONESIGNAL_APP_ID' || empty($appId)) {
            Log::warning('OneSignal App ID not configured.');
            return false;
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json; charset=utf-8',
            'Authorization' => 'Basic ' . $restApiKey,
        ])->post('https://onesignal.com/api/v1/notifications', [
            'app_id' => $appId,
            'include_player_ids' => $deviceTokens,
            'headings' => ['en' => $title],
            'contents' => ['en' => $message],
            'data' => $data,
        ]);

        if ($response->successful()) {
            Log::info('OneSignal push notification sent.', ['response' => $response->json()]);
            return true;
        } else {
            Log::error('OneSignal push notification failed.', ['response' => $response->json()]);
            return false;
        }
    }
}
