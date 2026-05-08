@extends('layouts.public')
@section('title', 'Résultats — Gala Tabaski Act 3')

@push('styles')
<style>
#results-page { position: relative; z-index: 1; padding: 120px 24px 100px; }

.results-container { max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px; }

.rank-item {
    display: flex; align-items: center; gap: 20px;
    background: linear-gradient(90deg, rgba(255,255,255,.03), transparent);
    border: 1px solid rgba(201,168,76,.1); border-radius: 10px;
    padding: 20px 28px; transition: all .3s; position: relative; overflow: hidden;
}
.rank-item:hover { border-color: rgba(201,168,76,.3); transform: translateX(4px); }
.rank-item.rank-1 { border-color: rgba(201,168,76,.4); background: linear-gradient(90deg, rgba(201,168,76,.06), transparent); }
.rank-item.rank-2 { border-color: rgba(192,192,192,.2); background: linear-gradient(90deg, rgba(192,192,192,.03), transparent); }
.rank-item.rank-3 { border-color: rgba(205,127,50,.2); background: linear-gradient(90deg, rgba(205,127,50,.03), transparent); }

.rank-number { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 900; min-width: 50px; text-align: center; }
.rank-1 .rank-number { color: var(--or); }
.rank-2 .rank-number { color: #c0c0c0; }
.rank-3 .rank-number { color: #cd7f32; }
.rank-4plus .rank-number { color: rgba(253,250,244,.3); font-size: 1.4rem; }

.rank-avatar {
    width: 64px; height: 64px; border-radius: 50%;
    object-fit: cover; flex-shrink: 0;
    border: 2px solid rgba(201,168,76,.2);
}
.rank-avatar-placeholder {
    width: 64px; height: 64px; border-radius: 50%; flex-shrink: 0;
    background: rgba(192,0,26,.15); border: 2px solid rgba(201,168,76,.2);
    display: flex; align-items: center; justify-content: center; font-size: 1.6rem;
}
.rank-info { flex: 1; }
.rank-name { font-family: 'Playfair Display', serif; font-size: 1.1rem; font-weight: 700; margin-bottom: 6px; }
.rank-tag  { font-size: .6rem; letter-spacing: 2px; color: var(--or); text-transform: uppercase; margin-bottom: 8px; }
.rank-bar-wrap { display: flex; align-items: center; gap: 12px; }
.rank-bar { flex: 1; height: 5px; background: rgba(255,255,255,.05); border-radius: 3px; overflow: hidden; }
.rank-bar-fill { height: 100%; border-radius: 3px; transition: width 2s cubic-bezier(.25,.8,.25,1); }
.rank-1 .rank-bar-fill { background: linear-gradient(90deg, var(--rouge), var(--or)); }
.rank-2 .rank-bar-fill { background: linear-gradient(90deg, #888, #c0c0c0); }
.rank-3 .rank-bar-fill { background: linear-gradient(90deg, #8b6324, #cd7f32); }
.rank-4plus .rank-bar-fill { background: rgba(201,168,76,.3); }
.rank-votes { font-family: 'Playfair Display', serif; font-size: 1.1rem; color: var(--or); font-weight: 700; min-width: 70px; text-align: right; }
.rank-votes .vote-label { display: block; font-size: .55rem; color: rgba(253,250,244,.4); font-family: 'Montserrat', sans-serif; letter-spacing: 1px; text-transform: uppercase; font-weight: 500; }

.btn-vote-mini {
    display: inline-flex; align-items: center; gap: 6px;
    background: linear-gradient(135deg, var(--rouge), var(--rouge-fonce));
    color: var(--blanc); padding: 8px 18px; border-radius: 4px;
    font-size: .65rem; font-weight: 700; letter-spacing: 2px; text-transform: uppercase;
    text-decoration: none; border: 1px solid rgba(201,168,76,.25);
    cursor: pointer; transition: all .25s; flex-shrink: 0;
    box-shadow: 0 4px 15px rgba(192,0,26,.3);
}
.btn-vote-mini:hover { transform: translateY(-2px); box-shadow: 0 6px 25px rgba(192,0,26,.5); }

/* Résultats masqués */
.hidden-box {
    max-width: 560px; margin: 60px auto;
    background: linear-gradient(135deg, rgba(192,0,26,.06), rgba(201,168,76,.04));
    border: 1px solid rgba(201,168,76,.2); border-radius: 16px;
    padding: 60px 40px; text-align: center;
}
.hidden-icon { font-size: 3.5rem; margin-bottom: 24px; opacity: .6; }
.hidden-title { font-family: 'Playfair Display', serif; font-size: 2rem; color: var(--or); margin-bottom: 12px; }
.hidden-sub { font-size: .85rem; color: rgba(253,250,244,.6); line-height: 1.8; margin-bottom: 32px; }

.btn-cta {
    display: inline-flex; align-items: center; gap: 10px;
    background: linear-gradient(135deg, var(--rouge), var(--rouge-fonce));
    color: var(--blanc); padding: 16px 40px; border-radius: 4px;
    font-size: .72rem; font-weight: 700; letter-spacing: 4px; text-transform: uppercase;
    text-decoration: none; border: 1px solid rgba(201,168,76,.3); transition: all .3s;
    box-shadow: 0 8px 30px rgba(192,0,26,.3);
}
.btn-cta:hover { box-shadow: 0 12px 40px rgba(192,0,26,.5); transform: translateY(-2px); }

@media(max-width:600px) {
    .rank-item { padding: 14px; gap: 12px; flex-wrap: wrap; }
    .rank-avatar, .rank-avatar-placeholder { width: 46px; height: 46px; }
    .rank-number { font-size: 1.6rem; min-width: 36px; }
    .rank-bar-wrap { display: none; }
    .btn-vote-mini { width: 100%; justify-content: center; margin-top: 8px; }
}
</style>
@endpush

@section('content')

<section id="results-page">
    <p class="section-label">Classement</p>
    <h2 class="section-title">
        Résultats en<br>
        <em style="color:var(--or)">Temps Réel</em>
    </h2>
    <div class="section-divider">
        <div class="divider-line"></div>
        <span class="divider-gem">✦</span>
        <div class="divider-line right"></div>
    </div>

    @if(!$showResults)
    <div class="hidden-box">
        <div class="hidden-icon">🔒</div>
        <h2 class="hidden-title">Résultats non encore disponibles</h2>
        <p class="hidden-sub">
            Le classement sera révélé lors de la soirée du Gala.<br>
            <strong style="color:var(--or)">
                {{ \Carbon\Carbon::parse($eventDate)->locale('fr')->isoFormat('D MMMM YYYY') }}
            </strong><br>
            {{ $eventLocation }}
        </p>
        @if($votingOpen)
        <a href="{{ route('candidates') }}" class="btn-cta">👑 Voter maintenant</a>
        @endif
    </div>

    @else




    <div class="results-container">
        @foreach($ranked as $c)
        @php
            $pct = round(($c->total_votes / ($totalVotes ?: 1)) * 100);
            $medals = ['🥇','🥈','🥉'];
            $rankClass = $c->rank <= 3 ? 'rank-' . $c->rank : 'rank-4plus';
        @endphp
        <div class="rank-item {{ $rankClass }}">
            <div class="rank-number">{{ $medals[$c->rank - 1] ?? $c->rank }}</div>

            @if($c->photo_path)
                <img src="{{ $c->photo_url }}" alt="{{ $c->name }}" class="rank-avatar">
            @else
                <div class="rank-avatar-placeholder">♛</div>
            @endif

            <div class="rank-info">
                <div class="rank-name">{{ $c->name }}</div>
                <div class="rank-tag">Candidate N°{{ $c->number }}</div>
                <div class="rank-bar-wrap">
                    <div class="rank-bar">
                        <div class="rank-bar-fill" style="width:{{ $pct }}%"></div>
                    </div>
                    <div class="rank-votes">
                        {{ number_format($c->total_votes) }}
                        <span class="vote-label">votes — {{ $pct }}%</span>
                    </div>
                </div>
            </div>

            @if($votingOpen)
            <a href="{{ route('candidates') }}#candidate-{{ $c->id }}" class="btn-vote-mini">✨ Voter</a>
            @endif
        </div>
        @endforeach
    </div>

    @if($votingOpen)
    <div style="text-align:center;margin-top:48px;">
        <a href="{{ route('candidates') }}" class="btn-cta">👑 Voter pour changer le classement</a>
    </div>
    @endif

    @endif
</section>

@endsection



