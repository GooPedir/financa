<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Member;
use App\Services\InvoiceService;
use App\Support\TenantContext;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $service) {}

    public function byCard($id)
    {
        $invoices = Invoice::where('card_id', $id)->orderByDesc('reference_month')->get();
        return response()->json($invoices);
    }

    public function pay(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);
        $member = Member::where('user_id', $request->user()->id)
            ->where('tenant_id', TenantContext::id())
            ->firstOrFail();
        $this->service->pay($invoice, $member, (float)$data['amount'], $data['description'] ?? 'Card payment');
        return response()->json($invoice->fresh());
    }
}

