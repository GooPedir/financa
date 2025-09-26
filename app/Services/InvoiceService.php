<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Member;
use App\Models\Transaction;
use App\Support\TenantContext;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function pay(Invoice $invoice, Member $member, float $amount, string $description = 'Card payment'): Invoice
    {
        return DB::transaction(function () use ($invoice, $member, $amount, $description) {
            Transaction::create([
                'tenant_id' => TenantContext::id(),
                'account_id' => $invoice->card->account_id,
                'member_id' => $member->id,
                'type' => 'CARD_PAYMENT',
                'description' => $description,
                'amount' => $amount,
                'date' => now()->toDateString(),
            ]);

            $invoice->paid_amount = ($invoice->paid_amount ?? 0) + $amount;
            if ($invoice->paid_amount >= $invoice->total_amount) {
                $invoice->status = 'PAID';
            }
            $invoice->save();

            return $invoice;
        });
    }
}

