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
use FedaPay\FedaPay;
use FedaPay\Transaction;

class VotingController extends Controller
{
    public function __construct() 
    {
        // Initialisation globale avec les variables config/services.php
        FedaPay::setApiKey(config('services.fedapay.secret_key'));
        FedaPay::setEnvironment(config('services.fedapay.environment'));
    }

    /**
     * Confirmation après paiement (souvent appelé par le JS en front)
     */
    public function confirmVote(Request $request)
    {
        $request->validate([
            'id' => 'required', 
            'local_id' => 'required|exists:payments,transaction_ref',
        ]);
     
        return DB::transaction(function () use ($request) {
            $payment = Payment::lockForUpdate()->where('transaction_ref', $request->local_id)->firstOrFail();

            if ($payment->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'Paiement déjà traité']);
            }

            try {
                // Vérification du statut auprès de FedaPay
                $fedapayTransaction = Transaction::retrieve($request->id);

                if ($fedapayTransaction->status === 'approved') {
                    $payment->update([
                        'status' => 'success',
                        'paid_at' => now(),
                    ]);

                    Vote::create([
                        'candidate_id' => $payment->candidate_id,
                        'payment_id'   => $payment->id,
                        'votes_count'  => $payment->votes_count,
                    ]);

                    $candidate = Candidate::find($payment->candidate_id);
                    $candidate->increment('total_votes', $payment->votes_count);

                    return response()->json([
                        'success' => true,
                        'candidate_name' => $candidate->name,
                        'votes_added' => $payment->votes_count
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('FedaPay Confirm Error: ' . $e->getMessage());
            }

            return response()->json(['success' => false, 'message' => 'Paiement non validé']);
        });
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
            'phone_number' => "0153258179", // À dynamiser si besoin
            'amount'       => $pack->price_fcfa,
            'votes_count'  => $pack->votes_count,
            'status'       => 'pending',
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
        ]);

        try {
            $transaction_ref = 'VOTE_' . $payment->id . '_' . time();
            $payment->update(['transaction_ref' => $transaction_ref]);
            
            // 2. Création de la transaction FedaPay
$transaction = Transaction::create([
    "description" => "Vote pour " . $candidate->name,
    "amount" => $payment->amount,
    "currency" => ["iso" => "XOF"],
    // Utilise soit l'un soit l'autre, mais proprement concaténé :
   "callback_url" => route('voting.callback'), 
    "customer" => [
        "firstname" => "Electeur",
        "lastname" => "Anonyme",
        "email" => "voter_" . $payment->id . "@example.com",
    ]
]);
            $token = $transaction->generateToken();

            return response()->json([
                'success'    => true,
                'token_url'  => $token->url, // URL vers laquelle rediriger ou charger dans un iframe
                'local_ref'  => $transaction_ref,
                'amount'     => $payment->amount,
            ]);

        } catch (\Exception $e) {
            Log::error('FedaPay Init Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur technique FedaPay.'], 500);
        }
    }

    /**
     * Webhook FedaPay
     */
    public function callback(Request $request)
    {
        // FedaPay envoie l'ID de la transaction dans le payload
        $id = $request->input('id');

        try {
            $fedapayTransaction = Transaction::retrieve($id);

            if ($fedapayTransaction->status === 'approved') {
                // On utilise ici une logique métier pour retrouver le paiement 
                // souvent via un champ 'custom_metadata' ou en parsant la description
                // Pour faire simple, on cherche le paiement en attente correspondant au montant/date
                $payment = Payment::where('status', 'pending')
                                  ->where('amount', $fedapayTransaction->amount)
                                  ->latest()
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

                        $payment->candidate->increment('total_votes', $payment->votes_count);
                    });
                }
                return redirect()->route('candidates')->with('success', 'Félicitations ! Votre vote a été enregistré avec succès.');
                
            }
        } catch (\Exception $e) {
            Log::error('FedaPay Webhook Error: ' . $e->getMessage());
        }

        
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
}