<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\Candidate;
use App\Models\Payment;
use App\Models\Vote;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // KPIs
        $totalVotes    = Vote::sum('votes_count');
        $totalRevenue  = Payment::success()->sum('amount');
        $pendingCount  = Payment::pending()->count();
        $successCount  = Payment::success()->count();
        $failedCount   = Payment::failed()->count();

        // Top 3 candidates
        $topCandidates = Candidate::ranked()->take(3)->get();

        // Tous les candidats pour classement
        $allCandidates = Candidate::ranked()->get();
        $totalVotesAll = $allCandidates->sum('total_votes') ?: 1;

        // Évolution des votes sur les 7 derniers jours
        $votesPerDay = Vote::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(votes_count) as total')
            )
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        // Remplir les jours manquants avec 0
        $labels = [];
        $data   = [];
        for ($i = 6; $i >= 0; $i--) {
            $date     = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->locale('fr')->isoFormat('D MMM');
            $data[]   = $votesPerDay[$date] ?? 0;
        }

        // Activité récente (10 derniers paiements)
        $recentPayments = Payment::with(['candidate', 'pack'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalVotes', 'totalRevenue', 'pendingCount', 'successCount', 'failedCount',
            'topCandidates', 'allCandidates', 'totalVotesAll',
            'labels', 'data', 'recentPayments'
        ));
    }
}