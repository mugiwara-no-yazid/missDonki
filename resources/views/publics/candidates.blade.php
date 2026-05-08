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
.candidate-tag { font-size: .65rem; letter-spacing: 3px; color: var(--or); text-transform: uppercase; margin-bottom: 12px; }
.candidate-bio {
    font-size: .78rem; color: rgba(253,250,244,.55); line-height: 1.6;
    margin-bottom: 6px;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    overflow: hidden;
    transition: all .3s ease;
}
.candidate-bio.expanded {
    -webkit-line-clamp: unset; display: block;
}
.bio-toggle {
    background: none; border: none; cursor: pointer;
    font-size: .68rem; color: var(--or); font-family: 'Montserrat', sans-serif;
    font-weight: 600; letter-spacing: 1px; padding: 0; margin-bottom: 16px;
    transition: color .2s; display: inline-block;
}
.bio-toggle:hover { color: var(--or-clair); }

/* ── SHARE BUTTON ── */
.share-wrap {
    position: absolute; top: 12px; right: 12px; z-index: 10;
}
.btn-share {
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(0,0,0,.5); backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,.15);
    color: var(--blanc); font-size: .95rem;
    cursor: pointer; transition: all .25s;
    display: flex; align-items: center; justify-content: center;
}
.btn-share:hover { background: rgba(255,255,255,.15); transform: scale(1.1); }
.share-menu {
    display: none; position: absolute; top: 44px; right: 0;
    background: rgba(15,15,15,.95); backdrop-filter: blur(16px);
    border: 1px solid rgba(201,168,76,.2); border-radius: 10px;
    padding: 6px 0; min-width: 180px;
    box-shadow: 0 12px 40px rgba(0,0,0,.6);
    animation: shareIn .2s ease forwards;
}
.share-menu.active { display: block; }
.share-menu a, .share-menu button {
    display: flex; align-items: center; gap: 10px;
    width: 100%; padding: 10px 16px; border: none;
    background: none; color: var(--blanc); font-size: .78rem;
    font-family: 'Montserrat', sans-serif; cursor: pointer;
    text-decoration: none; transition: background .15s;
}
.share-menu a:hover, .share-menu button:hover { background: rgba(201,168,76,.1); }
.share-menu .share-icon { font-size: 1.1rem; width: 22px; text-align: center; }
@keyframes shareIn { from { opacity:0; transform: translateY(-6px); } to { opacity:1; transform: translateY(0); } }
.candidate-votes-count {
    display: flex; align-items: baseline; gap: 6px;
    margin-bottom: 18px;
}
.candidate-votes-count .vote-number {
    font-family: 'Playfair Display', serif;
    font-size: 1.6rem; font-weight: 700; color: var(--or);
}
.candidate-votes-count .vote-text {
    font-size: .7rem; color: rgba(253,250,244,.4);
    letter-spacing: 2px; text-transform: uppercase;
}
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
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        spawnConfetti();
    });
</script>
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
        <div class="candidate-card {{ $isTop ? 'top-candidate' : '' }}" id="candidate-{{ $c->id }}"
             onclick="{{ $votingOpen ? "openVote({$c->id}, '{$c->name}', '{$c->photo_url}')" : '' }}">
            @if($isTop)<div class="top-badge">⭐ Top Candidate</div>@endif
            <div class="share-wrap">
                <button class="btn-share" onclick="event.stopPropagation(); toggleShare({{ $c->id }})" title="Partager"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg></button>
                <div class="share-menu" id="share-menu-{{ $c->id }}">
                    <a href="#" onclick="event.preventDefault(); shareWhatsApp({{ $c->id }}, '{{ addslashes($c->name) }}')">
                        <span class="share-icon">💬</span> WhatsApp
                    </a>
                    <a href="#" onclick="event.preventDefault(); shareFacebook({{ $c->id }})">
                        <span class="share-icon">📘</span> Facebook
                    </a>
                    <button onclick="copyLink({{ $c->id }}, '{{ addslashes($c->name) }}')">
                        <span class="share-icon">🔗</span> Copier le lien
                    </button>
                </div>
            </div>
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
                @if($c->bio)
                <p class="candidate-bio" id="bio-{{ $c->id }}">{{ $c->bio }}</p>
                <button class="bio-toggle" onclick="event.stopPropagation(); toggleBio({{ $c->id }}, this)">Lire plus ▾</button>
                @endif
                <div class="candidate-votes-count">
                    <span class="vote-number">{{ number_format($c->total_votes) }}</span>
                    <span class="vote-text">vote{{ $c->total_votes > 1 ? 's' : '' }}</span>
                </div>
                @if($votingOpen)
                <button class="btn-vote" onclick="event.stopPropagation(); openVote({{ $c->id }}, '{{ addslashes($c->name) }}', '{{ $c->photo_url }}')" 
                style="display: flex; justify-content:center">
                   <span><img src="{{ asset('storage/icons/queen.png') }}" alt="queen"  style="max-width: 40px;"></span> <span>Voter pour {{ explode(' ', $c->name)[0] }}</span>
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
<span><img src="{{ asset('storage/icons/queen.png') }}" alt="Calendrier"  style="max-width: 50px;"></span>
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
            <span id="btn-text">🔒 Voter — <span id="pay-amount">500 FCFA</span></span>
        </button>

        <p class="modal-note">
            Paiement sécurisé via <strong>FedaPay</strong><br>
            Le vote sera comptabilisé une fois le paiement confirmé.
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
let lastPaymentId = null;
const PACKS  = @json($packs->values());
const CSRF   = document.querySelector('meta[name="csrf-token"]').content;

let selectedCandidateId   = null;
let selectedCandidateName = '';
let selectedPackId        = null;
let selectedPackPrice     = 0;
let selectedPackVotes     = 0;
let selectedPackName      = '';

// --- AUTO-SCROLL si lien partagé (#candidate-X) ---
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash) {
        const target = document.querySelector(window.location.hash);
        if (target && target.classList.contains('candidate-card')) {
            setTimeout(() => {
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                target.style.boxShadow = '0 0 30px rgba(201,168,76,.5)';
                target.style.borderColor = 'var(--or)';
                setTimeout(() => { target.style.boxShadow = ''; target.style.borderColor = ''; }, 2500);
            }, 300);
        }
    }
});

// --- BIO TOGGLE ---
function toggleBio(id, btn) {
    const bio = document.getElementById('bio-' + id);
    bio.classList.toggle('expanded');
    btn.textContent = bio.classList.contains('expanded') ? 'Réduire ▴' : 'Lire plus ▾';
}

// --- SHARE ---
function toggleShare(id) {
    // Fermer tous les autres menus
    document.querySelectorAll('.share-menu.active').forEach(m => {
        if (m.id !== 'share-menu-' + id) m.classList.remove('active');
    });
    document.getElementById('share-menu-' + id).classList.toggle('active');
}

function getCandidateUrl(id) {
    return window.location.origin + '/candidates#candidate-' + id;
}

function shareWhatsApp(id, name) {
    const url = getCandidateUrl(id);
    const text = `👑 Votez pour ${name} au Gala Tabaski Act 3 !\n\nChaque vote compte pour la couronne de Miss Populaire ✨\n\n${url}`;
    window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank');
    toggleShare(id);
}

function shareFacebook(id) {
    const url = getCandidateUrl(id);
    window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url), '_blank', 'width=600,height=400');
    toggleShare(id);
}

function copyLink(id, name) {
    const url = getCandidateUrl(id);
    navigator.clipboard.writeText(url).then(() => {
        showToast('Lien de ' + name + ' copié !', 'success', 3000);
    }).catch(() => {
        showToast('Impossible de copier le lien', 'error');
    });
    toggleShare(id);
}

// Fermer les menus au clic extérieur
document.addEventListener('click', function(e) {
    if (!e.target.closest('.share-wrap')) {
        document.querySelectorAll('.share-menu.active').forEach(m => m.classList.remove('active'));
    }
});

// --- FONCTIONS DE GESTION DU MODAL ---

function openVote(id, name, photo) {
    selectedCandidateId = id;
    selectedCandidateName = name;
    
    document.getElementById('modal-candidate-name').innerText = name;
    document.getElementById('recap-candidate').innerText = name;
    document.getElementById('payment-modal').classList.add('active');

    // Sélectionner le pack par défaut (le 2ème / Populaire)
    const defaultPack = document.querySelector('.pack.selected') || document.querySelector('.pack');
    if (defaultPack) selectPack(defaultPack);
}

function closeModal() {
    document.getElementById('payment-modal').classList.remove('active');
}

function selectPack(element) {
    // UI : Switcher la classe selected
    document.querySelectorAll('.pack').forEach(p => p.classList.remove('selected'));
    element.classList.add('selected');

    // Data : Récupérer les infos du pack
    selectedPackId    = element.dataset.packId;
    selectedPackPrice = parseInt(element.dataset.price);
    selectedPackVotes = element.dataset.votes;
    selectedPackName  = element.dataset.packName;

    updateRecap();
}

function updateRecap() {
    document.getElementById('recap-pack').innerText = selectedPackName;
    document.getElementById('recap-votes').innerText = selectedPackVotes + (selectedPackVotes > 1 ? ' votes' : ' vote');
    
    const formattedPrice = new Intl.NumberFormat('fr-FR').format(selectedPackPrice) + ' FCFA';
    document.getElementById('recap-amount').innerText = formattedPrice;
    document.getElementById('pay-amount').innerText = formattedPrice;
}

async function processPayment() {
    if (!selectedCandidateId || !selectedPackId) return;

    const btn = document.getElementById('btn-pay');
    btn.disabled = true;
    btn.classList.add('loading');

    try {
        const res = await fetch("{{ route('vote.process') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({
                candidate_id: selectedCandidateId,
                pack_id:      selectedPackId,
            }),
        });
        
        const data = await res.json();

        if (data.success) {
            lastPaymentId = data.local_ref;
            // Redirection vers le checkout FedaPay
            window.location.href = data.token_url;
        } else {
            showToast(data.message || 'Erreur lors de l\'initialisation.', 'error');
            btn.disabled = false;
            btn.classList.remove('loading');
        }
    } catch (e) {
        console.error(e);
        showToast('Erreur réseau. Veuillez réessayer.', 'error');
        btn.disabled = false;
        btn.classList.remove('loading');
    }
}

function showSuccess(name, votes) {
    document.getElementById('success-text').innerHTML =
        `Merci pour votre soutien à <strong style="color:var(--or)">${name}</strong> !<br>
         <strong>${votes} vote${votes > 1 ? 's' : ''}</strong> ont été ajoutés.`;
    document.getElementById('success-overlay').classList.add('active');
    spawnConfetti();
}

function closeSuccess() {
    document.getElementById('success-overlay').classList.remove('active');
    location.reload();
}
</script>
@endpush