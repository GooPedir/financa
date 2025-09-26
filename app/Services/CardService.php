<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Card;
use App\Models\CardPurchase;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\Transaction;
use App\Support\TenantContext;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CardService
{
    public function purchase(Card $card, Member $member, string $description, float $amount, \DateTimeInterface $date, int $installments = 1): array
    {
        return DB::transaction(function () use ($card, $member, $description, $amount, $date, $installments) {
            $transaction = Transaction::create([
                'tenant_id' => TenantContext::id(),
                'account_id' => $card->account_id,
                'member_id' => $member->id,
                'type' => 'CARD_PURCHASE',
                'description' => $description,
                'amount' => $amount,
                'date' => Carbon::instance($date)->toDateString(),
            ]);

            $per = round($amount / max($installments, 1), 2);
            $purchases = [];
            for ($i = 1; $i <= $installments; $i++) {
                $purchases[] = CardPurchase::create([
                    'tenant_id' => TenantContext::id(),
                    'card_id' => $card->id,
                    'invoice_id' => null,
                    'original_transaction_id' => $transaction->id,
                    'installments_total' => $installments,
                    'installment_number' => $i,
                    'per_installment_amount' => $per,
                ]);
            }

            return [$transaction, $purchases];
        });
    }

    public function closeInvoice(Card $card, Carbon $closingDate): Invoice
    {
        $referenceMonth = $closingDate->copy()->startOfMonth();
        $dueDate = $closingDate->copy()->day($card->due_day)->startOfDay();

        $invoice = Invoice::firstOrCreate([
            'tenant_id' => TenantContext::id(),
            'card_id' => $card->id,
            'reference_month' => $referenceMonth->toDateString(),
        ], [
            'closing_date' => $closingDate->toDateString(),
            'due_date' => $dueDate->toDateString(),
            'status' => 'OPEN',
        ]);

        $total = 0.0;
        $purchases = CardPurchase::where('card_id', $card->id)
            ->whereNull('invoice_id')
            ->get();

        foreach ($purchases as $p) {
            $orig = $p->original_transaction_id ? Transaction::find($p->original_transaction_id) : null;
            $purchaseDate = $orig?->date ? Carbon::parse($orig->date) : $referenceMonth->copy();
            $installmentMonth = $purchaseDate->copy()->addMonths($p->installment_number - 1)->startOfMonth();
            if ($installmentMonth->equalTo($referenceMonth)) {
                $p->invoice_id = $invoice->id;
                $p->save();
                $total += (float)$p->per_installment_amount;
            }
        }

        $invoice->total_amount = $total;
        $invoice->status = 'OPEN';
        $invoice->save();

        return $invoice;
    }
}

