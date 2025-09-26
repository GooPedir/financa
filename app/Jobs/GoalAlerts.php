<?php

namespace App\Jobs;

use App\Models\Goal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GoalAlerts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        Goal::where('status', 'ACTIVE')->each(function (Goal $g) {
            if ($g->current_amount >= $g->target_amount) {
                $g->status = 'ACHIEVED';
                $g->save();
            }
        });
    }
}

