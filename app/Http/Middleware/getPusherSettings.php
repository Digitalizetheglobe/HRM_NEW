<?php

namespace App\Http\Middleware;

use App\Models\Utility;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class getPusherSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (file_exists(storage_path() . "/installed") && Schema::hasTable('settings') === true) {
            $settings = Utility::getPusherDetails();
            if ($settings) {
                if (!empty($settings['pusher_app_key'])) {
                    config(['chatify.pusher.key' => $settings['pusher_app_key']]);
                }
                if (!empty($settings['pusher_app_secret'])) {
                    config(['chatify.pusher.secret' => $settings['pusher_app_secret']]);
                }
                if (!empty($settings['pusher_app_id'])) {
                    config(['chatify.pusher.app_id' => $settings['pusher_app_id']]);
                }
                if (!empty($settings['pusher_app_cluster'])) {
                    config(['chatify.pusher.options.cluster' => $settings['pusher_app_cluster']]);
                }
            }
        }
        return $next($request);
    }
}
