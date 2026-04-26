@extends('layouts.public')
@section('title', 'Candidates — Gala Tabaski Act 3')

@push('styles')
<style>
/* ── PAGE ── */
#candidates-page {
    position: relative; z-index: 1;
    padding: 120px 24px 100px;
}

/* ── CANDIDATES GRID ── */
.candidates-grid {
    max-width: 1100px; margin: 0 auto;
    display: grid; grid-template-columns: repeat(3,1fr); gap: 32px;
}
.candidate-card {
    position: relative;
    background: linear-gradient(180deg, rgba(20,5,5,.9) 0%, rgba(10,0,0,.95) 100%);
    border: 1px solid rgba(201,168,76,.2);
    border-radius: 12px; overflow: hidden;
    transition: all .4s cubic-bezier(.25,.8,.25,1); cursor: pointer;
}
.candidate-card:hover {
    transform: translateY(-8px);
    border-color: var(--or);
    box-shadow: 0 20px 60px rgba(192,0,26,.3), 0 0 0 1px rgba(201,168,76,.1);
}
.candidate-card.top-candidate {
    border-color: var(--or);
    box-shadow: 0 0 30px rgba(201,168,76,.2);
}
.top-badge {
    position: absolute; top: 16px; right: 16px; z-index: 10;
    background: linear-gradient(135deg, var(--or), var(--or-fonce));
    color: #000; font-size: .55rem; font-weight: 700; letter-spacing: 2px;
    padding: 5px 12px; border-radius: 20px; text-transform: uppercase;
}
.candidate-number {
    position: absolute; top: 16px; left: 16px; z-index: 10;
    width: 36px; height: 36px;
    background: rgba(0,0,0,.7); border: 1px solid rgba(201,168,76,.4);
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    font-family: 'Playfair Display', serif; font-size: .8rem; color: var(--or); font-weight: 700;
}
.candidate-photo {
    width: 100%; aspect-ratio: 3/4; object-fit: cover; display: block;
    filter: brightness(.9) saturate(1.1);
    transition: filter .4s, transform .4s;
}
.candidate-card:hover .candidate-photo { filter: brightness(1) saturate(1.3); transform: scale(1.03); }
.candidate-photo-placeholder {
    width: 100%; aspect-ratio: 3/4;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: 12px;
    background: linear-gradient(180deg, rgba(192,0,26,.15), rgba(10,0,0,.9));
    position: relative; overflow: hidden;
}
.candidate-photo-placeholder .emoji { font-size: 5rem; }
.candidate-photo-placeholder span {
    font-size: .7rem; color: rgba(201,168,76,.5); letter-spacing: 2px;
}
.candidate-info { padding: 24px; }
.candidate-name {
    font-family: 'Playfair Display', serif;
    font-size: 1.4rem; font-weight: 700; color: var(--blanc); margin-bottom: 4px;
}
.candidate-tag { font-size: .65rem; letter-spacing: 3px; color: var(--or); text-transform: uppercase; margin-bottom: 20px; }
.votes-bar-wrap { margin-bottom: 20px; }
.votes-label { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
.votes-label span { font-size: .65rem; letter-spacing: 2px; color: rgba(253,250,244,.5); text-transform: uppercase; }
.votes-count { font-family: 'Playfair Display', serif; font-size: 1.2rem; color: var(--or) !important; font-weight: 700; letter-spacing: 0 !important; text-transform: none !important; }
.votes-bar { width: 100%; height: 4px; background: rgba(255,255,255,.06); border-radius: 2px; overflow: hidden; }
.votes-bar-fill { height: 100%; background: linear-gradient(90deg, var(--rouge), var(--or)); border-radius: 2px; transition: width 1.5s cubic-bezier(.25,.8,.25,1); }
.btn-vote {
    width: 100%;
    background: linear-gradient(135deg, var(--rouge) 0%, var(--rouge-fonce) 100%);
    color: var(--blanc); border: 1px solid rgba(201,168,76,.2);
    padding: 14px; border-radius: 6px;
    font-size: .7rem; font-weight: 700; letter-spacing: 4px; text-transform: uppercase;
    cursor: pointer; transition: all .3s; font-family: 'Montserrat', sans-serif;
    position: relative; overflow: hidden;
}
.btn-vote::before {
    content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.1), transparent);
    transition: left .5s;
}
.btn-vote:hover::before { left: 100%; }
.btn-vote:hover { background: linear-gradient(135deg, var(--rouge-vif), var(--rouge)); box-shadow: 0 8px 30px rgba(192,0,26,.5); transform: translateY(-1px); }
.btn-vote-disabled { background: #222; border-color: #333; color: #555; cursor: default; }
.btn-vote-disabled:hover { transform: none; box-shadow: none; }

/* Votes fermés banner */
.votes-closed-banner {
    max-width: 600px; margin: 0 auto 40px;
    background: rgba(192,0,26,.08); border: 1px solid rgba(192,0,26,.25);
    border-radius: 8px; padding: 16px 24px;
    display: flex; align-items: center; gap: 12px;
    font-size: .8rem; color: rgba(253,250,244,.6);
}

/* ── MODAL DE VOTE ── */
.modal-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.85); backdrop-filter: blur(10px);
    z-index: 2000; display: none; align-items: center; justify-content: center; padding: 24px;
}
.modal-overlay.active { display: flex; }
.modal {
    background: linear-gradient(180deg, #120008 0%, #0a0005 100%);
    border: 1px solid rgba(201,168,76,.25); border-radius: 16px;
    padding: 48px 40px; max-width: 500px; width: 100%;
    position: relative; animation: modalIn .4s cubic-bezier(.25,.8,.25,1);
    box-shadow: 0 40px 100px rgba(0,0,0,.8), 0 0 0 1px rgba(201,168,76,.08);
    max-height: 90vh; overflow-y: auto;
}
@keyframes modalIn {
    from { opacity:0; transform: scale(.9) translateY(20px); }
    to   { opacity:1; transform: scale(1) translateY(0); }
}
.modal-close {
    position: absolute; top: 20px; right: 24px;
    background: none; border: none; color: rgba(253,250,244,.4);
    font-size: 1.5rem; cursor: pointer; transition: color .2s; line-height: 1;
    font-family: 'Montserrat', sans-serif;
}
.modal-close:hover { color: var(--rouge); }
.modal-header { text-align: center; margin-bottom: 32px; }
.modal-crown { font-size: 2.5rem; margin-bottom: 12px; display: block; }
.modal-title { font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; margin-bottom: 6px; }
.modal-candidate { font-size: .7rem; letter-spacing: 3px; color: var(--or); text-transform: uppercase; }

/* Packs dans modal */
.packs-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; margin-bottom: 28px; }
.pack {
    background: rgba(255,255,255,.03); border: 1px solid rgba(201,168,76,.15);
    border-radius: 10px; padding: 20px 12px; text-align: center;
    cursor: pointer; transition: all .3s; position: relative;
}
.pack:hover, .pack.selected { border-color: var(--or); background: rgba(201,168,76,.08); transform: translateY(-2px); }
.pack.selected::after { content: '✓'; position: absolute; top: 8px; right: 10px; color: var(--or); font-size: .75rem; font-weight: 700; }
.pack-votes { font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 700; color: var(--or); line-height: 1; margin-bottom: 4px; }
.pack-label { font-size: .55rem; letter-spacing: 2px; color: rgba(253,250,244,.4); text-transform: uppercase; margin-bottom: 12px; }
.pack-price { font-size: .75rem; font-weight: 700; color: var(--blanc); background: rgba(192,0,26,.2); border: 1px solid rgba(192,0,26,.3); padding: 5px 10px; border-radius: 20px; }
.pack-popular { position: absolute; top: -10px; left: 50%; transform: translateX(-50%); background: linear-gradient(90deg, var(--rouge), var(--rouge-fonce)); font-size: .5rem; letter-spacing: 2px; padding: 3px 10px; border-radius: 10px; color: #fff; text-transform: uppercase; font-weight: 700; white-space: nowrap; }

/* Formulaire téléphone */
.phone-section { margin-bottom: 24px; }
.phone-label { font-size: .65rem; letter-spacing: 2px; text-transform: uppercase; color: rgba(253,250,244,.5); margin-bottom: 12px; display: block; }
.operator-btns { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px; }
.op-btn {
    padding: 11px; background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.08); border-radius: 8px;
    cursor: pointer; transition: all .2s;
    font-size: .65rem; color: rgba(253,250,244,.6);
    font-family: 'Montserrat', sans-serif; font-weight: 600; letter-spacing: 1px;
    display: flex; align-items: center; justify-content: center; gap: 6px;
}
.op-btn:hover, .op-btn.selected { border-color: var(--or); color: var(--or); background: rgba(201,168,76,.06); }
.op-icon { font-size: 1.1rem; }
.phone-input {
    width: 100%; padding: 13px 16px;
    background: rgba(255,255,255,.04); border: 1px solid rgba(201,168,76,.2);
    border-radius: 8px; color: var(--blanc);
    font-family: 'Montserrat', sans-serif; font-size: .9rem;
    outline: none; transition: border-color .2s;
}
.phone-input:focus { border-color: var(--or); }
.phone-input::placeholder { color: #444; }

/* Résumé commande */
.order-recap {
    background: rgba(201,168,76,.05); border: 1px solid rgba(201,168,76,.15);
    border-radius: 10px; padding: 14px 16px; margin-bottom: 20px;
    font-size: .8rem;
}
.recap-row { display: flex; justify-content: space-between; padding: 4px 0; color: rgba(253,250,244,.6); }
.recap-row.total { border-top: 1px solid rgba(201,168,76,.15); margin-top: 8px; padding-top: 10px; font-weight: 700; color: var(--blanc); }
.recap-row.total .amt { font-family: 'Playfair Display', serif; font-size: 1.2rem; color: var(--or); }

.btn-pay {
    width: 100%;
    background: linear-gradient(135deg, var(--rouge) 0%, var(--rouge-fonce) 100%);
    color: var(--blanc); border: none; padding: 18px;
    border-radius: 8px; font-size: .75rem; font-weight: 700;
    letter-spacing: 4px; text-transform: uppercase; cursor: pointer;
    transition: all .3s; font-family: 'Montserrat', sans-serif;
    box-shadow: 0 8px 30px rgba(192,0,26,.4);
    display: flex; align-items: center; justify-content: center; gap: 10px;
}
.btn-pay:hover { box-shadow: 0 12px 40px rgba(192,0,26,.6); transform: translateY(-2px); }
.btn-pay:disabled { opacity: .5; cursor: default; transform: none; box-shadow: none; }
.spinner {
    display: none; width: 18px; height: 18px;
    border: 2px solid rgba(255,255,255,.2); border-top-color: #fff;
    border-radius: 50%; animation: spin .7s linear infinite;
}
.btn-pay.loading .spinner { display: block; }
@keyframes spin { to { transform: rotate(360deg); } }
.modal-note { text-align: center; margin-top: 14px; font-size: .62rem; color: rgba(253,250,244,.3); line-height: 1.7; }

/* ── SUCCESS OVERLAY ── */
.success-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,.95);
    z-index: 3000; display: none; flex-direction: column;
    align-items: center; justify-content: center; text-align: center; padding: 24px;
}
.success-overlay.active { display: flex; }
.success-icon { font-size: 5rem; margin-bottom: 24px; animation: bounceIn .6s cubic-bezier(.68,-.55,.265,1.55); }
@keyframes bounceIn { 0% { transform:scale(0); } 60% { transform:scale(1.2); } 100% { transform:scale(1); } }
.success-title { font-family: 'Playfair Display', serif; font-size: 2.5rem; color: var(--or); margin-bottom: 12px; }
.success-text { font-size: .9rem; color: rgba(253,250,244,.7); margin-bottom: 32px; line-height: 1.7; }
.btn-continue {
    background: linear-gradient(135deg, var(--rouge), var(--rouge-fonce));
    color: var(--blanc); border: none; padding: 16px 40px;
    border-radius: 6px; font-size: .7rem; font-weight: 700;
    letter-spacing: 3px; text-transform: uppercase; cursor: pointer;
    font-family: 'Montserrat', sans-serif; transition: all .3s;
}
.btn-continue:hover { box-shadow: 0 8px 30px rgba(192,0,26,.5); transform: translateY(-2px); }

@media(max-width:900px) { .candidates-grid { grid-template-columns: repeat(2,1fr); gap: 20px; } }
@media(max-width:580px) {
    .candidates-grid { grid-template-columns: 1fr; max-width: 400px; margin: 0 auto; }
    .modal { padding: 36px 20px; }
    .packs-grid { grid-template-columns: repeat(3,1fr); gap: 8px; }
    .pack-votes { font-size: 1.4rem; }
}
</style>
@endpush

@section('content')

<section id="candidates-page">
    <p class="section-label">Nos Candidates</p>
    <h2 class="section-title">
        Votez pour Votre<br>
        <em style="color:var(--or)">Favorite</em>
    </h2>
    <div class="section-divider">
        <div class="divider-line"></div>
        <span class="divider-gem">✦</span>
        <div class="divider-line right"></div>
    </div>

    @if(!$votingOpen)
    <div class="votes-closed-banner">
        <span style="font-size:1.2rem;">⏸</span>
        <span>Les votes sont actuellement fermés. Revenez bientôt pour soutenir votre candidate.</span>
    </div>
    @endif

    @if($candidates->isEmpty())
    <div style="text-align:center;padding:80px 0;color:rgba(253,250,244,.3);">
        <div style="font-size:3rem;margin-bottom:16px;">♛</div>
        <div style="font-family:'Playfair Display',serif;font-size:1.2rem;">Les candidates seront bientôt annoncées</div>
    </div>
    @else
    <div class="candidates-grid" id="candidates-grid">
        @php $maxVotes = $candidates->max('total_votes') ?: 1; @endphp
        @foreach($candidates as $c)
        @php $pct = round(($c->total_votes / $maxVotes) * 100); $isTop = $c->total_votes == $maxVotes && $c->total_votes > 0; @endphp
        <div class="candidate-card {{ $isTop ? 'top-candidate' : '' }}"
             onclick="{{ $votingOpen ? "openVote({$c->id}, '{$c->name}', '{$c->photo_url}')" : '' }}">
            @if($isTop)<div class="top-badge">⭐ Top Candidate</div>@endif
            <div class="candidate-number">{{ $c->number }}</div>

            @if($c->photo_path)
                <img src="{{ $c->photo_url }}" alt="{{ $c->name }}" class="candidate-photo" loading="lazy">
            @else
                <div class="candidate-photo-placeholder">
                    <span class="emoji">♛</span>
                    <span>Candidate N°{{ $c->number }}</span>
                </div>
            @endif

            <div class="candidate-info">
                <div class="candidate-name">{{ $c->name }}</div>
                <div class="candidate-tag">Candidate N°{{ $c->number }}</div>
                @if($showVotes)
                <div class="votes-bar-wrap">
                    <div class="votes-label">
                        <span>Votes</span>
                        <span class="votes-count">{{ number_format($c->total_votes) }}</span>
                    </div>
                    <div class="votes-bar">
                        <div class="votes-bar-fill" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
                @endif
                @if($votingOpen)
                <button class="btn-vote" onclick="event.stopPropagation(); openVote({{ $c->id }}, '{{ addslashes($c->name) }}', '{{ $c->photo_url }}')">
                    👑 Voter pour {{ explode(' ', $c->name)[0] }}
                </button>
                @else
                <div class="btn-vote btn-vote-disabled">Vote fermé</div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</section>

{{-- MODAL DE VOTE --}}
<div class="modal-overlay" id="payment-modal">
    <div class="modal">
        <button class="modal-close" onclick="closeModal()">×</button>

        <div class="modal-header">
            <span class="modal-crown">👑</span>
            <h2 class="modal-title">Voter pour</h2>
            <p class="modal-candidate" id="modal-candidate-name">—</p>
        </div>

        <div class="packs-grid" id="packs-grid">
            @foreach($packs as $pack)
            <div class="pack {{ $loop->index === 1 ? 'selected' : '' }}"
                 data-votes="{{ $pack->votes_count }}"
                 data-price="{{ $pack->price_fcfa }}"
                 data-pack-id="{{ $pack->id }}"
                 data-pack-name="{{ $pack->name }}"
                 onclick="selectPack(this)">
                @if($loop->index === 1)<div class="pack-popular">Populaire</div>@endif
                <div class="pack-votes">{{ $pack->votes_count }}</div>
                <div class="pack-label">vote{{ $pack->votes_count > 1 ? 's' : '' }}</div>
                <div class="pack-price">{{ number_format($pack->price_fcfa) }} FCFA</div>
            </div>
            @endforeach
        </div>

        <div class="phone-section">
            <span class="phone-label">Opérateur Mobile Money</span>
            <div class="operator-btns">
                <button class="op-btn selected" data-op="mtn" onclick="selectOp(this)">
                    <span class="op-icon">🟡</span> MTN MoMo
                </button>
                <button class="op-btn" data-op="moov" onclick="selectOp(this)">
                    <span class="op-icon">🔵</span> Moov Money
                </button>
            </div>
            <span class="phone-label">Numéro Mobile Money</span>
            <input type="tel" class="phone-input" id="phone-input"
                   placeholder="Ex: 97000000" maxlength="15" inputmode="numeric">
        </div>

        <div class="order-recap" id="order-recap">
            <div class="recap-row"><span>Candidate</span><span id="recap-candidate">—</span></div>
            <div class="recap-row"><span>Pack</span><span id="recap-pack">—</span></div>
            <div class="recap-row"><span>Votes</span><span id="recap-votes">—</span></div>
            <div class="recap-row total">
                <span>Total</span>
                <span class="amt" id="recap-amount">— FCFA</span>
            </div>
        </div>

        <button class="btn-pay" id="btn-pay" onclick="processPayment()">
            <span class="spinner" id="spinner"></span>
            <span id="btn-text">🔒 Payer & Voter — <span id="pay-amount">500 FCFA</span></span>
        </button>

        <p class="modal-note">
            Paiement sécurisé via FeeXPay · Vote validé après confirmation<br>
            Vous recevrez une demande de paiement sur votre téléphone
        </p>
    </div>
</div>

{{-- SUCCESS OVERLAY --}}
<div class="success-overlay" id="success-overlay">
    <div class="success-icon">🎉</div>
    <h2 class="success-title">Vote Enregistré !</h2>
    <p class="success-text" id="success-text">
        Merci pour votre soutien !<br>Votre vote a bien été comptabilisé.
    </p>
    <button class="btn-continue" onclick="closeSuccess()">Continuer</button>
</div>

@endsection

@push('scripts')
<script>
const PACKS  = @json($packs->values());
const CSRF   = document.querySelector('meta[name="csrf-token"]').content;

let selectedCandidateId   = null;
let selectedCandidateName = '';
let selectedPackId        = null;
let selectedPackPrice     = 0;
let selectedPackVotes     = 0;
let selectedPackName      = '';
let selectedOp            = 'mtn';

// Init : sélectionner le pack du milieu par défaut
window.addEventListener('DOMContentLoaded', () => {
    const defaultPack = document.querySelector('.packs-grid .pack.selected');
    if (defaultPack) refreshPackState(defaultPack);
});

function openVote(id, name, photo) {
    selectedCandidateId   = id;
    selectedCandidateName = name;
    document.getElementById('modal-candidate-name').textContent = name;
    document.getElementById('recap-candidate').textContent = name;
    updateRecap();
    document.getElementById('payment-modal').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    document.getElementById('payment-modal').classList.remove('active');
    document.body.style.overflow = '';
}
document.getElementById('payment-modal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

function selectPack(el) {
    document.querySelectorAll('.packs-grid .pack').forEach(p => p.classList.remove('selected'));
    el.classList.add('selected');
    refreshPackState(el);
}
function refreshPackState(el) {
    selectedPackId    = el.dataset.packId;
    selectedPackPrice = parseInt(el.dataset.price);
    selectedPackVotes = parseInt(el.dataset.votes);
    selectedPackName  = el.dataset.packName;
    document.getElementById('pay-amount').textContent = selectedPackPrice.toLocaleString('fr-FR') + ' FCFA';
    updateRecap();
}
function selectOp(el) {
    document.querySelectorAll('.op-btn').forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
    selectedOp = el.dataset.op;
}
function updateRecap() {
    document.getElementById('recap-pack').textContent   = selectedPackName || '—';
    document.getElementById('recap-votes').textContent  = selectedPackVotes ? selectedPackVotes + ' vote(s)' : '—';
    document.getElementById('recap-amount').textContent = selectedPackPrice ? selectedPackPrice.toLocaleString('fr-FR') + ' FCFA' : '—';
}

async function processPayment() {
    const phone = document.getElementById('phone-input').value.trim();
    if (!phone || phone.length < 8) {
        document.getElementById('phone-input').style.borderColor = 'var(--rouge)';
        document.getElementById('phone-input').focus();
        return;
    }
    document.getElementById('phone-input').style.borderColor = '';

    const btn = document.getElementById('btn-pay');
    btn.disabled = true;
    btn.classList.add('loading');

    try {
        const res = await fetch('{{ route('vote.process') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({
                candidate_id: selectedCandidateId,
                pack_id:      selectedPackId,
                phone_number: phone,
                operator:     selectedOp,
            }),
        });
        const data = await res.json();

        closeModal();
        btn.disabled = false;
        btn.classList.remove('loading');
        console.log(data)
        if (data.success) {
            showSuccess(selectedCandidateName, selectedPackVotes);
            spawnConfetti();
        } else {
            alert('❌ ' + (data.message || 'Erreur lors du paiement. Veuillez réessayer.'));
        }
    } catch (e) {
        btn.disabled = false;
        btn.classList.remove('loading');
        alert('❌ Erreur réseau. Veuillez réessayer.');
    }
}

function showSuccess(name, votes) {
    document.getElementById('success-text').innerHTML =
        `Merci pour votre soutien à <strong style="color:var(--or)">${name}</strong> !<br>
         <strong>${votes} vote${votes > 1 ? 's' : ''}</strong> seront ajoutés après confirmation du paiement.`;
    document.getElementById('success-overlay').classList.add('active');
}
function closeSuccess() {
    document.getElementById('success-overlay').classList.remove('active');
}

updateWhatsApp(
    '{{ $candidates->sortByDesc('total_votes')->first()?->name ?? '—' }}',
    {{ $candidates->sortByDesc('total_votes')->first()?->total_votes ?? 0 }}
);
</script>
@endpush
