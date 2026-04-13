@extends('admin.layouts.app')
@section('title', 'Paiements')
@section('page-title', 'Paiements')
@section('breadcrumb', 'Historique et suivi des transactions Mobile Money')

@push('styles')
<style>
.filters-bar {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 160px 160px auto;
    gap: 12px;
    align-items: end;
}
@media(max-width:1100px){ .filters-bar { grid-template-columns: 1fr 1fr 1fr; } }
@media(max-width:700px)  { .filters-bar { grid-template-columns: 1fr 1fr; } }

.status-pill {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 11px; border-radius: 20px;
    font-size: 12px; font-weight: 500;
}
.status-pill::before {
    content:''; width:6px; height:6px; border-radius:50%; flex-shrink:0;
}
.status-pill.success { background:rgba(39,174,96,.12); color:#5dbb7a; border:1px solid rgba(39,174,96,.25); }
.status-pill.success::before { background:#5dbb7a; }
.status-pill.pending { background:rgba(201,168,76,.12); color:var(--or); border:1px solid rgba(201,168,76,.25); }
.status-pill.pending::before { background:var(--or); }
.status-pill.failed  { background:rgba(192,57,43,.12); color:#e05a4b; border:1px solid rgba(192,57,43,.25); }
.status-pill.failed::before  { background:#e05a4b; }

.kpi-row { display:grid; grid-template-columns:repeat(5,1fr); gap:12px; }
@media(max-width:900px){ .kpi-row { grid-template-columns:repeat(3,1fr); } }

.no-results-row td {
    padding: 60px 20px !important;
    text-align: center;
    color: var(--gris3) !important;
}
</style>
@endpush

@section('content')

{{-- KPIs --}}
<div class="kpi-row mb-6">
    <div class="kpi">
        <div class="label">Total</div>
        <div class="value">{{ $stats['total'] }}</div>
        <div class="sub">transactions</div>
    </div>
    <div class="kpi">
        <div class="label">Réussies</div>
        <div class="value" style="color:#5dbb7a;">{{ $stats['success'] }}</div>
        <div class="sub">confirmées</div>
    </div>
    <div class="kpi">
        <div class="label">En attente</div>
        <div class="value" style="color:var(--or);">{{ $stats['pending'] }}</div>
        <div class="sub">en cours</div>
    </div>
    <div class="kpi">
        <div class="label">Échouées</div>
        <div class="value" style="color:#e05a4b;">{{ $stats['failed'] }}</div>
        <div class="sub">échecs</div>
    </div>
    <div class="kpi">
        <div class="label">Revenus</div>
        <div class="value" style="font-size:24px;">{{ number_format($stats['revenue']) }}</div>
        <div class="sub">FCFA encaissés</div>
    </div>
</div>

{{-- Filtres --}}
<div class="card mb-6">
    <div class="card-title" style="margin-bottom:14px;">Filtres</div>
    <form method="GET" action="{{ route('admin.payments.index') }}">
        <div class="filters-bar">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Statut</label>
                <select name="status" class="form-control">
                    <option value="">Tous les statuts</option>
                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>✓ Réussi</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>⏳ En attente</option>
                    <option value="failed"  {{ request('status') === 'failed'  ? 'selected' : '' }}>✕ Échoué</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Opérateur</label>
                <select name="operator" class="form-control">
                    <option value="">Tous</option>
                    <option value="mtn"  {{ request('operator') === 'mtn'  ? 'selected' : '' }}>MTN Mobile Money</option>
                    <option value="moov" {{ request('operator') === 'moov' ? 'selected' : '' }}>Moov Money</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Candidate</label>
                <select name="candidate_id" class="form-control">
                    <option value="">Toutes les candidates</option>
                    @foreach($candidates as $c)
                        <option value="{{ $c->id }}" {{ request('candidate_id') == $c->id ? 'selected' : '' }}>
                            N°{{ $c->number }} — {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Du</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Au</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>

            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-or">Filtrer</button>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline" title="Réinitialiser">✕</a>
            </div>
        </div>
    </form>
</div>

{{-- Tableau --}}
<div class="card">
    <div class="card-title">
        <span>
            {{ $payments->total() }} transaction(s)
            @if(request()->hasAny(['status','operator','candidate_id','date_from','date_to']))
                <span style="font-size:12px;color:var(--gris3);font-family:'DM Sans',sans-serif;font-weight:400;">
                    (filtrées)
                </span>
            @endif
        </span>
        <a href="{{ route('admin.payments.export', request()->query()) }}"
           class="btn btn-outline btn-sm">
            ↓ Export CSV
        </a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Candidate</th>
                    <th>Pack</th>
                    <th>Téléphone</th>
                    <th>Opérateur</th>
                    <th>Montant</th>
                    <th>Votes</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Réf. transaction</th>
                </tr>
            </thead>
            <tbody>
            @forelse($payments as $p)
                <tr>
                    <td style="color:var(--gris3);font-size:11px;">#{{ $p->id }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:9px;">
                            <img src="{{ $p->candidate->photo_url ?? '' }}"
                                 style="width:30px;height:30px;border-radius:50%;object-fit:cover;border:1px solid var(--gris1);">
                            <div>
                                <div style="color:var(--blanc);font-size:12.5px;white-space:nowrap;">
                                    {{ $p->candidate->name ?? '—' }}
                                </div>
                                <div style="color:var(--gris3);font-size:11px;">
                                    N°{{ $p->candidate->number ?? '—' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:12px;color:var(--gris3);white-space:nowrap;">
                        {{ $p->pack->name ?? '—' }}
                    </td>
                    <td style="font-size:13px;color:var(--blanc);">{{ $p->phone_number }}</td>
                    <td>
                        <span style="font-size:12.5px;font-weight:500;
                                     color:{{ $p->operator === 'mtn' ? '#f0b429' : '#00b0cc' }}">
                            {{ strtoupper($p->operator) }}
                        </span>
                    </td>
                    <td>
                        <span style="font-family:'Cormorant Garamond',serif;font-size:18px;color:var(--or);">
                            {{ number_format($p->amount) }}
                        </span>
                        <span style="font-size:11px;color:var(--gris3);"> F</span>
                    </td>
                    <td style="text-align:center;">
                        <span style="font-family:'Cormorant Garamond',serif;font-size:17px;color:var(--blanc);">
                            {{ $p->votes_count }}
                        </span>
                    </td>
                    <td>
                        <span class="status-pill {{ $p->status }}">{{ $p->status_label }}</span>
                    </td>
                    <td style="color:var(--gris3);font-size:12px;white-space:nowrap;">
                        {{ $p->created_at->format('d/m/Y') }}
                        <span style="display:block;font-size:11px;">{{ $p->created_at->format('H:i:s') }}</span>
                    </td>
                    <td>
                        <span style="font-size:11px;color:var(--gris3);
                                     display:block;max-width:130px;overflow:hidden;
                                     text-overflow:ellipsis;white-space:nowrap;"
                              title="{{ $p->transaction_ref ?? '' }}">
                            {{ $p->transaction_ref ?? '—' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr class="no-results-row">
                    <td colspan="10">
                        <div style="font-size:32px;margin-bottom:10px;opacity:.3;">◈</div>
                        Aucune transaction trouvée avec ces critères.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($payments->hasPages())
    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;padding-top:16px;border-top:1px solid var(--gris1);">
        <div style="font-size:12px;color:var(--gris3);">
            Page {{ $payments->currentPage() }} sur {{ $payments->lastPage() }}
            — {{ $payments->total() }} transaction(s)
        </div>
        {{ $payments->links('admin.partials.pagination') }}
    </div>
    @endif
</div>

@endsection