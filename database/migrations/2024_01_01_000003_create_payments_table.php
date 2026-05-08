<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('candidate_id')
                  ->constrained()
                  ->restrictOnDelete();

            $table->foreignId('pack_id')
                  ->constrained('vote_packs')
                  ->restrictOnDelete();

            // Informations du votant
            $table->string('phone_number', 20)->nullable();  // numéro Mobile Money (optionnel avec FedaPay)
            $table->string('operator', 20)->nullable();       // opérateur (rempli si connu)

            // Données de la transaction
            $table->string('transaction_ref')->nullable()->unique(); // référence retournée par la passerelle
            $table->unsignedInteger('amount');              // montant payé en FCFA
            $table->unsignedSmallInteger('votes_count');    // nombre de votes achetés

            // Statut du paiement
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->text('failure_reason')->nullable();     // message d'erreur si échec
            $table->timestamp('paid_at')->nullable();       // horodatage de la confirmation

            // Traçabilité (IP pour anti-fraude)
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            // Index pour les requêtes fréquentes
            $table->index('status');
            $table->index('phone_number');
            $table->index(['candidate_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
