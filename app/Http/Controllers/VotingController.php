<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Vote;
use App\Models\VotePack;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
// Import du SDK Kkiapay
use Kkiapay\Kkiapay;

class VotingController extends Controller
{
    private $kkiapay;

    public function __construct() 
    {
        // Initialisation avec tes identifiants (à mettre dans config/services.php)
        $this->kkiapay = new Kkiapay(
            config('services.kkiapay.public_key'),
            config('services.kkiapay.private_key'),
            config('services.kkiapay.secret'),
            ['sandbox' => config('services.kkiapay.sandbox', true)]
        );
    }

    public function confirmVote(Request $request)
{
    
    $request->validate([
        'kkiapay_id' => 'required', // ID de transaction Kkiapay
        'local_id'   => 'required|exists:payments,transaction_ref',
    ]);
 
    // On utilise une transaction DB pour être sûr que tout passe ou rien ne passe
    return DB::transaction(function () use ($request) {
        
        $payment = Payment::lockForUpdate()->where('transaction_ref',$request->local_id)->firstOrFail();

        // Sécurité : Si le paiement est déjà traité, on ne fait rien
        if ($payment->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Paiement déjà traité']);
        }

        // 1. Mise à jour du paiement
        $payment->update([
            'status' => 'success',
            'transaction_ref' => $request->kkiapay_id,
            'paid_at' => now(),
            // Optionnel: on pourrait récupérer le numéro du client via l'API Kkiapay ici
        ]);

        // 2. Création de l'enregistrement de vote
        Vote::create([
            'candidate_id' => $payment->candidate_id,
            'payment_id'   => $payment->id,
            'votes_count'  => $payment->votes_count,
        ]);

        // 3. Mise à jour du total dénormalisé sur le candidat
        $candidate = Candidate::find($payment->candidate_id);
        $candidate->increment('total_votes', $payment->votes_count);

        return response()->json([
            'success' => true,
            'candidate_name' => $candidate->name,
            'votes_added' => $payment->votes_count
        ]);
    });
}

    public function showCandidates()
    {
        return view('publics.candidates', [
            'candidates'  => Candidate::where('is_active', true)->orderBy('number')->get(),
            'packs'       => VotePack::active()->get(),
            'votingOpen'  => Setting::isVotingOpen(),
            'showVotes'   => Setting::isResultsVisible(),
        ]);
    }

    public function process(Request $request): JsonResponse
    {
        if (!Setting::isVotingOpen()) {
            return response()->json(['success' => false, 'message' => 'Les votes sont actuellement fermés.'], 403);
        }
        $data = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'pack_id'      => 'required|exists:vote_packs,id',
            
        ]);

        $candidate = Candidate::findOrFail($data['candidate_id']);
        $pack      = VotePack::findOrFail($data['pack_id']);

        if (!$candidate->is_active || !$pack->is_active) {
            return response()->json(['success' => false, 'message' => 'Candidat ou pack indisponible.'], 422);
        }

        // 1. Créer le paiement local en attente
        $payment = Payment::create([
            'candidate_id' => $candidate->id,
            'pack_id'      => $pack->id,
            'phone_number' => "0153258179",
            'amount'       => $pack->price_fcfa,
            'votes_count'  => $pack->votes_count,
            'status'       => 'pending',
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
        ]);

        // 2. Préparer la transaction Kkiapay
        try {
            // Note: On utilise l'ID de notre paiement comme référence Kkiapay
            $transaction_id = 'VOTE_' . $payment->id . '_' . time();
            $payment->update(['transaction_ref' => $transaction_id]);

            // Si tu utilises le Widget (Frontend), tu renvoies juste l'ID à ton JS.
            // Si tu veux faire une redirection serveur (API), utilise :
            /*
            $response = $this->kkiapay->setupCheckout([
                "amount" => $payment->amount,
                "transaction_id" => $transaction_id,
                "callback" => route('voting.webhook'),
                "phoneNumber" => $payment->phone_number
            ]);
            */

            return response()->json([
                'success'        => true,
                'transaction_id' => $transaction_id,
                'amount'         => $payment->amount,
                'message'        => 'Initialisation du paiement...',
            ]);

        } catch (\Exception $e) {
            Log::error('Kkiapay Init Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur technique Kkiapay.'], 500);
        }
    }

    // ── Webhook Kkiapay (Appelé par Kkiapay après le paiement) ────────────────

    public function webhook(Request $request): JsonResponse
    {
        // Kkiapay envoie les infos de transaction
        $transactionId = $request->input('transactionId');

        try {
            // VÉRIFICATION CRITIQUE : On demande à Kkiapay le vrai statut de cette transaction
            $kkiapayTransaction = $this->kkiapay->verifyTransaction($transactionId);

            if ($kkiapayTransaction->status === 'SUCCESS') {
                
                // On retrouve notre paiement par la référence
                $payment = Payment::where('transaction_ref', $kkiapayTransaction->transactionId)
                                  ->where('status', 'pending')
                                  ->first();

                if ($payment) {
                    DB::transaction(function () use ($payment) {
                        $payment->update([
                            'status'  => 'success',
                            'paid_at' => now(),
                        ]);

                        Vote::create([
                            'candidate_id' => $payment->candidate_id,
                            'payment_id'   => $payment->id,
                            'votes_count'  => $payment->votes_count,
                        ]);

                        $payment->candidate->incrementVotes($payment->votes_count);
                    });
                }
                return response()->json(['status' => 'success']);
            }
        } catch (\Exception $e) {
            Log::error('Webhook Error: ' . $e->getMessage());
        }

        return response()->json(['status' => 'failed'], 400);
    }

    public function checkStatus(string $transactionId): JsonResponse
    {
        $payment = Payment::where('transaction_ref', $transactionId)->firstOrFail();

        return response()->json([
            'status'      => $payment->status,
            'votes_count' => $payment->votes_count,
            'candidate'   => $payment->candidate->name,
        ]);
    }
}