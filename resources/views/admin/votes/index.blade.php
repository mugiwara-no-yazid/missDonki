@extends('admin.layouts.app')
@section('title', 'Votes')
@section('page-title', 'Votes')
@section('breadcrumb', 'Classement en temps réel et historique des votes')

@push('styles')
<style>
.ranking-row {
    display: flex; align-items: center; gap: 14px;
    padding: 12px 0;
    border-bottom: 1px solid rgba(255,255,255,.04);
    transition: background .15s;
    border-radius: 6px;
    padding-left: 4px; padding-right: 4px;
}
.ranking-row:last-child { border-bottom: none; }
.ranking-row:hover { background: rgba(255,255,255,.02); }

.rank-num {
    font-family: 'Cormorant Garamond', serif;
    font-size: 22px; line-height: 1;
    min-width: 28px; text-align: right;
}
.rank-num.top { color: var(--or); }
.rank-num.other { color: var(--gris3); }

.rank-info { flex: 1; min-width: 0; }
.rank-name { font-size: 13.5px; font-weight: 500; color: var(--blanc); margin-bottom: 5px; }
.rank-bar-wrap { position: relative; }

.rank-right { text-align: right; white-space: nowrap; }
.rank-votes {
    font-family: 'Cormorant Garamond', serif;
    font-size: 20px; color: var(--or); line-height: 1;
}
.rank-pct { font-size: 11px; color: var(--gris3); }

.chart-wrap { position: relative; width: 200px; height: 200px; margin: 0 auto; }
.chart-center {
    position: absolute; top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    text-align: center; pointer-events: none;
}
.chart-center .big {
    font-family: 'Cormorant Garamond', serif;
    font-size: 28px; color: var(--blanc); line-height: 1;
}
.chart-center .sub { font-size: 11px; color: var(--gris3); margin-top: 3px; }

.op-legend { display: flex; flex-direction: column; gap: 10px; margin-top: 20px; }
.op-legend-item {
    display: flex; align-items: center; gap: 10px;
    font-size: 13px;
}
.op-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.op-name { flex: 1; color: var(--blanc); }
.op-val { font-family: 'Cormorant Garamond', serif; font-size: 18px; }
.op-pct { font-size: 12px; color: var(--gris3); min-width: 38px; text-align: right; }
</style>
@endpush

@section('content')

{{-- KPIs votes --}}
<div class="grid-4 mb-6">
    <div class="kpi">
        <div class="label">Total votes</div>
        <div class="value">{{ number_format($totalVotes) }}</div>
        <div class="sub">votes confirmés</div>
    </div>
    <div class="kpi">
        <div class="label">Candidates</div>
        <div class="value">{{ $ranking->count() }}</div>
        <div class="sub">en compétition</div>
    </div>
    <div class="kpi">
        <div class="label">MTN Mobile Money</div>
        <div class="value" style="font-size:24px;color:#f0b429;">
            {{ number_format(\App\Models\Payment::success()->where('operator','mtn')->sum('votes_count')) }}
        </div>
        <div class="sub">votes via MTN</div>
    </div>
    <div class="kpi">
        <div class="label">Moov Money</div>
        <div class="value" style="font-size:24px;color:#00b0cc;">
            {{ number_format(\App\Models\Payment::success()->where('operator','moov')->sum('votes_count')) }}
        </div>
        <div class="sub">votes via Moov</div>
    </div>
</div>

<div class="grid-2 mb-6">

    {{-- Classement --}}
    <div class="card">
        <div class="card-title">
            <span>Classement</span>
            <a href="{{ route('admin.results.index') }}" class="btn btn-outline btn-sm">
                Voir podium →
            </a>
        </div>

        @forelse($ranking as $c)
        <div class="ranking-row">
            <span class="rank-num {{ $c->rank <= 3 ? 'top' : 'other' }}">{{ $c->rank }}</span>

            <img src="{{ $c->photo_url }}"
                 style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:1px solid var(--gris1);flex-shrink:0;">

            <div class="rank-info">
                <div class="rank-name">{{ $c->name }} <span style="color:var(--gris3);font-size:11px;">N°{{ $c->number }}</span></div>
                <div class="rank-bar-wrap">
                    <div class="progress">
                        <div class="progress-bar" style="width:{{ $c->percentage }}%"></div>
                    </div>
                </div>
            </div>

            <div class="rank-right">
                <div class="rank-votes">{{ number_format($c->total_votes) }}</div>
                <div class="rank-pct">{{ $c->percentage }}%</div>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:40px;color:var(--gris3);font-size:13px;">
            Aucun vote enregistré pour le moment.
        </div>
        @endforelse
    </div>

    {{-- Répartition opérateurs --}}
    <div class="card">
        <div class="card-title">Répartition par opérateur</div>

        @php
            $mtnVotes  = \App\Models\Payment::success()->where('operator','mtn')->sum('votes_count');
            $moovVotes = \App\Models\Payment::success()->where('operator','moov')->sum('votes_count');
            $totalOp   = ($mtnVotes + $moovVotes) ?: 1;
        @endphp

        <div class="chart-wrap">
            <canvas id="opChart" width="200" height="200"></canvas>
            <div class="chart-center">
                <div class="big">{{ number_format($totalVotes) }}</div>
                <div class="sub">votes</div>
            </div>
        </div>

        <div class="op-legend">
            <div class="op-legend-item">
                <span class="op-dot" style="background:#f0b429;"></span>
                <span class="op-name">MTN Mobile Money</span>
                <span class="op-val" style="color:#f0b429;">{{ number_format($mtnVotes) }}</span>
                <span class="op-pct">{{ round(($mtnVotes / $totalOp) * 100) }}%</span>
            </div>
            <div class="op-legend-item">
                <span class="op-dot" style="background:#00b0cc;"></span>
                <span class="op-name">Moov Money</span>
                <span class="op-val" style="color:#00b0cc;">{{ number_format($moovVotes) }}</span>
                <span class="op-pct">{{ round(($moovVotes / $totalOp) * 100) }}%</span>
            </div>
        </div>

        {{-- Répartition par pack --}}
        <hr class="divider">
        <div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;color:var(--gris3);margin-bottom:12px;">
            Votes par pack
        </div>
        @foreach(\App\Models\VotePack::orderBy('price_fcfa')->get() as $pack)
        @php
            $packVotes = \App\Models\Payment::success()->where('pack_id', $pack->id)->sum('votes_count');
        @endphp
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
            <div style="flex:1;">
                <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                    <span style="font-size:12.5px;color:var(--blanc);">{{ $pack->name }}</span>
                    <span style="font-size:12px;color:var(--gris3);">{{ number_format($packVotes) }} votes</span>
                </div>
                <div class="progress">
                    <div class="progress-bar"
                         style="width:{{ $totalVotes > 0 ? round(($packVotes / $totalVotes) * 100) : 0 }}%;
                                background: linear-gradient(90deg, var(--gris2), var(--gris3));">
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Historique paginé --}}
<div class="card">
    <div class="card-title">
        <span>Historique des votes</span>
        <span style="font-size:12px;color:var(--gris3);">{{ $votesDetail->total() }} entrée(s)</span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Candidate</th>
                    <th>Votes comptabilisés</th>
                    <th>Montant payé</th>
                    <th>Opérateur</th>
                    <th>Téléphone</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
            @forelse($votesDetail as $v)
                <tr>
                    <td style="color:var(--gris3);font-size:11px;">#{{ $v->id }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <img src="{{ $v->candidate->photo_url ?? '' }}"
                                 style="width:30px;height:30px;border-radius:50%;object-fit:cover;border:1px solid var(--gris1);">
                            <div>
                                <div style="color:var(--blanc);font-size:13px;">{{ $v->candidate->name ?? '—' }}</div>
                                <div style="color:var(--gris3);font-size:11px;">N°{{ $v->candidate->number ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="font-family:'Cormorant Garamond',serif;font-size:20px;color:var(--or);">
                            +{{ $v->votes_count }}
                        </span>
                    </td>
                    <td style="color:var(--gris3);font-size:13px;">
                        {{ number_format($v->payment->amount ?? 0) }} FCFA
                    </td>
                    <td>
                        @if($v->payment)
                        <span style="font-size:12px;font-weight:500;
                                     color:{{ $v->payment->operator === 'mtn' ? '#f0b429' : '#00b0cc' }}">
                            {{ strtoupper($v->payment->operator) }}
                        </span>
                        @else
                        <span style="color:var(--gris3);">—</span>
                        @endif
                    </td>
                    <td style="color:var(--gris3);font-size:12.5px;">
                        {{ $v->payment->phone_number ?? '—' }}
                    </td>
                    <td style="color:var(--gris3);font-size:12px;white-space:nowrap;">
                        {{ $v->created_at->format('d/m/Y') }}
                        <span style="display:block;font-size:11px;">{{ $v->created_at->format('H:i') }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:var(--gris3);">
                        Aucun vote enregistré pour le moment.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $votesDetail->links('admin.partials.pagination') }}
</div>

@endsection

@push('scripts')
<script>
const mtn  = {{ $mtnVotes }};
const moov = {{ $moovVotes }};
const ctx  = document.getElementById('opChart').getContext('2d');

new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['MTN Mobile Money', 'Moov Money'],
        datasets: [{
            data: [mtn || 0.001, moov || 0.001],
            backgroundColor: ['rgba(240,180,41,.25)', 'rgba(0,176,204,.25)'],
            borderColor:      ['#f0b429', '#00b0cc'],
            borderWidth: 1.5,
            hoverBackgroundColor: ['rgba(240,180,41,.4)', 'rgba(0,176,204,.4)'],
        }]
    },
    options: {
        responsive: false,
        cutout: '72%',
        plugins: { legend: { display: false }, tooltip: {
            callbacks: {
                label: ctx => ' ' + ctx.label + ' : ' + ctx.parsed.toFixed(0) + ' votes'
            }
        }},
    }
});
</script>
@endpush