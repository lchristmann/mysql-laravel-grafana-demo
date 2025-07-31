<?php

namespace App\Http\Controllers\Metrics;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Valuation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
    public function activeUsers(Request $request): JsonResponse
    {
        $from = $request->input('from');    // expected: ISO 8601
        $to = $request->input('to');        // expected: ISO 8601

        $count = User::unlockedForValuation()->active()->hasValuationsInTimeRange($from, $to)->count();

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
    public function valuationsOverTime(Request $request): JsonResponse
    {
        $from = $request->input('from');    // expected: ISO 8601
        $to = $request->input('to');        // expected: ISO 8601

        $grouping = match ($request->input('group_by', 'day')) {
            'month' => 'month',
            'week' => 'week',
            default => 'day',
        };

        $format = match ($grouping) {
            'month' => '%Y-%m',     // e.g. 2025-07
            'week' => '%x-W%v',     // e.g. 2025-W30 (ISO week)
            default => '%Y-%m-%d',  // e.g. 2025-07-29
        };

        $query = Valuation::selectRaw("
                DATE_FORMAT(created_at, ?) as period,
                COUNT(*) as count
            ", [$format])
            ->createdInTimeRange($from, $to)
            ->groupBy('period')
            ->orderBy('period');

        $data = $query->get();

        return response()->json($data);
    }
}
