<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service FeeXPay — Passerelle de paiement Mobile Money (Bénin)
 * Documentation : https://docs.feexpay.me
 */
class FeeXPayService
{
    private string $baseUrl;
    private string $token;
    private string $shopId;
    private string $callbackUrl;

    public function __construct()
    {
        $this->baseUrl     = config('services.feexpay.base_url', 'https://api.feexpay.me');
        $this->token       = config('services.feexpay.token', '');
        $this->shopId      = config('services.feexpay.shop_id', '');
        $this->callbackUrl = route('webhook.feexpay');
    }

    /**
     * Initier un paiement Mobile Money via FeeXPay.
     *
     * @return array{success: bool, transaction_id: string|null, message: string}
     */
    public function requestToPay(Payment $payment): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->token}",
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->post("{$this->baseUrl}/api/transactions/public/requesttopay", [
                'shop_id'      => $this->shopId,
                'amount'       => $payment->amount,
                'phone_number' => $payment->phone_number,
                'network'      => $this->mapOperator($payment->operator),
                'callback_url' => $this->callbackUrl,
                'reference'    => 'GT3-' . $payment->id . '-' . time(),
                'description'  => "Vote Gala Tabaski Act 3 — {$payment->candidate->name} — {$payment->pack->name}",
                'first_name'   => 'Votant',
                'last_name'    => 'Gala',
                'email'        => 'vote@galatabaski.bj',
            ]);

            Log::info('FeeXPay requestToPay', [
                'payment_id' => $payment->id,
                'status'     => $response->status(),
                'body'       => $response->json(),
            ]);

            if ($response->successful()) {
                $body = $response->json();

                // FeeXPay retourne l'ID de transaction dans différents champs selon la version
                $transactionId = $body['id']
                              ?? $body['transaction_id']
                              ?? $body['reference']
                              ?? null;

                if ($transactionId) {
                    return [
                        'success'        => true,
                        'transaction_id' => (string) $transactionId,
                        'message'        => 'Demande de paiement initiée.',
                    ];
                }
            }

            $error = $response->json('message')
                  ?? $response->json('error')
                  ?? 'Erreur inconnue FeeXPay';

            Log::error('FeeXPay requestToPay failed', [
                'payment_id' => $payment->id,
                'status'     => $response->status(),
                'body'       => $response->json(),
            ]);

            return [
                'success'        => false,
                'transaction_id' => null,
                'message'        => $this->friendlyError($error, $response->status()),
            ];

        } catch (\Exception $e) {
            Log::error('FeeXPay exception', [
                'payment_id' => $payment->id,
                'error'      => $e->getMessage(),
            ]);

            return [
                'success'        => false,
                'transaction_id' => null,
                'message'        => 'Erreur de connexion. Veuillez réessayer.',
            ];
        }
    }

    /**
     * Vérifier la signature du webhook FeeXPay.
     * FeeXPay envoie un header X-FEEXPAY-SIGNATURE (HMAC-SHA256).
     */
    public function verifyWebhook(Request $request): bool
    {
        $secret    = config('services.feexpay.webhook_secret', '');
        $signature = $request->header('X-FEEXPAY-SIGNATURE')
                  ?? $request->header('X-Feexpay-Signature')
                  ?? '';

        if (empty($secret) || empty($signature)) {
            // En développement, accepter sans signature
            return app()->environment('local', 'testing') || empty($secret);
        }

        $payload  = $request->getContent();
        $expected = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expected, $signature);
    }

    /**
     * Normaliser le statut retourné par FeeXPay.
     * Statuts possibles : SUCCESSFUL, SUCCESS, FAILED, PENDING, CANCELLED, EXPIRED
     */
    public function isSuccessStatus(?string $status): bool
    {
        if (!$status) return false;
        return in_array(strtoupper($status), ['SUCCESSFUL', 'SUCCESS', 'COMPLETED']);
    }

    /**
     * Consulter le statut d'une transaction FeeXPay.
     */
    public function getStatus(string $transactionId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->token}",
                'Accept'        => 'application/json',
            ])->get("{$this->baseUrl}/api/transactions/{$transactionId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status'  => $response->json('status'),
                    'data'    => $response->json(),
                ];
            }

            return ['success' => false, 'status' => null, 'data' => []];

        } catch (\Exception $e) {
            return ['success' => false, 'status' => null, 'data' => []];
        }
    }

    // ── Helpers privés ───────────────────────────────────────────────────────

    /**
     * Mapper l'opérateur Laravel vers le code réseau FeeXPay.
     * MTN Bénin = 'MTN', Moov Bénin = 'MOOV'
     */
    private function mapOperator(string $operator): string
    {
        return match(strtolower($operator)) {
            'mtn'  => 'MTN',
            'moov' => 'MOOV',
            default => strtoupper($operator),
        };
    }

    /**
     * Transformer un message d'erreur technique en message lisible.
     */
    private function friendlyError(string $error, int $httpStatus): string
    {
        if ($httpStatus === 422) {
            return 'Numéro de téléphone invalide ou ne correspond pas à l\'opérateur sélectionné.';
        }
        if ($httpStatus === 401 || $httpStatus === 403) {
            return 'Erreur de configuration du paiement. Contactez l\'administrateur.';
        }
        if (str_contains(strtolower($error), 'phone')) {
            return 'Numéro de téléphone invalide.';
        }
        if (str_contains(strtolower($error), 'amount')) {
            return 'Montant invalide.';
        }
        return 'Paiement impossible pour le moment. Réessayez dans quelques instants.';
    }
}
