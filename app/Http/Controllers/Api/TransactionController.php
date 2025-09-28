<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionSplit;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('splits', 'category')->orderByDesc('date');
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->integer('account_id'));
        }
        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->date('to'));
        }
        return response()->json($query->paginate(50));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'member_id' => 'required|exists:members,id',
            'type' => 'required|in:INCOME,EXPENSE,TRANSFER,CARD_PURCHASE,CARD_PAYMENT',
            'expense_kind' => 'nullable|in:FIXED,INSTALLMENT,ONE_TIME',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'required|string',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'tags' => 'array',
            'notes' => 'nullable|string',
            'splits' => 'array',
            'splits.*.category_id' => 'required_with:splits|exists:categories,id',
            'splits.*.amount' => 'required_with:splits|numeric',
        ]);

        if (!in_array($data['type'], ['EXPENSE', 'CARD_PURCHASE'], true)) {
            $data['expense_kind'] = null;
        }

        return DB::transaction(function () use ($data) {
            $payload = collect($data)->except(['splits'])->toArray();
            $payload['tenant_id'] = TenantContext::id();
            $tx = Transaction::create($payload);

            if (!empty($data['splits'])) {
                foreach ($data['splits'] as $s) {
                    TransactionSplit::create([
                        'transaction_id' => $tx->id,
                        'category_id' => $s['category_id'],
                        'amount' => $s['amount'],
                    ]);
                }
            }

            return response()->json($tx->load('splits'), 201);
        });
    }

    public function show($id)
    {
        return response()->json(Transaction::with('splits')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $tx = Transaction::findOrFail($id);
        $data = $request->validate([
            'description' => 'sometimes|string',
            'amount' => 'sometimes|numeric',
            'date' => 'sometimes|date',
            'tags' => 'sometimes|array',
            'notes' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'expense_kind' => 'nullable|in:FIXED,INSTALLMENT,ONE_TIME',
        ]);
        if ($tx->type !== 'EXPENSE' && $tx->type !== 'CARD_PURCHASE') {
            $data['expense_kind'] = null;
        }
        $tx->fill($data)->save();
        return response()->json($tx);
    }

    public function destroy($id)
    {
        $tx = Transaction::findOrFail($id);
        $tx->delete();
        return response()->json(['deleted' => true]);
    }
}
