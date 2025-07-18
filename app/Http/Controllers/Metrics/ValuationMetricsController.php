<?php

namespace App\Http\Controllers\Metrics;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Valuation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ValuationMetricsController extends Controller
{
    // 1. Total number of users unlocked for doing valuations
    public function totalUnlockedUsers(): JsonResponse
    {
        return response()->json([
            'count' => User::where('unlocked_for_valuation', true)->count()
        ]);
    }

    // 2. Number of active users with at least one valuation in the given time period
    public function activeUsers(Request $request): JsonResponse
    {
        $from = $request->input('from');    // expected: ISO 8601
        $to = $request->input('to');        // expected: ISO 8601

        $count = User::where('unlocked_for_valuation', true)
            ->where('last_login', '>=', Carbon::now()->subDays(30))
            ->whereHas('valuations', function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            })
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
            'month' => 'YYYY-MM',
            'week' => 'IYYY-"W"IW', // ISO week format IYYYIW with '-W' put into it, e.g. 2025-W24
            default => 'YYYY-MM-DD',
        };

        $query = Valuation::selectRaw("
                TO_CHAR(created_at, ?) as period,
                COUNT(*) as count
            ", [$format])
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('period')
            ->orderBy('period');

        $data = $query->get();

        return response()->json($data);
    }
}
