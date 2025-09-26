<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function importCsv(Request $request)
    {
        $data = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'member_id' => 'required|exists:members,id',
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('file')->store('imports/'.TenantContext::id());
        $full = Storage::path($path);

        $rows = array_map('str_getcsv', file($full));
        $header = array_map('trim', array_shift($rows));
        $count = 0;
        foreach ($rows as $r) {
            $row = array_combine($header, $r);
            if (!$row) continue;
            Transaction::create([
                'tenant_id' => TenantContext::id(),
                'account_id' => $data['account_id'],
                'member_id' => $data['member_id'],
                'type' => $row['type'] ?? 'EXPENSE',
                'category_id' => $row['category_id'] ?? null,
                'description' => $row['description'] ?? 'Imported',
                'amount' => (float)($row['amount'] ?? 0),
                'date' => $row['date'] ?? now()->toDateString(),
            ]);
            $count++;
        }

        return response()->json(['imported' => $count]);
    }
}
