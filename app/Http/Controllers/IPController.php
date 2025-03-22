<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IPController extends Controller
{
    /**
     * Get the client's IP address.
     *
     * @param Request $request
     * @return string
     */
    public function getClientIP(Request $request)
    {
        // Retrieve the client's IP address
        $clientIP = $request->ip();

        // Return the IP address as a response
        return response()->json(['ip' => $clientIP]);
    }
}