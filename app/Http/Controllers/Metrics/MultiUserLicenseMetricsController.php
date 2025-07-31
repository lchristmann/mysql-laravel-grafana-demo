<?php

namespace App\Http\Controllers\Metrics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Metrics\TimePeriodBoundRequest;
use App\Models\Protocol;
use App\Models\User;
use Illuminate\Http\JsonResponse;

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
    public function activeSubUsers(TimePeriodBoundRequest $request): JsonResponse
    {
        $count = User::subuser()->activeInTimeRange($request->input('from'), $request->input('to'))->count();

        return response()->json(['count' => $count]);
    }

    // 3. Number of QES-signed protocols by a sub-users in the given time period
    public function protocolsSignedBySubUsers(TimePeriodBoundRequest $request): JsonResponse
    {
        $count = Protocol::signedWithQESInTimeRange($request->input('from'), $request->input('to'))->subuser()->count();

        return response()->json(['count' => $count]);
    }
}
