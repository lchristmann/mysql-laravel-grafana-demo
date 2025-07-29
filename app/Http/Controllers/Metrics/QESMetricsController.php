<?php

namespace App\Http\Controllers\Metrics;

use App\Http\Controllers\Controller;
use App\Models\Protocol;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class QESMetricsController extends Controller
{
    // 1. Total number of users unlocked for QES
    public function totalUnlockedUsers(): JsonResponse
    {
        return response()->json([
            'count' => User::where('unlocked_for_qes', true)->count()
        ]);
    }

    // 2. Number of active users with at least one QES-signed protocol in the given time period
    public function activeUsers(Request $request): JsonResponse
    {
        $from = $request->input('from');    // expected: ISO 8601
        $to = $request->input('to');        // expected: ISO 8601

        $count = User::where('unlocked_for_qes', true)
            ->where('last_login', '>=', Carbon::now()->subDays(30))
            ->whereHas('protocols', function ($query) use ($from, $to) {
                $query->whereBetween('signed_with_qes_at', [$from, $to]);
            })
            ->count();

        return response()->json(['count' => $count]);
    }

    // 3. Total number of QES-signed protocols
    public function totalSignedProtocols(): JsonResponse
    {
        return response()->json([
            'count' => Protocol::whereNotNull('signed_with_qes_at')->count()
        ]);
    }

    // 4. Number of QES-signed protocols in the given time period grouped by day/week/month
    public function signedProtocolsOverTime(Request $request): JsonResponse
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

        $query = Protocol::selectRaw("
                DATE_FORMAT(signed_with_qes_at, ?) as period,
                COUNT(*) as count
            ", [$format])
            ->whereBetween('signed_with_qes_at', [$from, $to])
            ->groupBy('period')
            ->orderBy('period');

        $data = $query->get();

        return response()->json($data);
    }
}
