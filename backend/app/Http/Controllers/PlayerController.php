<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlayerController extends Controller
{
    /**
     * Helper to compute dynamic squad statistics directly from the database.
     */
    private function getStatistics(): array
    {
        $players = Player::all();
        $total = $players->count();
        $paid = $players->where('payment_status', 'paid')->count();
        $halfPaid = $players->where('payment_status', 'half_paid')->count();
        $unpaid = $players->where('payment_status', 'unpaid')->count();
        $notConfirmed = $players->where('payment_status', 'not_confirmed')->count();

        $sizes = [];
        foreach (Player::SIZES as $size) {
            $sizes[$size] = $players->where('jersey_size', $size)->count();
        }

        return [
            'total' => $total,
            'paid' => $paid,
            'half_paid' => $halfPaid,
            'halfPaid' => $halfPaid,
            'unpaid' => $unpaid,
            'not_confirmed' => $notConfirmed,
            'notConfirmed' => $notConfirmed,
            'sizes' => $sizes,
        ];
    }

    /**
     * Format player record to match frontend expectations.
     */
    private function formatPlayer(Player $player): array
    {
        $statusLabels = [
            'paid' => 'Paid',
            'half_paid' => '50% Paid',
            'unpaid' => 'Unpaid',
            'not_confirmed' => 'Not Confirmed',
        ];

        return [
            'id' => $player->id,
            'no' => $player->id,
            'name' => $player->name,
            'number' => $player->shirt_number,
            'shirt_number' => $player->shirt_number,
            'size' => $player->jersey_size,
            'jersey_size' => $player->jersey_size,
            'status' => $statusLabels[$player->payment_status] ?? 'Unpaid',
            'payment_status' => $player->payment_status,
            'notes' => $player->notes ?: '',
            'created_at' => $player->created_at?->toISOString(),
            'updated_at' => $player->updated_at?->toISOString(),
        ];
    }

    /**
     * Display a listing of players and current statistics (Public Read-Only).
     */
    public function index(): JsonResponse
    {
        $players = Player::orderBy('id', 'asc')->get();

        return response()->json([
            'players' => $players->map(fn($p) => $this->formatPlayer($p)),
            'stats' => $this->getStatistics(),
        ]);
    }

    /**
     * Store a newly created player (Admin Protected).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'shirt_number' => ['required', 'integer', 'min:0', 'max:999'],
            'jersey_size' => ['required', Rule::in(Player::SIZES)],
            'payment_status' => ['required', Rule::in(['paid', 'half_paid', 'unpaid', 'not_confirmed', 'Paid', '50% Paid', '50%_paid', 'half_paid', 'Unpaid', 'Not Confirmed'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $player = Player::create($validated);

        return response()->json([
            'message' => 'Player added successfully.',
            'player' => $this->formatPlayer($player),
            'stats' => $this->getStatistics(),
        ], 201);
    }

    /**
     * Display the specified player.
     */
    public function show(int $id): JsonResponse
    {
        $player = Player::findOrFail($id);

        return response()->json([
            'player' => $this->formatPlayer($player),
        ]);
    }

    /**
     * Update the specified player (Admin Protected).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $player = Player::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'shirt_number' => ['required', 'integer', 'min:0', 'max:999'],
            'jersey_size' => ['required', Rule::in(Player::SIZES)],
            'payment_status' => ['required', Rule::in(['paid', 'half_paid', 'unpaid', 'not_confirmed', 'Paid', '50% Paid', '50%_paid', 'half_paid', 'Unpaid', 'Not Confirmed'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $player->update($validated);

        return response()->json([
            'message' => 'Player updated successfully.',
            'player' => $this->formatPlayer($player),
            'stats' => $this->getStatistics(),
        ]);
    }

    /**
     * Quick 1-tap update of player payment status (Admin Protected).
     */
    public function updatePayment(Request $request, int $id): JsonResponse
    {
        $player = Player::findOrFail($id);

        $validated = $request->validate([
            'payment_status' => ['required', Rule::in(['paid', 'half_paid', 'unpaid', 'not_confirmed', 'Paid', '50% Paid', '50%_paid', 'half_paid', 'Unpaid', 'Not Confirmed'])],
        ]);

        $player->update([
            'payment_status' => $validated['payment_status'],
        ]);

        return response()->json([
            'message' => "Payment status updated to {$player->payment_status}.",
            'player' => $this->formatPlayer($player),
            'stats' => $this->getStatistics(),
        ]);
    }

    /**
     * Remove the specified player (Admin Protected).
     */
    public function destroy(int $id): JsonResponse
    {
        $player = Player::findOrFail($id);
        $name = $player->name;
        $player->delete();

        return response()->json([
            'message' => "Player '{$name}' deleted successfully.",
            'stats' => $this->getStatistics(),
        ]);
    }
}
