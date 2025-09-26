<?php

namespace App\Jobs;

use App\Models\Card;
use App\Services\CardService;
use App\Support\TenantContext;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class CloseInvoices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private CardService $service) {}

    public function handle(): void
    {
        $today = Carbon::now();
        Card::all()->each(function (Card $card) use ($today) {
            if ((int)$card->closing_day === (int)$today->day) {
                TenantContext::set($card->account->tenant ?? null);
                $this->service->closeInvoice($card, $today->copy()->startOfMonth()->day($card->closing_day));
            }
        });
    }
}

