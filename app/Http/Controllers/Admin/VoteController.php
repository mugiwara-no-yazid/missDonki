<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\Candidate;
use App\Models\Vote;

class VoteController extends Controller
{
    public function index()
    {
        $candidates  = Candidate::orderBy('number')->get();
        $totalVotes  = Vote::sum('votes_count') ?: 1;
        $votesDetail = Vote::with(['candidate', 'payment'])
            ->latest()
            ->paginate(30);

        // Classement
        $ranking = Candidate::ranked()->get()->map(function ($c) use ($totalVotes) {
            $c->percentage = round(($c->total_votes / $totalVotes) * 100, 1);
            return $c;
        });

        return view('admin.votes.index', compact('candidates', 'votesDetail', 'ranking', 'totalVotes'));
    }
}