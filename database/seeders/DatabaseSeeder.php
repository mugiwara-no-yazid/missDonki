<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------------------------------------------
        // Packs de vote
        // -------------------------------------------------------
        DB::table('vote_packs')->insert([
            [
                'name'        => 'Pack Bronze',
                'price_fcfa'  => 100,
                'votes_count' => 1,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Pack Argent',
                'price_fcfa'  => 500,
                'votes_count' => 6,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Pack Or',
                'price_fcfa'  => 1000,
                'votes_count' => 15,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);

        // -------------------------------------------------------
        // Paramètres de configuration du site
        // -------------------------------------------------------
        $settings = [
            // Contrôle du vote
            'voting_open'           => 'true',

            // Affichage des résultats
            // 'true'  => visible en temps réel
            // 'false' => masqués jusqu'au jour du gala
            'show_results'          => 'false',

            // Informations de l'événement
            'event_name'            => 'Gala Tabaski Act 3',
            'event_date'            => '2026-05-30',
            'event_location'        => 'Salle des fêtes Jéricho',
            'organizer'             => 'Association des Guinéens au Bénin',

            // Message de transparence (affiché sur la page de vote)
            'transparency_message'  => "Chaque vote coûte 100 FCFA. Les fonds collectés servent à l'organisation du Gala Tabaski et aux récompenses des candidates.",
        ];

        foreach ($settings as $key => $value) {
            DB::table('settings')->insert([
                'key'        => $key,
                'value'      => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // -------------------------------------------------------
        // Compte administrateur par défaut
        // -------------------------------------------------------
        DB::table('admins')->insert([
            'name'       => 'Admin Gala',
            'email'      => 'admin@galatabaski.bj',
            'password'   => Hash::make('ChangeMe2026!!'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
