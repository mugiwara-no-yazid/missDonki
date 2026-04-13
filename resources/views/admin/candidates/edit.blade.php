@extends('admin.layouts.app')
@section('title', 'Modifier — ' . $candidate->name)
@section('page-title', 'Modifier la candidate')
@section('breadcrumb', 'Candidates → ' . $candidate->name)

@push('styles')
<style>
.edit-page { display: grid; grid-template-columns: 1fr 300px; gap: 24px; align-items: start; }
@media(max-width:900px){ .edit-page { grid-template-columns: 1fr; } }

.photo-change-row {
    display: flex; align-items: center; gap: 20px;
    padding: 16px;
    background: var(--noir3);
    border: 1px solid var(--gris1);
    border-radius: 10px;
    margin-bottom: 6px;
}
.photo-change-row img {
    width: 80px; height: 80px;
    object-fit: cover; object-position: top;
    border-radius: 8px; border: 2px solid var(--or);
    flex-shrink: 0; transition: opacity .2s;
}
.stat-mini {
    background: var(--noir3);
    border: 1px solid var(--gris1);
    border-radius: 10px;
    padding: 14px 16px;
    display: flex; align-items: center; gap: 12px;
}
.stat-mini .ico { font-size: 20px; opacity: .6; }
.stat-mini .val {
    font-family: 'Cormorant Garamond', serif;
    font-size: 24px; color: var(--or); line-height: 1;
}
.stat-mini .lbl { font-size: 11px; color: var(--gris3); }

.recent-list { max-height: 220px; overflow-y: auto; }
.recent-item {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 0; border-bottom: 1px solid var(--gris1);
    font-size: 12.5px; color: var(--gris3);
}
.recent-item:last-child { border-bottom: none; }
</style>
@endpush

@section('content')

<div class="edit-page">

    {{-- Formulaire --}}
    <div>
        <div class="card">
            <div class="card-title">
                <span>Modifier N°{{ $candidate->number }} — {{ $candidate->name }}</span>
                <span class="badge badge-{{ $candidate->is_active ? 'active' : 'inactive' }}">
                    {{ $candidate->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <form method="POST" action="{{ route('admin.candidates.update', $candidate) }}"
                  enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="grid-2" style="gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Nom complet <span style="color:var(--or)">*</span></label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', $candidate->name) }}" required>
                        @error('name')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Numéro <span style="color:var(--or)">*</span></label>
                        <input type="number" name="number" class="form-control"
                               value="{{ old('number', $candidate->number) }}" min="1" max="99" required>
                        @error('number')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Changement photo --}}
                <div class="form-group">
                    <label class="form-label">Photo</label>
                    <div class="photo-change-row">
                        <img id="photoPreview" src="{{ $candidate->photo_url }}" alt="{{ $candidate->name }}">
                        <div style="flex:1;">
                            <input type="file" name="photo" class="form-control"
                                   accept="image/jpeg,image/png,image/webp"
                                   onchange="previewChange(this)">
                            <div class="form-hint" style="margin-top:6px;">
                                Laisser vide pour conserver la photo actuelle
                            </div>
                        </div>
                    </div>
                    @error('photo')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- Biographie --}}
                <div class="form-group">
                    <label class="form-label">Biographie</label>
                    <textarea name="bio" class="form-control" rows="4" maxlength="500"
                              id="bioArea">{{ old('bio', $candidate->bio) }}</textarea>
                    <div class="form-hint flex justify-between">
                        <span>Max 500 caractères</span>
                        <span id="bioCount">{{ strlen($candidate->bio ?? '') }} / 500</span>
                    </div>
                    @error('bio')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="flex gap-3 mt-4">
                    <button type="submit" class="btn btn-or">Enregistrer les modifications</button>
                    <a href="{{ route('admin.candidates.index') }}" class="btn btn-outline">Retour</a>
                </div>
            </form>
        </div>

        {{-- Toggle actif / inactif --}}
        <div class="card mt-6" style="display:flex;align-items:center;justify-content:space-between;padding:18px 24px;">
            <div>
                <div style="font-weight:500;color:var(--blanc);">
                    {{ $candidate->is_active ? 'Candidate active' : 'Candidate inactive' }}
                </div>
                <div style="font-size:12px;color:var(--gris3);margin-top:3px;">
                    {{ $candidate->is_active
                        ? 'Elle est visible et votable sur le site public.'
                        : 'Elle est masquée du site public.' }}
                </div>
            </div>
            <form method="POST" action="{{ route('admin.candidates.toggle', $candidate) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="btn {{ $candidate->is_active ? 'btn-danger' : 'btn-or' }}">
                    {{ $candidate->is_active ? 'Désactiver' : 'Activer' }}
                </button>
            </form>
        </div>

        {{-- Suppression --}}
        @if($candidate->total_votes === 0)
        <div class="card mt-6" style="border-color:rgba(192,57,43,.25);padding:18px 24px;">
            <div class="card-title" style="color:#e05a4b;margin-bottom:10px;">Zone de danger</div>
            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;">
                <div style="font-size:13px;color:var(--gris3);">
                    Cette candidate n'a aucun vote. La suppression est définitive et irréversible.
                </div>
                <form method="POST" action="{{ route('admin.candidates.destroy', $candidate) }}"
                      onsubmit="return confirm('Supprimer définitivement {{ addslashes($candidate->name) }} ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="white-space:nowrap;">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    {{-- Colonne droite : stats --}}
    <div>
        <div class="card">
            <div class="card-title">Statistiques</div>

            <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:20px;">
                <div class="stat-mini">
                    <span class="ico">◉</span>
                    <div>
                        <div class="val">{{ number_format($candidate->total_votes) }}</div>
                        <div class="lbl">Votes totaux</div>
                    </div>
                </div>
                <div class="stat-mini">
                    <span class="ico">✓</span>
                    <div>
                        <div class="val" style="color:#5dbb7a;">
                            {{ $candidate->payments()->where('status','success')->count() }}
                        </div>
                        <div class="lbl">Paiements réussis</div>
                    </div>
                </div>
                <div class="stat-mini">
                    <span class="ico">◈</span>
                    <div>
                        <div class="val">
                            {{ number_format($candidate->payments()->where('status','success')->sum('amount')) }}
                        </div>
                        <div class="lbl">FCFA générés</div>
                    </div>
                </div>
                <div class="stat-mini">
                    <span class="ico">⏳</span>
                    <div>
                        <div class="val" style="color:var(--or);font-size:20px;">
                            {{ $candidate->payments()->where('status','pending')->count() }}
                        </div>
                        <div class="lbl">En attente</div>
                    </div>
                </div>
            </div>

            {{-- Derniers votants --}}
            <div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;color:var(--gris3);margin-bottom:10px;">
                Derniers votes
            </div>
            <div class="recent-list">
                @forelse($candidate->payments()->where('status','success')->latest()->take(8)->get() as $p)
                <div class="recent-item">
                    <span style="color:{{ $p->operator === 'mtn' ? '#f0b429' : '#00b0cc' }};font-size:11px;font-weight:500;">
                        {{ strtoupper($p->operator) }}
                    </span>
                    <span style="flex:1;">{{ $p->phone_number }}</span>
                    <span style="color:var(--or);">+{{ $p->votes_count }}v</span>
                    <span style="font-size:11px;">{{ $p->created_at->format('d/m H:i') }}</span>
                </div>
                @empty
                <div style="font-size:12px;color:var(--gris3);padding:10px 0;">Aucun vote reçu.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
function previewChange(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('photoPreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
document.getElementById('bioArea').addEventListener('input', function () {
    document.getElementById('bioCount').textContent = this.value.length + ' / 500';
});
</script>
@endpush