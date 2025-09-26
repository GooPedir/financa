<?php

namespace App\Jobs;

use App\Models\Recurrence;
use App\Models\Transaction;
use App\Support\TenantContext;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class ProcessRecurrences implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $now = Carbon::now();
        Recurrence::where('next_run_at', '<=', $now)->each(function (Recurrence $r) use ($now) {
            TenantContext::set($r->baseTransaction->account->tenant ?? null);
            $base = $r->baseTransaction;
            Transaction::create([
                'tenant_id' => $base->tenant_id,
                'account_id' => $base->account_id,
                'member_id' => $base->member_id,
                'type' => $base->type,
                'category_id' => $base->category_id,
                'description' => $base->description,
                'amount' => $base->amount,
                'date' => $now->toDateString(),
                'tags' => $base->tags,
                'notes' => $base->notes,
            ]);

            $r->next_run_at = match ($r->frequency) {
                'DAILY' => $now->copy()->addDay(),
                'WEEKLY' => $now->copy()->addWeek(),
                'MONTHLY' => $now->copy()->addMonth(),
                'YEARLY' => $now->copy()->addYear(),
                'CRON' => $now->copy()->addDay(),
            };
            if ($r->occurrences_left !== null) {
                $r->occurrences_left -= 1;
                if ($r->occurrences_left <= 0) {
                    $r->delete();
                    return;
                }
            }
            $r->save();
        });
    }
}
