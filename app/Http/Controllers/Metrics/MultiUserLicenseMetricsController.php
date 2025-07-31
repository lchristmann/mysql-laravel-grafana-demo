<?php

namespace App\Http\Controllers\Metrics;

use App\Http\Controllers\Controller;
use App\Models\Protocol;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MultiUserLicenseMetricsController extends Controller
{
    // 1. Total number of existing sub-users
    public function totalSubUsers(): JsonResponse
    {
        return response()->json([
            'count' => User::subuser()->count()
        ]);
    }

    // 2. Number of active sub-users in the given time period
    public function activeSubUsers(Request $request): JsonResponse
    {
        $from = $request->input('from');    // expected: ISO 8601
        $to = $request->input('to');        // expected: ISO 8601

        $count = User::subuser()->activeInTimeRange($from, $to)->count();

        return response()->json(['count' => $count]);
    }

    // 3. Number of QES-signed protocols by a sub-users in the given time period
    public function protocolsSignedBySubUsers(Request $request): JsonResponse
    {
        $from = $request->input('from'); // expected: ISO 8601
        $to = $request->input('to');     // expected: ISO 8601

        $count = Protocol::signedWithQESInTimeRange($from, $to)->subuser()->count();

        return response()->json(['count' => $count]);
    }
}
