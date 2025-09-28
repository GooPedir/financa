<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CardPurchase;
use App\Models\Goal;
use App\Models\Recurrence;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        $fromInput = $request->input('from');
        $toInput = $request->input('to');

        $from = $fromInput ? Carbon::parse($fromInput)->startOfDay() : Carbon::now()->startOfMonth();
        $to = $toInput ? Carbon::parse($toInput)->endOfDay() : Carbon::now()->endOfMonth();

        $base = Transaction::whereBetween('date', [$from->toDateString(), $to->toDateString()]);

        $income = (clone $base)->whereIn('type', ['INCOME'])->sum('amount');
        $expenses = (clone $base)->whereIn('type', ['EXPENSE', 'CARD_PURCHASE', 'CARD_PAYMENT'])->sum('amount');

        $rawBreakdown = (clone $base)
            ->whereIn('type', ['EXPENSE', 'CARD_PURCHASE'])
            ->whereNotNull('expense_kind')
            ->select('expense_kind', DB::raw('sum(amount) as total'))
            ->groupBy('expense_kind')
            ->pluck('total', 'expense_kind')
            ->toArray();

        $breakdown = [
            'FIXED' => (float)($rawBreakdown['FIXED'] ?? 0),
            'INSTALLMENT' => (float)($rawBreakdown['INSTALLMENT'] ?? 0),
            'ONE_TIME' => (float)($rawBreakdown['ONE_TIME'] ?? 0),
        ];

        $latest = (clone $base)
            ->with('category', 'account')
            ->orderByDesc('date')
            ->limit(5)
            ->get();

        $recurrences = Recurrence::with(['baseTransaction.category'])
            ->orderBy('next_run_at')
            ->limit(6)
            ->get();

        $goals = Goal::with('category')
            ->get()
            ->map(function (Goal $goal) {
                $target = (float)$goal->target_amount;
                $current = (float)$goal->current_amount;
                return [
                    'id' => $goal->id,
                    'label' => $goal->category?->name ?? 'Meta',
                    'target' => $target,
                    'current' => $current,
                    'percent' => $target > 0 ? round(($current / $target) * 100, 2) : 0,
                ];
            })
            ->values();

        $upcomingInstallments = CardPurchase::with(['card'])
            ->whereNull('invoice_id')
            ->orderBy('installment_number')
            ->limit(6)
            ->get()
            ->map(function (CardPurchase $purchase) {
                return [
                    'card' => $purchase->card?->name,
                    'installment' => $purchase->installment_number,
                    'total' => $purchase->installments_total,
                    'amount' => (float)$purchase->per_installment_amount,
                ];
            })
            ->values();

        return response()->json([
            'period' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'income' => (float)$income,
            'expenses' => (float)$expenses,
            'net' => (float)($income - $expenses),
            'expense_breakdown' => $breakdown,
            'latest_transactions' => $latest,
            'recurrences' => $recurrences,
            'goals' => $goals,
            'installments' => $upcomingInstallments,
        ]);
    }
}
