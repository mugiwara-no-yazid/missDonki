<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Setting;
use App\Models\Vote;
use App\Models\VotePack;
use Illuminate\Routing\Controller;

class PublicController extends Controller
{
    public function home()
    {
        return view('publics.home', [
            'votingOpen'      => Setting::isVotingOpen(),
            'eventDate'       => Setting::get('event_date', '2026-05-30'),
            'eventLocation'   => Setting::get('event_location', 'Salle des fêtes Jéricho'),
            'candidatesCount' => Candidate::where('is_active', true)->count(),
            'totalVotes'      => Vote::sum('votes_count'),
            'packs'           => VotePack::active()->get(),
        ]);
    }

    public function candidates()
    {

       
        return view('publics.candidates', [
            'candidates'  => Candidate::where('is_active', true)->orderBy('number')->get(),
            'votingOpen'  => Setting::isVotingOpen(), 
            'showVotes'   => Setting::isResultsVisible(),
        ]);
    }

    public function results()
    {
        $showResults = Setting::isResultsVisible();
        $totalVotes  = 1;
        $ranked      = collect();

        if ($showResults) {
            $candidates = Candidate::ranked()->get();
            $totalVote = $candidates->sum('total_votes') ?: 1;
            $totalVotes = $candidates->sum('total_votes') ?: 0;
            $ranked = $candidates->map(function ($c, $i) use ($totalVote) {
                $c->rank       = $i + 1;
                $c->percentage = round(($c->total_votes / $totalVote) * 100, 1);
                return $c;
            });
        }

        return view('publics.results', [
            'showResults'   => $showResults,
            'ranked'        => $ranked,
            'totalVotes'    => $totalVotes,
            'votingOpen'    => Setting::isVotingOpen(),
            'eventDate'     => Setting::get('event_date', '2026-05-30'),
            'eventLocation' => Setting::get('event_location', 'Salle des fêtes Jéricho'),
        ]);
    }
}