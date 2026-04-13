<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedSmallInteger('number')->unique();
            $table->string('photo_path')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('total_votes')->default(0); // dénormalisé pour classement rapide
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
