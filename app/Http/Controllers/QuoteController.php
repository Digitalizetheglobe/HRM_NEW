<?php

namespace App\Http\Controllers;

use App\Models\DailyQuote;
use Illuminate\Support\Facades\Cache;

class QuoteController extends Controller
{
    public function getDailyQuote()
    {
        // Cache the quote for a day to ensure it stays the same
        $quote = Cache::remember('daily_quote', now()->endOfDay(), function () {
            return DailyQuote::inRandomOrder()->first();
        });

        return response()->json($quote);
    }
}
