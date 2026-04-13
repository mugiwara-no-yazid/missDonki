@extends('admin.layouts.app')
@section('title', 'Nouvelle candidate')
@section('page-title', 'Ajouter une candidate')
@section('breadcrumb', 'Candidates → Nouvelle candidate')

@push('styles')
<style>
.form-page { display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start; }
@media(max-width:900px){ .form-page { grid-template-columns: 1fr; } }

.photo-upload-zone {
    border: 2px dashed var(--gris2);
    border-radius: 14px;
    padding: 32px 20px;
    text-align: center;
    cursor: pointer;
    transition: border-color .2s, background .2s;
    position: relative;
    overflow: hidden;
}
.photo-upload-zone:hover {
    border-color: var(--or);
    background: rgba(201,168,76,.04);
}
.photo-upload-zone input[type=file] {
    position: absolute; inset: 0; opacity: 0; cursor: pointer;
}
.photo-upload-zone .upload-icon { font-size: 36px; margin-bottom: 10px; color: var(--gris3); }
.photo-upload-zone .upload-label { font-size: 13px; color: var(--gris3); }
.photo-upload-zone .upload-hint { font-size: 11px; color: var(--gris2); margin-top: 6px; }

.photo-preview-wrap {
    position: relative;
    display: inline-block;
    margin-top: 16px;
}
.photo-preview-wrap img {
    width: 140px; height: 140px;
    object-fit: cover; object-position: top;
    border-radius: 10px;
    border: 2px solid var(--or);
    display: block;
}
.photo-preview-wrap .remove-btn {
    position: absolute; top: -8px; right: -8px;
    width: 24px; height: 24px;
    background: #c0392b; color: #fff;
    border: none; border-radius: 50%;
    font-size: 13px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    line-height: 1;
}

.info-card {
    background: var(--noir3);
    border: 1px solid var(--gris1);
    border-radius: 12px;
    padding: 20px;
}
.info-card .info-title {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--gris3);
    margin-bottom: 14px;
    font-weight: 500;
}
.info-item {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid var(--gris1);
    font-size: 12.5px;
    color: var(--gris3);
    line-height: 1.5;
}
.info-item:last-child { border-bottom: none; }
.info-item .dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--or); flex-shrink: 0; margin-top: 5px;
}
</style>
@endpush

@section('content')

<div class="form-page">

    {{-- Colonne principale --}}
    <div>
        <div class="card">
            <div class="card-title">Informations de la candidate</div>

            <form method="POST" action="{{ route('admin.candidates.store') }}"
                  enctype="multipart/form-data" id="candidateForm">
                @csrf

                {{-- Ligne nom + numéro --}}
                <div class="grid-2" style="gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Nom complet <span style="color:var(--or)">*</span></label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name') }}"
                               placeholder="Ex : Fatoumata Diallo"
                               autofocus required>
                        @error('name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Numéro de candidate <span style="color:var(--or)">*</span></label>
                        <input type="number" name="number" class="form-control"
                               value="{{ old('number') }}"
                               placeholder="Ex : 1"
                               min="1" max="99" required>
                        <div class="form-hint">Numéro unique affiché sur la carte publique</div>
                        @error('number')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Photo --}}
                <div class="form-group">
                    <label class="form-label">Photo</label>
                    <div class="photo-upload-zone" id="uploadZone">
                        <input type="file" name="photo" id="photoInput"
                               accept="image/jpeg,image/png,image/webp">
                        <div id="uploadPrompt">
                            <div class="upload-icon">⬆</div>
                            <div class="upload-label">Cliquez ou glissez une photo ici</div>
                            <div class="upload-hint">JPEG, PNG ou WebP — max 2 Mo</div>
                        </div>
                        <div id="previewWrap" class="photo-preview-wrap" style="display:none;">
                            <img id="photoPreview" src="" alt="Aperçu">
                            <button type="button" class="remove-btn" onclick="removePhoto(event)">✕</button>
                        </div>
                    </div>
                    @error('photo')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Biographie --}}
                <div class="form-group">
                    <label class="form-label">Biographie <span style="color:var(--gris3);font-size:11px;">(optionnel)</span></label>
                    <textarea name="bio" class="form-control"
                              placeholder="Courte présentation de la candidate, sa ville, ses passions…"
                              rows="4" maxlength="500">{{ old('bio') }}</textarea>
                    <div class="form-hint flex justify-between">
                        <span>Max 500 caractères</span>
                        <span id="bioCount">0 / 500</span>
                    </div>
                    @error('bio')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex gap-3 mt-4">
                    <button type="submit" class="btn btn-or">
                        Enregistrer la candidate
                    </button>
                    <a href="{{ route('admin.candidates.index') }}" class="btn btn-outline">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Colonne droite : conseils --}}
    <div>
        <div class="info-card">
            <div class="info-title">Conseils de saisie</div>
            <div class="info-item">
                <span class="dot"></span>
                <span>Le <strong style="color:var(--blanc);">numéro</strong> doit être unique et correspond au numéro officiel du concours.</span>
            </div>
            <div class="info-item">
                <span class="dot"></span>
                <span>Utilisez une photo <strong style="color:var(--blanc);">portrait de bonne qualité</strong> (format vertical recommandé).</span>
            </div>
            <div class="info-item">
                <span class="dot"></span>
                <span>La biographie est <strong style="color:var(--blanc);">affichée publiquement</strong> sur la page des candidates.</span>
            </div>
            <div class="info-item">
                <span class="dot"></span>
                <span>Une candidate est <strong style="color:var(--blanc);">active</strong> par défaut et apparaîtra immédiatement sur le site.</span>
            </div>
        </div>

        <div class="info-card" style="margin-top:16px;">
            <div class="info-title">Candidates enregistrées</div>
            @php $existing = \App\Models\Candidate::orderBy('number')->get(); @endphp
            @forelse($existing as $ec)
            <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--gris1);">
                <span style="font-family:'Cormorant Garamond',serif;color:var(--or);min-width:26px;font-size:15px;">{{ $ec->number }}</span>
                <img src="{{ $ec->photo_url }}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;border:1px solid var(--gris1);">
                <span style="font-size:13px;color:var(--blanc);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $ec->name }}</span>
                <span class="badge badge-{{ $ec->is_active ? 'active' : 'inactive' }}" style="font-size:10px;padding:2px 7px;">
                    {{ $ec->is_active ? '●' : '○' }}
                </span>
            </div>
            @empty
            <div style="font-size:12px;color:var(--gris3);padding:8px 0;">Aucune candidate pour l'instant.</div>
            @endforelse
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
// Compteur biographie
const bioArea = document.querySelector('[name="bio"]');
const bioCount = document.getElementById('bioCount');
if (bioArea) {
    bioArea.addEventListener('input', () => {
        bioCount.textContent = bioArea.value.length + ' / 500';
    });
}

// Preview photo
document.getElementById('photoInput').addEventListener('change', function () {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('photoPreview').src = e.target.result;
            document.getElementById('uploadPrompt').style.display = 'none';
            document.getElementById('previewWrap').style.display = 'inline-block';
        };
        reader.readAsDataURL(this.files[0]);
    }
});

function removePhoto(e) {
    e.stopPropagation();
    document.getElementById('photoInput').value = '';
    document.getElementById('uploadPrompt').style.display = 'block';
    document.getElementById('previewWrap').style.display = 'none';
}

// Drag & drop
const zone = document.getElementById('uploadZone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.borderColor = 'var(--or)'; });
zone.addEventListener('dragleave', () => { zone.style.borderColor = ''; });
zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.style.borderColor = '';
    const input = document.getElementById('photoInput');
    input.files = e.dataTransfer.files;
    input.dispatchEvent(new Event('change'));
});
</script>
@endpush