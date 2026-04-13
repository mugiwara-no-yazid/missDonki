<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'voting_open'          => Setting::get('voting_open', 'true'),
            'show_results'         => Setting::get('show_results', 'false'),
            'event_name'           => Setting::get('event_name', 'Gala Tabaski Act 3'),
            'event_date'           => Setting::get('event_date', '2026-05-30'),
            'event_location'       => Setting::get('event_location', 'Salle des fêtes Jéricho'),
            'organizer'            => Setting::get('organizer', 'Association des Guinéens au Bénin'),
            'transparency_message' => Setting::get('transparency_message'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'event_name'           => 'required|string|max:120',
            'event_date'           => 'required|date',
            'event_location'       => 'required|string|max:200',
            'organizer'            => 'required|string|max:200',
            'transparency_message' => 'required|string|max:500',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        // Toggles booléens
        Setting::set('voting_open',  $request->boolean('voting_open')  ? 'true' : 'false');
        Setting::set('show_results', $request->boolean('show_results') ? 'true' : 'false');

        return back()->with('success', 'Paramètres sauvegardés.');
    }
}