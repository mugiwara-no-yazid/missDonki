@extends('admin.layouts.app')
@section('title', 'Résultats')
@section('page-title', 'Résultats')
@section('breadcrumb', 'Classement final — Miss Populaire Gala Tabaski Act 3')

@push('styles')
<style>
/* Bannière statut */
.result-banner {
    display: flex; align-items: center; justify-content: space-between; gap: 16px;
    padding: 16px 22px;
    border-radius: 10px; margin-bottom: 28px;
    border: 1px solid;
}
.result-banner.visible {
    background: rgba(39,174,96,.07);
    border-color: rgba(39,174,96,.25);
}
.result-banner.hidden {
    background: rgba(201,168,76,.06);
    border-color: rgba(201,168,76,.2);
}
.result-banner .status-icon { font-size: 22px; }
.result-banner .status-text .t1 { font-size: 14px; font-weight: 500; }
.result-banner .status-text .t2 { font-size: 12px; color: var(--gris3); margin-top: 3px; }

/* Podium */
.podium-wrap {
    display: flex; align-items: flex-end; justify-content: center;
    gap: 12px; padding: 0 20px 0;
}
.podium-slot { display: flex; flex-direction: column; align-items: center; flex: 1; max-width: 200px; }
.podium-slot__card {
    width: 100%; text-align: center;
    padding-bottom: 12px;
}
.podium-slot__photo {
    border-radius: 50%; object-fit: cover; object-position: top;
    display: block; margin: 0 auto 8px;
    border: 3px solid var(--gris2);
}
.podium-slot.first .podium-slot__photo { width: 100px; height: 100px; border-color: var(--or); }
.podium-slot.second .podium-slot__photo { width: 80px; height: 80px; border-color: #aaa; }
.podium-slot.third .podium-slot__photo  { width: 70px; height: 70px; border-color: #8b6914; }

.podium-slot__num { font-size: 11px; color: var(--gris3); margin-bottom: 3px; }
.podium-slot__name { font-size: 13px; font-weight: 500; color: var(--blanc); margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; }
.podium-slot__votes { font-family: 'Cormorant Garamond', serif; font-size: 20px; color: var(--or); }
.podium-slot__pct { font-size: 11px; color: var(--gris3); }
.podium-slot__crown { font-size: 22px; margin-bottom: 4px; }

.podium-base {
    width: 100%; border-radius: 8px 8px 0 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 26px;
}
.podium-slot.first  .podium-base { height: 100px; background: linear-gradient(180deg,rgba(201,168,76,.25) 0%,rgba(201,168,76,.08) 100%); border: 1px solid rgba(201,168,76,.3); }
.podium-slot.second .podium-base { height: 75px;  background: rgba(180,180,180,.1); border: 1px solid rgba(180,180,180,.2); }
.podium-slot.third  .podium-base { height: 55px;  background: rgba(139,105,20,.12); border: 1px solid rgba(139,105,20,.2); }

.podium-slot__placeholder {
    border-radius: 50%; border: 2px dashed var(--gris2);
    display: flex; align-items: center; justify-content: center;
    color: var(--gris2); font-size: 22px; margin: 0 auto 8px;
}
.podium-slot.first  .podium-slot__placeholder { width:100px; height:100px; }
.podium-slot.second .podium-slot__placeholder { width: 80px; height: 80px; }
.podium-slot.third  .podium-slot__placeholder { width: 70px; height: 70px; }

/* Table classement */
.rank-badge {
    display: inline-flex; align-items: center; justify-content: center;
    width: 28px; height: 28px; border-radius: 50%;
    font-family: 'Cormorant Garamond', serif; font-size: 16px; font-weight: 600;
}
.rank-badge.top1 { background: rgba(201,168,76,.2); color: var(--or); border: 1px solid rgba(201,168,76,.4); }
.rank-badge.top2 { background: rgba(180,180,180,.1); color: #bbb; border: 1px solid rgba(180,180,180,.25); }
.rank-badge.top3 { background: rgba(139,105,20,.15); color: #a07828; border: 1px solid rgba(139,105,20,.25); }
.rank-badge.other { background: transparent; color: var(--gris3); border: 1px solid var(--gris1); }
</style>
@endpush

@section('content')

{{-- Bannière visibilité --}}
<div class="result-banner {{ $showResults ? 'visible' : 'hidden' }}">
    <div style="display:flex;align-items:center;gap:14px;">
        <span class="status-icon">{{ $showResults ? '👁' : '🔒' }}</span>
        <div class="status-text">
            <div class="t1" style="color:{{ $showResults ? '#5dbb7a' : 'var(--or)' }}">
                {{ $showResults ? 'Résultats visibles sur le site public' : 'Résultats masqués au public' }}
            </div>
            <div class="t2">
                {{ $showResults
                    ? 'Le classement et les scores sont actuellement affichés sur le site.'
                    : 'Le classement sera révélé lors de la cérémonie du ' . (\Carbon\Carbon::parse(\App\Models\Setting::get('event_date'))->locale('fr')->isoFormat('D MMMM YYYY')) . '.' }}
            </div>
        </div>
    </div>
    <form method="POST" action="{{ route('admin.results.toggle') }}" style="flex-shrink:0;">
        @csrf
        <button type="submit" class="btn {{ $showResults ? 'btn-danger' : 'btn-or' }}">
            {{ $showResults ? '🔒 Masquer au public' : '👁 Publier les résultats' }}
        </button>
    </form>
</div>

{{-- Podium --}}
<div class="card mb-6">
    <div class="card-title" style="text-align:center;justify-content:center;font-size:18px;margin-bottom:28px;">
        Podium — Miss Populaire
    </div>

    @if($ranked->isEmpty())
        <div style="text-align:center;padding:40px;color:var(--gris3);">
            Aucun vote enregistré. Le podium s'affichera dès les premiers votes.
        </div>
    @else
    <div class="podium-wrap">
        {{-- 2e place (à gauche) --}}
        <div class="podium-slot second">
            <div class="podium-slot__card">
                @php $s = $ranked->get(1) @endphp
                @if($s)
                    <img src="{{ $s->photo_url }}" class="podium-slot__photo" alt="{{ $s->name }}">
                    <div class="podium-slot__num">N°{{ $s->number }}</div>
                    <div class="podium-slot__name">{{ $s->name }}</div>
                    <div class="podium-slot__votes">{{ number_format($s->total_votes) }}</div>
                    <div class="podium-slot__pct">{{ $s->percentage }}%</div>
                @else
                    <div class="podium-slot__placeholder">—</div>
                    <div class="podium-slot__name" style="color:var(--gris3);">— à déterminer —</div>
                @endif
            </div>
            <div class="podium-base">🥈</div>
        </div>

        {{-- 1re place (au centre, surélevée) --}}
        <div class="podium-slot first">
            <div class="podium-slot__card">
                @php $f = $ranked->get(0) @endphp
                @if($f)
                    <div class="podium-slot__crown">👑</div>
                    <img src="{{ $f->photo_url }}" class="podium-slot__photo" alt="{{ $f->name }}">
                    <div class="podium-slot__num">N°{{ $f->number }}</div>
                    <div class="podium-slot__name">{{ $f->name }}</div>
                    <div class="podium-slot__votes" style="font-size:26px;">{{ number_format($f->total_votes) }}</div>
                    <div class="podium-slot__pct">{{ $f->percentage }}%</div>
                @else
                    <div class="podium-slot__crown" style="opacity:.3;">👑</div>
                    <div class="podium-slot__placeholder">—</div>
                    <div class="podium-slot__name" style="color:var(--gris3);">— à déterminer —</div>
                @endif
            </div>
            <div class="podium-base">🥇</div>
        </div>

        {{-- 3e place (à droite) --}}
        <div class="podium-slot third">
            <div class="podium-slot__card">
                @php $t = $ranked->get(2) @endphp
                @if($t)
                    <img src="{{ $t->photo_url }}" class="podium-slot__photo" alt="{{ $t->name }}">
                    <div class="podium-slot__num">N°{{ $t->number }}</div>
                    <div class="podium-slot__name">{{ $t->name }}</div>
                    <div class="podium-slot__votes" style="font-size:18px;">{{ number_format($t->total_votes) }}</div>
                    <div class="podium-slot__pct">{{ $t->percentage }}%</div>
                @else
                    <div class="podium-slot__placeholder">—</div>
                    <div class="podium-slot__name" style="color:var(--gris3);">— à déterminer —</div>
                @endif
            </div>
            <div class="podium-base">🥉</div>
        </div>
    </div>
    @endif
</div>

{{-- Classement complet --}}
<div class="card">
    <div class="card-title">
        <span>Classement complet</span>
        <span style="font-size:12px;color:var(--gris3);">
            {{ number_format($totalVotes) }} votes — {{ $ranked->count() }} candidates
        </span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:50px;">Rang</th>
                    <th>Candidate</th>
                    <th>Votes</th>
                    <th>Part</th>
                    <th style="width:260px;">Progression</th>
                    <th>Revenus générés</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
            @foreach($ranked as $c)
            @php
                $rankClass = match($c->rank) { 1 => 'top1', 2 => 'top2', 3 => 'top3', default => 'other' };
                $revenue   = \App\Models\Payment::where('candidate_id', $c->id)->where('status','success')->sum('amount');
            @endphp
            <tr>
                <td>
                    <span class="rank-badge {{ $rankClass }}">{{ $c->rank }}</span>
                </td>
                <td>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <img src="{{ $c->photo_url }}"
                             style="width:40px;height:40px;border-radius:50%;object-fit:cover;object-position:top;
                                    border:{{ $c->rank === 1 ? '2px solid var(--or)' : '1px solid var(--gris1)' }}">
                        <div>
                            <div style="color:var(--blanc);font-size:13.5px;font-weight:500;">{{ $c->name }}</div>
                            <div style="color:var(--gris3);font-size:11px;">N°{{ $c->number }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span style="font-family:'Cormorant Garamond',serif;font-size:22px;color:var(--or);">
                        {{ number_format($c->total_votes) }}
                    </span>
                </td>
                <td style="white-space:nowrap;">
                    <span style="font-size:14px;font-weight:500;
                                 color:{{ $c->rank === 1 ? 'var(--or)' : 'var(--gris3)' }}">
                        {{ $c->percentage }}%
                    </span>
                </td>
                <td>
                    <div class="progress">
                        <div class="progress-bar"
                             style="width:{{ $c->percentage }}%;
                                    background: {{ $c->rank === 1
                                        ? 'linear-gradient(90deg,var(--or),var(--or2))'
                                        : 'linear-gradient(90deg,var(--gris2),var(--gris3))' }}">
                        </div>
                    </div>
                </td>
                <td style="font-family:'Cormorant Garamond',serif;font-size:17px;color:var(--gris3);">
                    {{ number_format($revenue) }}
                    <span style="font-size:11px;font-family:'DM Sans',sans-serif;"> F</span>
                </td>
                <td>
                    <span class="badge badge-{{ $c->is_active ? 'active' : 'inactive' }}">
                        {{ $c->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection