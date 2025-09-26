<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recurrence;
use App\Support\TenantContext;
use Illuminate\Http\Request;

class RecurrenceController extends Controller
{
    public function index()
    {
        return response()->json(Recurrence::with('baseTransaction')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'base_transaction_id' => 'required|exists:transactions,id',
            'frequency' => 'required|in:DAILY,WEEKLY,MONTHLY,YEARLY,CRON',
            'cron_expr' => 'nullable|string',
            'next_run_at' => 'required|date',
            'occurrences_left' => 'nullable|integer|min:1',
        ]);
        $rec = Recurrence::create(array_merge($data, ['tenant_id' => TenantContext::id()]));
        return response()->json($rec, 201);
    }

    public function show($id)
    {
        return response()->json(Recurrence::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $rec = Recurrence::findOrFail($id);
        $rec->fill($request->only(['frequency','cron_expr','next_run_at','occurrences_left']))->save();
        return response()->json($rec);
    }

    public function destroy($id)
    {
        Recurrence::findOrFail($id)->delete();
        return response()->json(['deleted' => true]);
    }
}

