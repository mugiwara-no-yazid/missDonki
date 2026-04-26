<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Vote;
use App\Models\VotePack;
use App\Services\FeeXPayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VotingController extends Controller
{
    public function __construct(private FeeXPayService $feexpay) {}

    // ── Pages publiques ──────────────────────────────────────────────────────

    public function showCandidates()
    {
        return view('publics.candidates', [
            'candidates'  => Candidate::where('is_active', true)->orderBy('number')->get(),
            'packs'       => VotePack::active()->get(),
            'votingOpen'  => Setting::isVotingOpen(),
            'showVotes'   => Setting::isResultsVisible(),
        ]);
    }

    // ── Traitement du vote (AJAX JSON) ───────────────────────────────────────

    public function process(Request $request): JsonResponse
    {
        if (!Setting::isVotingOpen()) {
            return response()->json(['success' => false, 'message' => 'Les votes sont actuellement fermés.'], 403);
        }

        $data = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'pack_id'      => 'required|exists:vote_packs,id',
            'phone_number' => ['required', 'string', 'regex:/^[0-10]{8,15}$/'],
            'operator'     => 'required|in:mtn,moov',
        ]);
        return response()->json([
                'success'        => true,
                'transaction_id' => $data,
                'message'        => 'Demande de paiement envoyée. Validez sur votre téléphone.',
            ]);
        $candidate = Candidate::findOrFail($data['candidate_id']);
        $pack      = VotePack::findOrFail($data['pack_id']);

        if (!$candidate->is_active || !$pack->is_active) {
            return response()->json(['success' => false, 'message' => 'Candidate ou pack indisponible.'], 422);
        }

        // Créer le paiement en attente
        $payment = Payment::create([
            'candidate_id' => $candidate->id,
            'pack_id'      => $pack->id,
            'phone_number' => $data['phone_number'],
            'operator'     => $data['operator'],
            'amount'       => $pack->price_fcfa,
            'votes_count'  => $pack->votes_count,
            'status'       => 'pending',
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
        ]);

        // Appel FeeXPay
        $result = $this->feexpay->requestToPay($payment);

        if ($result['success']) {
            $payment->update(['transaction_ref' => $result['transaction_id']]);

            return response()->json([
                'success'        => true,
                'transaction_id' => $result['transaction_id'],
                'message'        => 'Demande de paiement envoyée. Validez sur votre téléphone.',
            ]);
        }

        // Échec immédiat
        $payment->update(['status' => 'failed', 'failure_reason' => $result['message']]);

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], 422);
    }

    // ── Webhook FeeXPay ──────────────────────────────────────────────────────

    public function webhook(Request $request): JsonResponse
    {
        Log::info('FeeXPay webhook', $request->all());

        // Vérifier la signature FeeXPay
        if (!$this->feexpay->verifyWebhook($request)) {
            Log::warning('FeeXPay webhook signature invalide', $request->all());
            return response()->json(['error' => 'Signature invalide'], 401);
        }

        $transactionId = $request->input('id')
                      ?? $request->input('transaction_id')
                      ?? $request->input('reference');

        $status = $request->input('status');

        if (!$transactionId) {
            return response()->json(['error' => 'Reference manquante'], 400);
        }

        $payment = Payment::where('transaction_ref', $transactionId)
                          ->where('status', 'pending')
                          ->first();

        if (!$payment) {
            // Déjà traité ou inconnu
            return response()->json(['ok' => true]);
        }

        DB::transaction(function () use ($payment, $status) {
            $isSuccess = $this->feexpay->isSuccessStatus($status);

            if ($isSuccess) {
                $payment->update([
                    'status'  => 'success',
                    'paid_at' => now(),
                ]);

                // Créer le vote
                Vote::create([
                    'candidate_id' => $payment->candidate_id,
                    'payment_id'   => $payment->id,
                    'votes_count'  => $payment->votes_count,
                ]);

                // Incrémenter compteur dénormalisé
                $payment->candidate->incrementVotes($payment->votes_count);

            } else {
                $payment->update([
                    'status'         => 'failed',
                    'failure_reason' => "FeeXPay status: {$status}",
                ]);
            }
        });

        return response()->json(['ok' => true]);
    }

    // ── Vérification du statut d'un paiement (polling optionnel) ─────────────

    public function checkStatus(string $transactionId): JsonResponse
    {
        $payment = Payment::where('transaction_ref', $transactionId)
                          ->with('candidate')
                          ->firstOrFail();

        return response()->json([
            'status'       => $payment->status,
            'votes_count'  => $payment->votes_count,
            'candidate'    => $payment->candidate->name,
            'paid_at'      => $payment->paid_at,
        ]);
    }
}
