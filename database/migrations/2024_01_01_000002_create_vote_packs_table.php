<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vote_packs', function (Blueprint $table) {
            $table->id();
            $table->string('name');                         // ex: "Pack Bronze", "Pack Argent"
            $table->unsignedInteger('price_fcfa');          // 100 | 500 | 1000
            $table->unsignedSmallInteger('votes_count');    // 1  | 6   | 15
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vote_packs');
    }
};
