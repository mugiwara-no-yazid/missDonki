@extends('admin.layouts.app')
@section('title', 'Paramètres')
@section('page-title', 'Paramètres')
@section('breadcrumb', 'Configuration générale du site public')

@push('styles')
<style>
.settings-layout { display: grid; grid-template-columns: 1fr 320px; gap: 24px; align-items: start; }
@media(max-width:960px){ .settings-layout { grid-template-columns: 1fr; } }

.toggle-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 20px;
    background: var(--noir3);
    border: 1px solid var(--gris1);
    border-radius: 10px;
    transition: border-color .2s;
}
.toggle-row:hover { border-color: var(--gris2); }
.toggle-row.on { border-color: rgba(201,168,76,.3); }

.toggle-row__info .title {
    font-size: 14px; font-weight: 500; color: var(--blanc);
}
.toggle-row__info .desc {
    font-size: 12px; color: var(--gris3); margin-top: 4px; line-height: 1.5;
}

/* Preview message --*/
.msg-preview {
    background: var(--noir3);
    border-left: 3px solid var(--or);
    border-radius: 0 8px 8px 0;
    padding: 12px 16px;
    font-size: 13px; color: var(--gris3); line-height: 1.6;
    margin-top: 8px;
    font-style: italic;
    transition: opacity .2s;
}

/* Sidebar info --*/
.side-info { background: var(--noir2); border: 1px solid var(--gris1); border-radius: 12px; overflow: hidden; }
.side-info__header {
    padding: 14px 18px;
    background: var(--noir3);
    border-bottom: 1px solid var(--gris1);
    font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--gris3); font-weight: 500;
}
.side-info__item {
    display: flex; gap: 10px; align-items: flex-start;
    padding: 12px 18px;
    border-bottom: 1px solid rgba(255,255,255,.03);
    font-size: 13px;
}
.side-info__item:last-child { border-bottom: none; }
.side-info__item .key { color: var(--gris3); min-width: 80px; font-size: 12px; }
.side-info__item .val { color: var(--blanc); flex: 1; }

.section-label {
    font-size: 11px; text-transform: uppercase; letter-spacing: 1px;
    color: var(--gris3); font-weight: 500; margin-bottom: 12px;
}
</style>
@endpush

@section('content')

<div class="settings-layout">

    {{-- Colonne principale --}}
    <div>
        <form method="POST" action="{{ route('admin.settings.update') }}" id="settingsForm">
            @csrf @method('PUT')

            {{-- Section 1 : Contrôles temps réel --}}
            <div class="card mb-6">
                <div class="card-title">Contrôles en temps réel</div>
                <p style="font-size:12.5px;color:var(--gris3);margin-bottom:16px;">
                    Ces paramètres prennent effet immédiatement sur le site public sans rechargement.
                </p>

                <div style="display:flex;flex-direction:column;gap:12px;">
                    {{-- Toggle votes --}}
                    <div class="toggle-row {{ $settings['voting_open'] === 'true' ? 'on' : '' }}"
                         id="voteToggleRow">
                        <div class="toggle-row__info">
                            <div class="title">Votes ouverts</div>
                            <div class="desc">
                                Autorise le public à voter via le site.
                                Si désactivé, le bouton "Voter" est masqué.
                            </div>
                        </div>
                        <label class="toggle-label" style="flex-shrink:0;">
                            <input type="checkbox" name="voting_open" value="1"
                                   id="voteToggle"
                                   {{ $settings['voting_open'] === 'true' ? 'checked' : '' }}
                                   onchange="syncRow('voteToggleRow', this)">
                            <span class="toggle-track"></span>
                        </label>
                    </div>

                    {{-- Toggle résultats --}}
                    <div class="toggle-row {{ $settings['show_results'] === 'true' ? 'on' : '' }}"
                         id="resultsToggleRow">
                        <div class="toggle-row__info">
                            <div class="title">Afficher les résultats publiquement</div>
                            <div class="desc">
                                Rend le classement visible sur le site.
                                À désactiver avant le gala pour maintenir le suspense.
                            </div>
                        </div>
                        <label class="toggle-label" style="flex-shrink:0;">
                            <input type="checkbox" name="show_results" value="1"
                                   id="resultsToggle"
                                   {{ $settings['show_results'] === 'true' ? 'checked' : '' }}
                                   onchange="syncRow('resultsToggleRow', this)">
                            <span class="toggle-track"></span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Section 2 : Informations événement --}}
            <div class="card mb-6">
                <div class="card-title">Informations de l'événement</div>

                <div class="form-group">
                    <label class="form-label">Nom de l'événement <span style="color:var(--or)">*</span></label>
                    <input type="text" name="event_name" class="form-control"
                           value="{{ old('event_name', $settings['event_name']) }}" required>
                    @error('event_name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="grid-2" style="gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Date de l'événement <span style="color:var(--or)">*</span></label>
                        <input type="date" name="event_date" class="form-control"
                               value="{{ old('event_date', $settings['event_date']) }}" required>
                        @error('event_date')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Lieu <span style="color:var(--or)">*</span></label>
                        <input type="text" name="event_location" class="form-control"
                               value="{{ old('event_location', $settings['event_location']) }}"
                               placeholder="Ex: Salle des fêtes Jéricho" required>
                        @error('event_location')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Organisateur <span style="color:var(--or)">*</span></label>
                    <input type="text" name="organizer" class="form-control"
                           value="{{ old('organizer', $settings['organizer']) }}" required>
                    @error('organizer')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Section 3 : Message transparence --}}
            <div class="card mb-6">
                <div class="card-title">Message de transparence</div>
                <p style="font-size:12.5px;color:var(--gris3);margin-bottom:14px;">
                    Ce message est affiché en bas de chaque page de vote pour informer les votants
                    de l'utilisation des fonds collectés.
                </p>

                <div class="form-group">
                    <textarea name="transparency_message"
                              class="form-control"
                              id="msgArea"
                              rows="3"
                              maxlength="500"
                              required>{{ old('transparency_message', $settings['transparency_message']) }}</textarea>
                    <div class="form-hint flex justify-between">
                        <span>Max 500 caractères</span>
                        <span id="msgCount">{{ strlen($settings['transparency_message'] ?? '') }} / 500</span>
                    </div>
                    @error('transparency_message')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div style="font-size:11px;color:var(--gris3);margin-bottom:6px;">Aperçu :</div>
                <div class="msg-preview" id="msgPreview">
                    {{ $settings['transparency_message'] ?? '' }}
                </div>
            </div>

            <button type="submit" class="btn btn-or"
                    style="width:100%;justify-content:center;padding:13px;font-size:14px;">
                Sauvegarder tous les paramètres
            </button>
        </form>
    </div>

    {{-- Colonne droite --}}
    <div>
        {{-- Aperçu config actuelle --}}
        <div class="side-info mb-6">
            <div class="side-info__header">Configuration actuelle</div>

            <div class="side-info__item">
                <span class="key">Votes</span>
                <span class="val">
                    @if($settings['voting_open'] === 'true')
                        <span style="color:#5dbb7a;">● Ouverts</span>
                    @else
                        <span style="color:#e05a4b;">○ Fermés</span>
                    @endif
                </span>
            </div>
            <div class="side-info__item">
                <span class="key">Résultats</span>
                <span class="val">
                    @if($settings['show_results'] === 'true')
                        <span style="color:#5dbb7a;">● Publics</span>
                    @else
                        <span style="color:var(--or);">○ Masqués</span>
                    @endif
                </span>
            </div>
            <div class="side-info__item">
                <span class="key">Événement</span>
                <span class="val">{{ $settings['event_name'] }}</span>
            </div>
            <div class="side-info__item">
                <span class="key">Date</span>
                <span class="val">
                    {{ $settings['event_date']
                        ? \Carbon\Carbon::parse($settings['event_date'])->locale('fr')->isoFormat('D MMMM YYYY')
                        : '—' }}
                </span>
            </div>
            <div class="side-info__item">
                <span class="key">Lieu</span>
                <span class="val">{{ $settings['event_location'] }}</span>
            </div>
        </div>

        {{-- Raccourcis --}}
        <div class="side-info">
            <div class="side-info__header">Raccourcis</div>
            <div class="side-info__item" style="padding:8px 18px;">
                <a href="{{ route('admin.candidates.index') }}" class="btn btn-outline btn-sm"
                   style="width:100%;justify-content:center;">♛ Gérer les candidates</a>
            </div>
            <div class="side-info__item" style="padding:8px 18px;border-bottom:none;">
                <a href="{{ route('admin.results.index') }}" class="btn btn-outline btn-sm"
                   style="width:100%;justify-content:center;">★ Voir les résultats</a>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
// Sync bord de la carte toggle
function syncRow(rowId, checkbox) {
    document.getElementById(rowId).classList.toggle('on', checkbox.checked);
}

// Compteur + aperçu message transparence
const msgArea    = document.getElementById('msgArea');
const msgPreview = document.getElementById('msgPreview');
const msgCount   = document.getElementById('msgCount');

msgArea.addEventListener('input', function () {
    msgPreview.textContent = this.value;
    msgCount.textContent   = this.value.length + ' / 500';
});
</script>
@endpush