@extends('admin.layouts.app')
@section('title', 'Candidates')
@section('page-title', 'Candidates')
@section('breadcrumb', 'Gestion des candidates au concours Miss Populaire')

@push('styles')
<style>
.candidates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 20px;
}
.c-card {
    background: var(--noir2);
    border: 1px solid var(--gris1);
    border-radius: 14px;
    overflow: hidden;
    transition: border-color .2s, transform .2s;
    position: relative;
}
.c-card:hover { border-color: var(--gris2); transform: translateY(-2px); }
.c-card.inactive { opacity: .5; }

.c-card__photo {
    width: 100%; height: 220px;
    object-fit: cover; object-position: top center;
    display: block; background: var(--noir3);
}
.c-card__placeholder {
    width: 100%; height: 220px;
    background: var(--noir3);
    display: flex; align-items: center; justify-content: center;
    font-size: 52px; color: var(--gris2);
}
.c-card__num {
    position: absolute; top: 12px; left: 12px;
    background: rgba(10,10,10,.8);
    border: 1px solid rgba(201,168,76,.5);
    border-radius: 6px; padding: 3px 10px;
    font-family: 'Cormorant Garamond', serif;
    font-size: 15px; color: var(--or);
}
.c-card__badge { position: absolute; top: 12px; right: 12px; }
.c-card__body { padding: 16px; }
.c-card__name {
    font-weight: 500; color: var(--blanc); font-size: 15px;
    margin-bottom: 4px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.c-card__bio {
    font-size: 12px; color: var(--gris3); line-height: 1.5;
    height: 36px; overflow: hidden;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    margin-bottom: 12px;
}
.c-card__votes-row {
    display: flex; align-items: baseline; gap: 5px;
    padding-bottom: 12px; margin-bottom: 12px;
    border-bottom: 1px solid var(--gris1);
}
.c-card__votes-row .n {
    font-family: 'Cormorant Garamond', serif;
    font-size: 28px; color: var(--or); line-height: 1;
}
.c-card__votes-row .lbl { font-size: 11px; color: var(--gris3); }
.c-card__votes-row .pct { margin-left: auto; font-size: 12px; color: var(--gris3); }
.c-card__actions { display: flex; gap: 7px; }

.search-bar { position: relative; width: 280px; }
.search-bar input {
    width: 100%; padding: 9px 14px 9px 36px;
    background: var(--noir2); border: 1px solid var(--gris1);
    border-radius: 8px; color: var(--blanc);
    font-family: 'DM Sans', sans-serif; font-size: 13.5px;
    outline: none; transition: border-color .18s;
}
.search-bar input:focus { border-color: var(--or); }
.search-bar input::placeholder { color: var(--gris3); }
.search-bar .s-icon {
    position: absolute; left: 11px; top: 50%;
    transform: translateY(-50%); color: var(--gris3);
    font-size: 15px; pointer-events: none;
}
.filter-pills { display: flex; gap: 6px; }
.fpill {
    padding: 6px 14px; border-radius: 20px; font-size: 12px;
    border: 1px solid var(--gris1); color: var(--gris3);
    cursor: pointer; transition: all .15s; background: transparent;
    font-family: 'DM Sans', sans-serif; text-decoration: none;
    white-space: nowrap;
}
.fpill:hover, .fpill.on { border-color: var(--or); color: var(--or); background: rgba(201,168,76,.08); }
.empty-state { text-align: center; padding: 80px 40px; color: var(--gris3); }
.empty-state .eico { font-size: 48px; margin-bottom: 16px; opacity: .3; }
</style>
@endpush

@section('content')

@php
    $totalVotes = $candidates->sum('total_votes') ?: 1;
    $filter     = request('filter');
    $shown      = match($filter) {
        'active'   => $candidates->where('is_active', true),
        'inactive' => $candidates->where('is_active', false),
        default    => $candidates,
    };
@endphp

{{-- Toolbar --}}
<div class="flex items-center justify-between mb-6" style="flex-wrap:wrap;gap:12px;">
    <div class="flex items-center gap-3" style="flex-wrap:wrap;gap:10px;">
        <div class="search-bar">
            <span class="s-icon">⌕</span>
            <input type="text" id="searchInput" placeholder="Rechercher…">
        </div>
        <div class="filter-pills">
            <a href="{{ route('admin.candidates.index') }}"
               class="fpill {{ !$filter ? 'on' : '' }}">
                Toutes ({{ $candidates->count() }})
            </a>
            <a href="{{ route('admin.candidates.index', ['filter' => 'active']) }}"
               class="fpill {{ $filter === 'active' ? 'on' : '' }}">
                Actives ({{ $candidates->where('is_active', true)->count() }})
            </a>
            <a href="{{ route('admin.candidates.index', ['filter' => 'inactive']) }}"
               class="fpill {{ $filter === 'inactive' ? 'on' : '' }}">
                Inactives ({{ $candidates->where('is_active', false)->count() }})
            </a>
        </div>
    </div>
    <a href="{{ route('admin.candidates.create') }}" class="btn btn-or">
        + Ajouter une candidate
    </a>
</div>

{{-- Grille --}}
@if($shown->isEmpty())
    <div class="empty-state">
        <div class="eico">♛</div>
        <p>Aucune candidate trouvée.</p>
        @if(!$filter)
        <a href="{{ route('admin.candidates.create') }}" class="btn btn-or">
            Ajouter la première candidate
        </a>
        @endif
    </div>
@else
<div class="candidates-grid" id="grid">
    @foreach($shown as $c)
    <div class="c-card {{ !$c->is_active ? 'inactive' : '' }}" data-name="{{ strtolower($c->name) }}">

        <div class="c-card__num">N° {{ $c->number }}</div>
        <div class="c-card__badge">
            <span class="badge badge-{{ $c->is_active ? 'active' : 'inactive' }}">
                {{ $c->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        @if($c->photo_path)
            <img src="{{ $c->photo_url }}" alt="{{ $c->name }}" class="c-card__photo">
        @else
            <div class="c-card__placeholder">♛</div>
        @endif

        <div class="c-card__body">
            <div class="c-card__name">{{ $c->name }}</div>
            <div class="c-card__bio">
                {{ $c->bio ?: '—' }}
            </div>

            <div class="c-card__votes-row">
                <span class="n">{{ number_format($c->total_votes) }}</span>
                <span class="lbl">vote{{ $c->total_votes > 1 ? 's' : '' }}</span>
                <span class="pct">{{ round(($c->total_votes / $totalVotes) * 100, 1) }}%</span>
            </div>

            <div class="progress" style="margin-bottom:14px;">
                <div class="progress-bar" style="width:{{ round(($c->total_votes / $totalVotes) * 100, 1) }}%"></div>
            </div>

            <div class="c-card__actions">
                <a href="{{ route('admin.candidates.edit', $c) }}"
                   class="btn btn-outline btn-sm" style="flex:1;justify-content:center;">
                    Modifier
                </a>

                <form method="POST" action="{{ route('admin.candidates.toggle', $c) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-outline btn-sm"
                            title="{{ $c->is_active ? 'Désactiver' : 'Activer' }}">
                        {{ $c->is_active ? '⏸' : '▶' }}
                    </button>
                </form>

                @if($c->total_votes === 0)
                <form method="POST" action="{{ route('admin.candidates.destroy', $c) }}"
                      onsubmit="return confirm('Supprimer définitivement {{ addslashes($c->name) }} ? Cette action est irréversible.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" title="Supprimer">✕</button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection

@push('scripts')
<script>
document.getElementById('searchInput').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.c-card').forEach(card => {
        card.style.display = card.dataset.name.includes(q) ? '' : 'none';
    });
});
</script>
@endpush