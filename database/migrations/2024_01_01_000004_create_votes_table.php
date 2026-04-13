<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('candidate_id')
                  ->constrained()
                  ->restrictOnDelete();

            // Relation 1-to-1 avec un paiement confirmé
            $table->foreignId('payment_id')
                  ->unique()                   // un paiement ne génère qu'un seul enregistrement de vote
                  ->constrained()
                  ->restrictOnDelete();

            $table->unsignedSmallInteger('votes_count'); // nombre de votes comptabilisés

            $table->timestamps();

            $table->index('candidate_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
