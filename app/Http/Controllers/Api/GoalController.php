<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\Transaction;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GoalController extends Controller
{
    public function index()
    {
        return response()->json(Goal::with('category','account')->with('contributions')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'account_id' => 'nullable|exists:accounts,id',
            'period' => 'required|in:MONTHLY,YEARLY,CUSTOM',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'target_amount' => 'required|numeric|min:0',
        ]);
        $goal = Goal::create(array_merge($data, [
            'tenant_id' => TenantContext::id(),
            'current_amount' => 0,
            'status' => 'ACTIVE',
        ]));
        return response()->json($goal, 201);
    }

    public function show($id)
    {
        return response()->json(Goal::with('category','account', 'contributions')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $goal = Goal::findOrFail($id);
        $goal->fill($request->only(['target_amount','status','start_date','end_date']))->save();
        return response()->json($goal);
    }

    public function destroy($id)
    {
        Goal::findOrFail($id)->delete();
        return response()->json(['deleted' => true]);
    }

    public function progress($id)
    {
        $goal = Goal::findOrFail($id);
        $from = match ($goal->period) {
            'MONTHLY' => Carbon::now()->startOfMonth(),
            'YEARLY' => Carbon::now()->startOfYear(),
            default => $goal->start_date ? Carbon::parse($goal->start_date) : Carbon::minValue(),
        };
        $to = $goal->end_date ? Carbon::parse($goal->end_date) : Carbon::now();

        $q = Transaction::whereBetween('date', [$from->toDateString(), $to->toDateString()]);
        if ($goal->account_id) {
            $q->where('account_id', $goal->account_id);
        }
        $q->where('category_id', $goal->category_id);

        $sum = (float)$q->sum('amount');

        return response()->json([
            'goal' => $goal,
            'progress' => $sum,
            'target' => (float)$goal->target_amount,
            'percent' => $goal->target_amount > 0 ? round(($sum / (float)$goal->target_amount) * 100, 2) : 0,
        ]);
    }

    public function contribute(Request $request, Goal $goal)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'contributed_at' => 'nullable|date',
            'note' => 'nullable|string'
        ]);

        $contribution = $goal->contributions()->create([
            'amount' => $data['amount'],
            'contributed_at' => $data['contributed_at'] ?? Carbon::now()->toDateString(),
            'note' => $data['note'] ?? null,
        ]);

        $goal->increment('current_amount', $data['amount']);

        return response()->json([
            'goal' => $goal->refresh(),
            'contribution' => $contribution,
        ]);
    }
}
