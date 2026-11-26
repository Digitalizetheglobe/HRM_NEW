<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FcmTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token'       => 'required|string|max:512',
            'device_type' => 'nullable|string|max:50',
        ]);

        FcmToken::updateOrCreate(
            ['token' => $request->token],
            [
                'user_id'     => Auth::id(),
                'device_type' => $request->input('device_type', 'android'),
            ]
        );

        return response()->json(['success' => true]);
    }

    public function destroy(Request $request)
    {
        if ($request->filled('token')) {
            FcmToken::where('token', $request->token)
                ->where('user_id', Auth::id())
                ->delete();
        }

        return response()->json(['success' => true]);
    }
}

