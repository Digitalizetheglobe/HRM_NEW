<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string'
        ]);

        $user = Auth::user();
        if ($user) {
            $user->device_token = $request->device_token;
            $user->save();

            return response()->json(['success' => true, 'message' => 'Device token saved successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
    }
}
