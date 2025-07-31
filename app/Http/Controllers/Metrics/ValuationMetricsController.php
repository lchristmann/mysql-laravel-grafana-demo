<?php

namespace App\Http\Controllers\Metrics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Metrics\TimePeriodBoundRequest;
use App\Models\User;
use App\Models\Valuation;
use Illuminate\Http\JsonResponse;

class ValuationMetricsController extends Controller
{
    // 1. Total number of users unlocked for doing valuations
    public function totalUnlockedUsers(): JsonResponse
    {
        return response()->json([
            'count' => User::unlockedForValuation()->count()
        ]);
    }

    // 2. Number of active users with at least one valuation in the given time period
    public function activeUsers(TimePeriodBoundRequest $request): JsonResponse
    {
        $count = User::active()
            ->unlockedForValuation()
            ->hasValuationsInTimeRange($request->input('from'), $request->input('to'))
            ->count();

        return response()->json(['count' => $count]);
    }

    // 3. Total number of done valuations
    public function totalValuations(): JsonResponse
    {
        return response()->json([
            'count' => Valuation::count()
        ]);
    }

    // 4. Number of valuations in the given time period grouped by day/week/month
    public function valuationsOverTime(TimePeriodBoundRequest $request): JsonResponse
    {
        $query = Valuation::selectRaw("
                DATE_FORMAT(created_at, ?) as period,
                COUNT(*) as count
            ", [$request->dateFormatToGroupBy()])
            ->createdInTimeRange($request->input('from'), $request->input('to'))
            ->groupBy('period')
            ->orderBy('period');

        $data = $query->get();

        return response()->json($data);
    }
}
