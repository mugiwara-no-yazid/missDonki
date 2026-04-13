<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\Candidate;
use App\Models\Setting;
use App\Models\Vote;

class ResultController extends Controller
{
    public function index()
    {
        $candidates = Candidate::ranked()->get();
        $totalVotes = $candidates->sum('total_votes') ?: 1;
        $showResults = Setting::isResultsVisible();

        $ranked = $candidates->map(function ($c, $i) use ($totalVotes) {
            $c->rank       = $i + 1;
            $c->percentage = round(($c->total_votes / $totalVotes) * 100, 1);
            return $c;
        });

        return view('admin.results.index', compact('ranked', 'showResults', 'totalVotes'));
    }

    public function toggleVisibility()
    {
        $current = Setting::isResultsVisible();
        Setting::set('show_results', $current ? 'false' : 'true');
        $msg = $current ? 'Résultats masqués au public.' : 'Résultats publiés au public.';
        return back()->with('success', $msg);
    }
}