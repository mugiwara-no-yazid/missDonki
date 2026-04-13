@extends('admin.layouts.app')
@section('title', 'Packs de vote')
@section('page-title', 'Packs de vote')
@section('breadcrumb', 'Configuration des offres d\'achat de votes')

@push('styles')
<style>
.pack-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
@media(max-width:800px){ .pack-grid { grid-template-columns: 1fr; } }

.pack-card {
    background: var(--noir2);
    border: 1px solid var(--gris1);
    border-radius: 14px;
    overflow: hidden;
    transition: border-color .2s;
    position: relative;
}
.pack-card.is-active { border-color: rgba(201,168,76,.3); }
.pack-card:hover { border-color: var(--gris2); }
.pack-card.inactive { opacity: .65; }

.pack-header {
    padding: 24px 20px;
    text-align: center;
    border-bottom: 1px solid var(--gris1);
    position: relative;
}
.pack-status {
    position: absolute; top: 12px; right: 12px;
}
.pack-price {
    font-family: 'Cormorant Garamond', serif;
    font-size: 48px; font-weight: 600; color: var(--or);
    line-height: 1;
}
.pack-unit {
    font-size: 12px; letter-spacing: 1.5px; text-transform: uppercase;
    color: var(--gris3); margin-top: 2px;
}
.pack-votes-count {
    margin-top: 14px; padding: 8px 16px;
    background: rgba(201,168,76,.08);
    border: 1px solid rgba(201,168,76,.2);
    border-radius: 8px; display: inline-block;
}
.pack-votes-count .n {
    font-family: 'Cormorant Garamond', serif;
    font-size: 24px; color: var(--or2);
}
.pack-votes-count .lbl { font-size: 12px; color: var(--gris3); }

.pack-body { padding: 20px; }
.pack-stats {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 10px; margin-bottom: 18px;
}
.pack-stat {
    background: var(--noir3);
    border: 1px solid var(--gris1);
    border-radius: 8px; padding: 10px 12px;
    text-align: center;
}
.pack-stat .val {
    font-family: 'Cormorant Garamond', serif;
    font-size: 20px; line-height: 1;
}
.pack-stat .lbl { font-size: 10px; color: var(--gris3); margin-top: 3px; letter-spacing:.5px; }

/* Modal d'édition inline via details/summary trick */
.edit-section {
    border-top: 1px solid var(--gris1);
    padding: 16px 20px 20px;
    background: var(--noir3);
}
.edit-toggle-btn {
    width: 100%;
    padding: 9px;
    background: transparent;
    border: 1px solid var(--gris2);
    color: var(--gris3);
    border-radius: 8px;
    font-size: 12.5px;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    transition: all .18s;
    margin-bottom: 10px;
}
.edit-toggle-btn:hover { border-color: var(--or); color: var(--or); }

.pack-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
</style>
@endpush

@section('content')

<p style="color:var(--gris3);font-size:13px;margin-bottom:24px;max-width:600px;">
    Les packs définissent les offres disponibles sur le site public. Les modifications s'appliquent immédiatement.
    Désactiver un pack le rend indisponible à l'achat sans supprimer l'historique des transactions associées.
</p>

{{-- Grille 3 packs --}}
<div class="pack-grid mb-6">
    @foreach($packs as $pack)
    @php
        $txOk   = $pack->payments()->where('status','success')->count();
        $revenue = $pack->payments()->where('status','success')->sum('amount');
    @endphp
    <div class="pack-card {{ $pack->is_active ? 'is-active' : 'inactive' }}">

        {{-- En-tête prix --}}
        <div class="pack-header">
            <div class="pack-status">
                <span class="badge badge-{{ $pack->is_active ? 'active' : 'inactive' }}">
                    {{ $pack->is_active ? 'Actif' : 'Inactif' }}
                </span>
            </div>

            <div style="font-size:12px;color:var(--gris3);margin-bottom:6px;font-weight:500;letter-spacing:.5px;">
                {{ strtoupper($pack->name) }}
            </div>
            <div class="pack-price">{{ number_format($pack->price_fcfa) }}</div>
            <div class="pack-unit">FCFA</div>

            <div class="pack-votes-count">
                <span class="n">{{ $pack->votes_count }}</span>
                <span class="lbl"> vote{{ $pack->votes_count > 1 ? 's' : '' }}</span>
            </div>
        </div>

        {{-- Stats utilisation --}}
        <div class="pack-body">
            <div style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--gris3);margin-bottom:10px;">
                Statistiques d'utilisation
            </div>
            <div class="pack-stats">
                <div class="pack-stat">
                    <div class="val" style="color:#5dbb7a;">{{ $txOk }}</div>
                    <div class="lbl">Transactions</div>
                </div>
                <div class="pack-stat">
                    <div class="val">{{ number_format($txOk * $pack->votes_count) }}</div>
                    <div class="lbl">Votes générés</div>
                </div>
                <div class="pack-stat" style="grid-column:1/-1;">
                    <div class="val" style="font-size:17px;">{{ number_format($revenue) }} FCFA</div>
                    <div class="lbl">Revenus totaux</div>
                </div>
            </div>

            {{-- Bouton ouvrir édition --}}
            <button class="edit-toggle-btn" onclick="toggleEdit('edit-{{ $pack->id }}', this)">
                ✎ Modifier ce pack
            </button>

            {{-- Formulaire modification (caché par défaut) --}}
            <div id="edit-{{ $pack->id }}" style="display:none;">
                <form method="POST" action="{{ route('admin.packs.update', $pack) }}">
                    @csrf @method('PUT')

                    <div class="form-group">
                        <label class="form-label">Nom du pack</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', $pack->name) }}" required>
                    </div>

                    <div class="pack-form-row">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Prix (FCFA)</label>
                            <input type="number" name="price_fcfa" class="form-control"
                                   value="{{ old('price_fcfa', $pack->price_fcfa) }}"
                                   min="1" required>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Votes offerts</label>
                            <input type="number" name="votes_count" class="form-control"
                                   value="{{ old('votes_count', $pack->votes_count) }}"
                                   min="1" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-or" style="width:100%;justify-content:center;margin-top:14px;">
                        Enregistrer
                    </button>
                </form>
            </div>

            {{-- Toggle actif/inactif --}}
            <form method="POST" action="{{ route('admin.packs.toggle', $pack) }}" style="margin-top:8px;">
                @csrf @method('PATCH')
                <button type="submit"
                        class="btn btn-{{ $pack->is_active ? 'danger' : 'outline' }}"
                        style="width:100%;justify-content:center;font-size:12px;">
                    {{ $pack->is_active ? '⏸ Désactiver' : '▶ Activer' }}
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>

{{-- Récap global --}}
<div class="card">
    <div class="card-title">Récapitulatif global</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Pack</th>
                    <th>Prix unitaire</th>
                    <th>Votes offerts</th>
                    <th>Ratio FCFA/vote</th>
                    <th>Transactions réussies</th>
                    <th>Votes générés</th>
                    <th>Revenus</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
            @foreach($packs as $pack)
            @php
                $txOk    = $pack->payments()->where('status','success')->count();
                $revenue = $pack->payments()->where('status','success')->sum('amount');
                $ratio   = round($pack->price_fcfa / $pack->votes_count);
            @endphp
            <tr>
                <td style="font-weight:500;color:var(--blanc);">{{ $pack->name }}</td>
                <td class="text-or" style="font-family:'Cormorant Garamond',serif;font-size:17px;">
                    {{ number_format($pack->price_fcfa) }} F
                </td>
                <td style="text-align:center;font-size:15px;">{{ $pack->votes_count }}</td>
                <td style="color:var(--gris3);font-size:12px;">{{ $ratio }} FCFA / vote</td>
                <td style="color:#5dbb7a;">{{ $txOk }}</td>
                <td>{{ number_format($txOk * $pack->votes_count) }}</td>
                <td style="font-family:'Cormorant Garamond',serif;font-size:17px;color:var(--or);">
                    {{ number_format($revenue) }} F
                </td>
                <td>
                    <span class="badge badge-{{ $pack->is_active ? 'active' : 'inactive' }}">
                        {{ $pack->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
function toggleEdit(id, btn) {
    const el = document.getElementById(id);
    const open = el.style.display !== 'none';
    el.style.display  = open ? 'none' : 'block';
    btn.textContent   = open ? '✎ Modifier ce pack' : '✕ Annuler';
    btn.style.color   = open ? '' : 'var(--or)';
    btn.style.borderColor = open ? '' : 'var(--or)';
}
</script>
@endpush