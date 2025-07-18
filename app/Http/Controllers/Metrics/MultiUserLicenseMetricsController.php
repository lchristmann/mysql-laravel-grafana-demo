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
            'count' => User::whereNotNull('parent_user_id')->count()
        ]);
    }

    // 2. Number of active sub-users in the given time period
    public function activeSubUsers(Request $request): JsonResponse
    {
        $from = $request->input('from');    // expected: ISO 8601
        $to = $request->input('to');        // expected: ISO 8601

        $count = User::whereNotNull('parent_user_id')
            ->whereBetween('last_login', [$from, $to])
            ->count();

        return response()->json(['count' => $count]);
    }

    // 3. Number of QES-signed protocols by a sub-users in the given time period
    public function protocolsSignedBySubUsers(Request $request): JsonResponse
    {
        $from = $request->input('from'); // expected: ISO 8601
        $to = $request->input('to');     // expected: ISO 8601

        $count = Protocol::whereNotNull('signed_with_qes_at')
            ->whereBetween('signed_with_qes_at', [$from, $to])
            ->whereHas('user', function ($query) {
                $query->whereNotNull('parent_user_id');
            })
            ->count();

        return response()->json(['count' => $count]);
    }
}
