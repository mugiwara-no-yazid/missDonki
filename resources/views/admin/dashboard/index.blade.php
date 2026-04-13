@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Tableau de bord')

@section('content')

{{-- KPIs --}}
<div class="grid-4 mb-6">
    <div class="kpi">
        <div class="label">Total votes</div>
        <div class="value">{{ number_format($totalVotes) }}</div>
        <div class="sub">votes confirmés</div>
    </div>
    <div class="kpi">
        <div class="label">Revenus collectés</div>
        <div class="value">{{ number_format($totalRevenue) }}</div>
        <div class="sub">FCFA encaissés</div>
    </div>
    <div class="kpi">
        <div class="label">Paiements réussis</div>
        <div class="value" style="color:#5dbb7a">{{ $successCount }}</div>
        <div class="sub">transactions validées</div>
    </div>
    <div class="kpi">
        <div class="label">En attente / Échoués</div>
        <div class="value" style="color:var(--or)">{{ $pendingCount }}</div>
        <div class="sub">{{ $failedCount }} échoués</div>
    </div>
</div>

{{-- Graphique + Top candidates --}}
<div class="grid-2 mb-6">
    {{-- Graphique votes --}}
    <div class="card">
        <div class="card-title">Votes — 7 derniers jours</div>
        <canvas id="votesChart" height="180"></canvas>
    </div>

    {{-- Top 3 --}}
    <div class="card">
        <div class="card-title">
            <span>Top candidates</span>
            <a href="{{ route('admin.results.index') }}" class="btn btn-outline btn-sm">Voir tout</a>
        </div>
        @forelse($topCandidates as $i => $c)
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px;">
            <span class="rank-medal">{{ ['🥇','🥈','🥉'][$i] }}</span>
            <img src="{{ $c->photo_url }}" class="candidate-avatar" alt="{{ $c->name }}">
            <div style="flex:1;min-width:0;">
                <div style="font-weight:500;color:var(--blanc);font-size:13.5px;">
                    N°{{ $c->number }} — {{ $c->name }}
                </div>
                <div class="progress mt-4" style="margin-top:6px;">
                    @php $pct = round(($c->total_votes / ($allCandidates->sum('total_votes') ?: 1)) * 100, 1) @endphp
                    <div class="progress-bar" style="width:{{ $pct }}%"></div>
                </div>
            </div>
            <span class="or-accent" style="font-size:18px;white-space:nowrap;">{{ number_format($c->total_votes) }} v.</span>
        </div>
        @empty
            <p class="text-gris" style="font-size:13px;">Aucune candidate pour le moment.</p>
        @endforelse
    </div>
</div>

{{-- Classement complet + Activité récente --}}
<div class="grid-2">
    {{-- Classement --}}
    <div class="card">
        <div class="card-title">Classement complet</div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Candidate</th>
                        <th>Votes</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($allCandidates as $i => $c)
                    @php $pct = round(($c->total_votes / $totalVotesAll) * 100, 1) @endphp
                    <tr>
                        <td style="color:var(--gris3);font-size:12px;">{{ $i + 1 }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <img src="{{ $c->photo_url }}" class="candidate-avatar">
                                <span style="color:var(--blanc);">{{ $c->name }}</span>
                            </div>
                        </td>
                        <td class="text-or" style="font-family:'Cormorant Garamond',serif;font-size:17px;">
                            {{ number_format($c->total_votes) }}
                        </td>
                        <td>
                            <span style="font-size:12px;color:var(--gris3);">{{ $pct }}%</span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Activité récente --}}
    <div class="card">
        <div class="card-title">
            <span>Activité récente</span>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline btn-sm">Tout voir</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Candidate</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recentPayments as $p)
                    <tr>
                        <td style="color:var(--blanc);font-size:13px;">
                            {{ $p->candidate->name ?? '—' }}
                        </td>
                        <td class="text-or">{{ number_format($p->amount) }} F</td>
                        <td>
                            <span class="badge badge-{{ $p->status }}">{{ $p->status_label }}</span>
                        </td>
                        <td style="color:var(--gris3);font-size:12px;">
                            {{ $p->created_at->diffForHumans() }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-gris">Aucune activité.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const ctx = document.getElementById('votesChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($labels) !!},
        datasets: [{
            label: 'Votes',
            data: {!! json_encode($data) !!},
            borderColor: '#c9a84c',
            backgroundColor: 'rgba(201,168,76,.08)',
            borderWidth: 2,
            pointBackgroundColor: '#c9a84c',
            pointRadius: 4,
            tension: .35,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: 'rgba(255,255,255,.04)' }, ticks: { color: '#888', font: { family: 'DM Sans', size: 12 } } },
            y: { grid: { color: 'rgba(255,255,255,.04)' }, ticks: { color: '#888', font: { family: 'DM Sans', size: 12 } }, beginAtZero: true }
        }
    }
});
</script>
@endpush