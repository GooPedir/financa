<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function cashflow(Request $request)
    {
        $from = $request->date('from', now()->startOfMonth());
        $to = $request->date('to', now());
        $rows = Transaction::select('date', 'type', DB::raw('sum(amount) as total'))
            ->whereBetween('date', [$from, $to])
            ->groupBy('date','type')
            ->orderBy('date')
            ->get();
        return response()->json($rows);
    }

    public function byCategory(Request $request)
    {
        $period = $request->query('period', 'month');
        $from = $period === 'month' ? now()->startOfMonth() : now()->startOfYear();
        $rows = Transaction::select('category_id', DB::raw('sum(amount) as total'))
            ->whereBetween('date', [$from->toDateString(), now()->toDateString()])
            ->groupBy('category_id')
            ->with('category')
            ->get();
        return response()->json($rows);
    }

    public function balanceSummary()
    {
        $rows = Transaction::select('account_id', DB::raw('sum(case when type in ("INCOME","CARD_PAYMENT") then amount else -amount end) as balance'))
            ->groupBy('account_id')
            ->with('account')
            ->get();
        return response()->json($rows);
    }

    public function goals()
    {
        $now = Carbon::now();
        $goals = Goal::with('category','account')->get();
        return response()->json($goals->map(function ($g) use ($now) {
            return [
                'goal' => $g,
                'percent' => $g->target_amount > 0 ? round(((float)$g->current_amount / (float)$g->target_amount) * 100, 2) : 0,
            ];
        }));
    }
}

