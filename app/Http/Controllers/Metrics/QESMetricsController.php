<?php

namespace App\Http\Controllers\Metrics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Metrics\TimePeriodBoundRequest;
use App\Models\Protocol;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class QESMetricsController extends Controller
{
    // 1. Total number of users unlocked for QES
    public function totalUnlockedUsers(): JsonResponse
    {
        return response()->json([
            'count' => User::unlockedForQes()->count()
        ]);
    }

    // 2. Number of active users with at least one QES-signed protocol in the given time period
    public function activeUsers(TimePeriodBoundRequest $request): JsonResponse
    {
        $count = User::active()
            ->unlockedForQes()
            ->hasProtocolsInTimeRange($request->input('from'), $request->input('to'))
            ->count();

        return response()->json(['count' => $count]);
    }

    // 3. Total number of QES-signed protocols
    public function totalSignedProtocols(): JsonResponse
    {
        return response()->json([
            'count' => Protocol::signedWithQES()->count()
        ]);
    }

    // 4. Number of QES-signed protocols in the given time period grouped by day/week/month
    public function signedProtocolsOverTime(TimePeriodBoundRequest $request): JsonResponse
    {
        $query = Protocol::selectRaw("
                DATE_FORMAT(signed_with_qes_at, ?) as period,
                COUNT(*) as count
            ", [$request->dateFormatToGroupBy()])
            ->signedWithQESInTimeRange($request->input('from'), $request->input('to'))
            ->groupBy('period')
            ->orderBy('period');

        $data = $query->get();

        return response()->json($data);
    }
}
