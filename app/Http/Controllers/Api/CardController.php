<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Member;
use App\Services\CardService;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CardController extends Controller
{
    public function __construct(private CardService $cardService) {}

    public function index()
    {
        return response()->json(Card::with('account')->get());
    }

    public function show($id)
    {
        return response()->json(Card::with('invoices')->findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'name' => 'required|string',
            'brand' => 'nullable|string',
            'limit_amount' => 'required|numeric',
            'closing_day' => 'required|integer|min:1|max:28',
            'due_day' => 'required|integer|min:1|max:28',
        ]);

        $card = Card::create(array_merge($data, [
            'tenant_id' => TenantContext::id(),
        ]));

        return response()->json($card, 201);
    }

    public function purchase(Request $request, $id)
    {
        $card = Card::findOrFail($id);
        $data = $request->validate([
            'description' => 'required|string',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'installments' => 'nullable|integer|min:1|max:60',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $installments = (int)($data['installments'] ?? 1);
        $kind = $installments > 1 ? 'INSTALLMENT' : 'ONE_TIME';

        $member = Member::where('tenant_id', TenantContext::id())
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        [$tx, $purchases] = $this->cardService->purchase(
            $card,
            $member,
            $data['description'],
            (float)$data['amount'],
            Carbon::parse($data['date']),
            $installments,
            $data['category_id'] ?? null,
            $kind
        );

        return response()->json(['transaction' => $tx, 'purchases' => $purchases], 201);
    }

    public function close(Request $request, $id)
    {
        $card = Card::findOrFail($id);
        $closingDate = Carbon::now()->startOfMonth()->day($card->closing_day);
        $invoice = $this->cardService->closeInvoice($card, $closingDate);
        return response()->json($invoice);
    }
}
