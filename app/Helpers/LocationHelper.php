<?php

namespace App\Helpers;

class LocationHelper
{
    public static function extractMainLocation($fullAddress)
    {
        if (empty($fullAddress)) {
            return 'Location not available';
        }

        // Try to extract landmark (e.g., "next to vision mall")
        if (preg_match('/(?:next to|near|opposite|at) ([^,]+)/i', $fullAddress, $matches)) {
            return 'Near ' . $matches[1];
        }

        // Otherwise take first part + last part (street + city)
        $parts = explode(',', $fullAddress);
        $parts = array_map('trim', $parts);
        $parts = array_filter($parts);

        if (count($parts) >= 2) {
            return $parts[0] . ', ' . end($parts);
        }

        return $fullAddress;
    }
}