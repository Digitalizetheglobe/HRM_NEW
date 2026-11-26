<?php

namespace App\Services;

use App\Models\FcmToken;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FcmPushService
{
    /**
     * Send push notification to a list of user IDs.
     */
    public function sendToUsers(array $userIds, string $title, string $body, array $data = []): void
    {
        if (empty($userIds)) {
            return;
        }

        $tokens = FcmToken::whereIn('user_id', $userIds)
            ->pluck('token')
            ->unique()
            ->values()
            ->toArray();

        if (empty($tokens)) {
            return;
        }

        $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Send push notification to specific FCM device tokens using FCM HTTP v1 API.
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): void
    {
        try {
            $credentials = $this->loadServiceAccount();
            if (!$credentials) {
                Log::warning('FCM: Firebase service account JSON not found.');
                return;
            }

            $accessToken = $this->getAccessToken($credentials);
            if (!$accessToken) {
                Log::warning('FCM: Could not obtain OAuth2 access token.');
                return;
            }

            $projectId = $credentials['project_id'];
            $endpoint = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
            $client = new Client(['timeout' => 10]);

            // FCM data values must all be strings
            $stringData = [
                'title' => $title,
                'body'  => $body,
            ];
            foreach ($data as $k => $v) {
                $stringData[(string) $k] = (string) $v;
            }

            foreach ($tokens as $token) {
                try {
                    // Include both 'notification' (for reliable background/killed delivery across all OEMs)
                    // and 'data' (for foreground handling + click deep-link URL).
                    $payload = [
                        'message' => [
                            'token' => $token,
                            'notification' => [
                                'title' => $title,
                                'body'  => $body,
                            ],
                            'data'  => $stringData,
                            'android' => [
                                'priority' => 'high',
                                'notification' => [
                                    'channel_id' => 'hrm_channel_alerts',
                                    'sound'      => 'default',
                                ],
                            ],
                        ],
                    ];

                    $client->post($endpoint, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $accessToken,
                            'Content-Type'  => 'application/json',
                        ],
                        'json' => $payload,
                    ]);
                } catch (\GuzzleHttp\Exception\ClientException $e) {
                    $respBody = $e->getResponse() ? (string) $e->getResponse()->getBody() : '';
                    if (str_contains($respBody, 'UNREGISTERED') || str_contains($respBody, 'NOT_FOUND')) {
                        FcmToken::where('token', $token)->delete();
                    } else {
                        Log::error('FCM send error: ' . $respBody);
                    }
                } catch (\Throwable $e) {
                    Log::error('FCM send exception: ' . $e->getMessage());
                }
            }
        } catch (\Throwable $e) {
            Log::error('FCM service failure: ' . $e->getMessage());
        }
    }

    private function loadServiceAccount(): ?array
    {
        $files = glob(storage_path('app/*firebase-adminsdk*.json'));
        if (empty($files)) {
            return null;
        }

        $json = @file_get_contents($files[0]);
        if (!$json) {
            return null;
        }

        $data = json_decode($json, true);
        if (!is_array($data) || empty($data['project_id']) || empty($data['private_key']) || empty($data['client_email'])) {
            return null;
        }

        return $data;
    }

    private function getAccessToken(array $credentials): ?string
    {
        return Cache::remember('fcm_oauth2_access_token_' . $credentials['project_id'], 3000, function () use ($credentials) {
            $now = time();
            $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $claim = $this->base64UrlEncode(json_encode([
                'iss'   => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud'   => $credentials['token_uri'] ?? 'https://oauth2.googleapis.com/token',
                'iat'   => $now,
                'exp'   => $now + 3600,
            ]));

            $signingInput = $header . '.' . $claim;
            $signature = '';
            if (!openssl_sign($signingInput, $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256)) {
                return null;
            }

            $jwt = $signingInput . '.' . $this->base64UrlEncode($signature);

            $client = new Client(['timeout' => 10]);
            $response = $client->post($credentials['token_uri'] ?? 'https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion'  => $jwt,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            return $body['access_token'] ?? null;
        });
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

