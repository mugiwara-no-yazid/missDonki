@extends('layouts.public')
@section('title', 'Gala Tabaski Act 3 — Vote Miss Populaire')

@push('styles')
<style>
/* ── HERO ── */
#hero {
    position: relative;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 120px 24px 80px;
    overflow: hidden;
    z-index: 1;
}
.hero-bg {
    position: absolute; inset: 0;
    background:
        radial-gradient(ellipse 80% 60% at 50% 40%, rgba(192,0,26,.18) 0%, transparent 70%),
        radial-gradient(ellipse 60% 80% at 20% 80%, rgba(201,168,76,.08) 0%, transparent 60%),
        radial-gradient(ellipse 50% 50% at 80% 20%, rgba(192,0,26,.10) 0%, transparent 60%),
        linear-gradient(180deg, #050505 0%, #0d0105 50%, #050505 100%);
    z-index: -1;
}
.hero-ornament {
    position: absolute;
    width: 600px; height: 600px;
    border: 1px solid rgba(201,168,76,.07);
    border-radius: 50%;
    top: 50%; left: 50%;
    transform: translate(-50%,-50%);
    animation: orbit 20s linear infinite;
}
.hero-ornament::before {
    content: '';
    position: absolute;
    width: 800px; height: 800px;
    border: 1px solid rgba(192,0,26,.05);
    border-radius: 50%;
    top: 50%; left: 50%;
    transform: translate(-50%,-50%);
}
@keyframes orbit {
    from { transform: translate(-50%,-50%) rotate(0deg); }
    to   { transform: translate(-50%,-50%) rotate(360deg); }
}

.hero-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(201,168,76,.1);
    border: 1px solid rgba(201,168,76,.3);
    padding: 8px 20px; border-radius: 30px;
    font-size: .65rem; letter-spacing: 4px; text-transform: uppercase;
    color: var(--or); margin-bottom: 28px;
    animation: fadeUp .8s ease forwards; opacity: 0;
}
.hero-badge::before, .hero-badge::after { content: '✦'; font-size: .7rem; }

.hero-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(3rem, 10vw, 7rem);
    font-weight: 900; line-height: .95;
    letter-spacing: -1px; margin-bottom: 12px;
    animation: fadeUp .8s ease .2s forwards; opacity: 0;
}
.hero-title .line1 { color: var(--blanc); display: block; }
.hero-title .line2 { color: transparent; -webkit-text-stroke: 2px var(--or); display: block; font-style: italic; }
.hero-title .line3 {
    padding-bottom: 5px;
    display: block;
    background: linear-gradient(90deg, var(--rouge), var(--or), var(--rouge-vif));
    background-clip: text; -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-size: 200% auto;
    animation: shimmer 3s linear infinite, fadeUp .8s ease .4s forwards;
    opacity: 0;
}
@keyframes shimmer { from { background-position: 0% center; } to { background-position: 200% center; } }

.hero-subtitle {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.1rem; color: rgba(253,250,244,.6);
    letter-spacing: 3px; text-transform: uppercase;
    margin-bottom: 16px;
    animation: fadeUp .8s ease .5s forwards; opacity: 0;
}
.hero-live {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: .7rem; letter-spacing: 3px; color: var(--or-clair);
    margin-bottom: 48px;
    animation: fadeUp .8s ease .6s forwards; opacity: 0;
}
.pulse-dot {
    width: 8px; height: 8px; background: #00e87a;
    border-radius: 50%; animation: pulse 1.5s infinite;
}
@keyframes pulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(0,232,122,.5); }
    50%      { box-shadow: 0 0 0 6px rgba(0,232,122,0); }
}
.hero-closed {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: .7rem; letter-spacing: 3px; color: rgba(253,250,244,.4);
    margin-bottom: 48px;
    animation: fadeUp .8s ease .6s forwards; opacity: 0;
}

.btn-hero {
    display: inline-flex; align-items: center; gap: 12px;
    background: linear-gradient(135deg, var(--rouge) 0%, var(--rouge-fonce) 100%);
    color: var(--blanc);
    padding: 18px 48px; border-radius: 4px;
    font-size: .75rem; font-weight: 700; letter-spacing: 4px; text-transform: uppercase;
    text-decoration: none;
    border: 1px solid rgba(201,168,76,.3); cursor: pointer; transition: all .3s;
    box-shadow: 0 0 40px rgba(192,0,26,.4), inset 0 1px 0 rgba(255,255,255,.1);
    animation: fadeUp .8s ease .7s forwards; opacity: 0;
    position: relative; overflow: hidden;
}
.btn-hero::before {
    content: '';
    position: absolute; top: 0; left: -100%; width: 100%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.1), transparent);
    transition: left .5s;
}
.btn-hero:hover::before { left: 100%; }
.btn-hero:hover { transform: translateY(-2px); box-shadow: 0 8px 60px rgba(192,0,26,.6), inset 0 1px 0 rgba(255,255,255,.2); }

.hero-scroll {
    position: absolute; bottom: 32px;
    display: flex; flex-direction: column; align-items: center; gap: 8px;
    font-size: .55rem; letter-spacing: 3px; color: rgba(201,168,76,.5);
    animation: fadeUp 1s ease 1.2s forwards; opacity: 0;
}
.scroll-line {
    width: 1px; height: 40px;
    background: linear-gradient(180deg, var(--or), transparent);
    animation: scrollLine 2s ease infinite;
}
@keyframes scrollLine {
    0%    { transform: scaleY(0); transform-origin: top; }
    50%   { transform: scaleY(1); transform-origin: top; }
    50.1% { transform: scaleY(1); transform-origin: bottom; }
    100%  { transform: scaleY(0); transform-origin: bottom; }
}
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(30px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── ABOUT ── */
#about {
    position: relative; z-index: 1;
    padding: 100px 24px;
    background: linear-gradient(180deg, transparent, rgba(192,0,26,.04) 50%, transparent);
}
.about-grid {
    max-width: 900px; margin: 0 auto;
    display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px;
}
.about-card {
    background: linear-gradient(135deg, rgba(255,255,255,.03) 0%, rgba(201,168,76,.03) 100%);
    border: 1px solid rgba(201,168,76,.15);
    border-radius: 8px; padding: 32px 24px; text-align: center;
    transition: all .4s;
}
.about-card:hover {
    border-color: rgba(201,168,76,.4);
    background: linear-gradient(135deg, rgba(201,168,76,.05) 0%, rgba(192,0,26,.05) 100%);
    transform: translateY(-4px);
}
.about-icon { font-size: 2rem; margin-bottom: 16px; display: block; }
.about-card h3 { font-family: 'Playfair Display', serif; font-size: 1.1rem; color: var(--or); margin-bottom: 10px; }
.about-card p { font-size: .8rem; color: rgba(253,250,244,.6); line-height: 1.7; }

/* ── COUNTDOWN ── */
.countdown-wrap {
    display: flex; justify-content: center; gap: 32px;
    margin: 40px auto; max-width: 600px;
}
.cd-unit { text-align: center; }
.cd-num {
    margin-bottom: 15px;
    font-family: 'Playfair Display', serif;
    font-size: clamp(36px, 8vw, 60px); font-weight: 900;
    color: var(--blanc); line-height: 1;
    display: block;
}
.cd-label { font-size: .6rem; letter-spacing: 3px; text-transform: uppercase; color: rgba(201,168,76,.6); margin-top: 4px; display: block; }
.cd-sep {
    font-family: 'Playfair Display', serif;
    font-size: 50px; color: rgba(201,168,76,.3);
    align-self: center; padding-bottom: 14px;
}

/* ── PACKS PREVIEW ── */
.packs-preview {
    max-width: 560px; margin: 0 auto 60px;
    display: grid; grid-template-columns: repeat(3,1fr); gap: 16px;
    text-align: center;
}
.pack-prev {
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(201,168,76,.15);
    border-radius: 10px; padding: 20px 12px;
    transition: all .3s;
}
.pack-prev:hover { border-color: rgba(201,168,76,.4); transform: translateY(-3px); }
.pack-prev .price {
    font-family: 'Playfair Display', serif;
    font-size: 2rem; font-weight: 700; color: var(--or); line-height: 1;
}
.pack-prev .currency { font-size: .6rem; letter-spacing: 2px; color: rgba(253,250,244,.4); margin-top: 2px; }
.pack-prev .votes { font-size: .8rem; color: var(--blanc); margin-top: 10px; font-weight: 600; }
.pack-prev .votes strong { color: var(--or-clair); }

/* ── INFO BAND ── */
.info-band {
    border-top: 1px solid rgba(201,168,76,.1);
    border-bottom: 1px solid rgba(201,168,76,.1);
    padding: 24px 0; z-index: 1; position: relative;
    background: rgba(0,0,0,.3);
}
.info-band-inner {
    display: flex; justify-content: center; flex-wrap: wrap;
    max-width: 900px; margin: 0 auto;
}
.info-item {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 40px;
    border-right: 1px solid rgba(201,168,76,.1);
}
.info-item:last-child { border-right: none; }
.info-icon { font-size: 1.4rem; opacity: .8; }
.info-text .lbl { font-size: .55rem; letter-spacing: 3px; text-transform: uppercase; color: rgba(201,168,76,.6); }
.info-text .val { font-size: .9rem; font-weight: 600; color: var(--blanc); margin-top: 3px; }

@media(max-width:768px) {
    .about-grid { grid-template-columns: 1fr; }
    .info-item { padding: 10px 20px; }
    .countdown-wrap { gap: 16px; }
    .cd-sep { font-size: 36px; }
}
</style>
@endpush

@section('content')

{{-- HERO --}}
<section id="hero">
    <div class="hero-bg"></div>
    <div class="hero-ornament"></div>
    <h1 class="hero-title">
        <span class="line1">Gala</span>
        <span class="line2">Tabaski</span>
        <span class="line3">Act 3</span>
    </h1>

    <p class="hero-subtitle">Vote Miss Populaire</p>

    @if($votingOpen)
    <div class="hero-live">
        <div class="pulse-dot"></div>
        <span>VOTE OUVERT EN DIRECT</span>
    </div>
    <a href="{{ route('candidates') }}" class="btn-hero">
        <span><img src="" alt=""></span><span>Voter Maintenant</span>
    </a>
    @else
    <div class="hero-closed">
        <span>⏸</span> <span>VOTES TEMPORAIREMENT FERMÉS</span>
    </div>
    <a href="{{ route('candidates') }}" class="btn-hero" style="background:linear-gradient(135deg,#333,#222);">
        <span>♛</span><span>Voir les Candidates</span>
    </a>
    @endif

</section>

{{-- BANDE INFO --}}
<div class="info-band" style="background-color: #333333b0;">
    <div class="info-band-inner" >
        <div class="info-item">
           <img src="{{ asset('storage/icons/calendar.png') }}" alt="Calendrier"  style="max-width: 30px;">
            <div class="info-text">
                <div class="lbl">Date</div>
                <div class="val">{{ \Carbon\Carbon::parse($eventDate)->locale('fr')->isoFormat('D MMMM YYYY') }}</div>
            </div>
        </div>
        <div class="info-item">
            <span class="info-icon"> <img src="{{ asset('storage/icons/location.png') }}" alt="Calendrier"  style="max-width: 30px;"></span>
            <div class="info-text">
                <div class="lbl">Lieu</div>
                <div class="val">{{ $eventLocation }}</div>
            </div>
        </div>
        <div class="info-item">
            <span class="info-icon"><img src="{{ asset('storage/icons/queen.png') }}" alt="Calendrier"  style="max-width: 30px;"></span>
            <div class="info-text">
                <div class="lbl">Candidates</div>
                <div class="val">{{ $candidatesCount }} participantes</div>
            </div>
        </div>
        <div class="info-item">
            <span class="info-icon">◉</span>
            <div class="info-text">
                <div class="lbl">Votes enregistrés</div>
                <div class="val">{{ number_format($totalVotes) }}</div>
            </div>
        </div>
    </div>
</div>

{{-- COMPTE À REBOURS --}}
<section id="about">
    <p class="section-label">Compte à rebours</p>
    <div class="countdown-wrap" id="countdown">
        <div class="cd-unit"><span class="cd-num" id="cd-j">--</span><span class="cd-label">Jours</span></div>
        <span class="cd-sep">:</span>
        <div class="cd-unit"><span class="cd-num" id="cd-h">--</span><span class="cd-label">Heures</span></div>
        <span class="cd-sep">:</span>
        <div class="cd-unit"><span class="cd-num" id="cd-m">--</span><span class="cd-label">Minutes</span></div>
        <span class="cd-sep">:</span>
        <div class="cd-unit"><span class="cd-num" id="cd-s">--</span><span class="cd-label">Secondes</span></div>
    </div>

    <hr style="border:none;border-top:1px solid rgba(201,168,76,.1);max-width:200px;margin:40px auto 60px;">

    <p class="section-label">Le Concours</p>
    <h2 class="section-title">
        Une Célébration de la<br>
        <em style="color:var(--or)">Beauté & de l'Élégance</em>
    </h2>
    <div class="section-divider">
        <div class="divider-line"></div>
        <span class="divider-gem">✦</span>
        <div class="divider-line right"></div>
    </div>

    <div class="about-grid">
        <div class="about-card">
            <span class="about-icon"><img src="{{ asset('storage/icons/queen.png') }}" alt="Calendrier"  style="max-width: 50px;"></span>
            <h3>Miss Populaire</h3>
            <p>Le titre est décerné à la candidate ayant reçu le plus de votes du public. Chaque voix compte !</p>
        </div>
        <div class="about-card">
            <span class="about-icon"><img src="{{ asset('storage/icons/collaboration.png') }}" alt="Calendrier"  style="max-width: 50px;"></span>
            <h3>Organisé par</h3>
            <p>L'Association des Guinéens au Bénin, fière de célébrer la Tabaski avec culture, style et solidarité.</p>
        </div>
        <div class="about-card">
            <span class="about-icon"><img src="{{ asset('storage/icons/festivity.png') }}" alt="Calendrier"  style="max-width: 50px;"></span>
            <h3>Grand Gala</h3>
            <p>Une soirée inoubliable mêlant beauté, tradition et modernité. La 3ème édition promet d'être grandiose.</p>
        </div>
    </div>

    <hr style="border:none;border-top:1px solid rgba(201,168,76,.08);max-width:400px;margin:60px auto 50px;">

    <p class="section-label">Packs de vote</p>
    <h2 class="section-title" style="margin-bottom:40px;">
        Votez à partir de<br><em style="color:var(--or)">100 FCFA</em>
    </h2>
    <div class="packs-preview">
        @foreach($packs as $pack)
        <div class="pack-prev">
            <div class="price">{{ number_format($pack->price_fcfa) }}</div>
            <div class="currency">FCFA</div>
            <div class="votes">= <strong>{{ $pack->votes_count }}</strong> vote{{ $pack->votes_count > 1 ? 's' : '' }}</div>
        </div>
        @endforeach
    </div>

    @if($votingOpen)
    <div style="text-align:center;">
        <a href="{{ route('candidates') }}" class="btn-hero" style="animation:none;opacity:1;display:inline-flex;">
            <span><img src="{{ asset('storage/icons/queen.png') }}" alt="Calendrier"  style="max-width: 50px;"></span><span>Voir les Candidates</span>
        </a>
    </div>
    @endif
</section>

@endsection

@push('scripts')
<script>
const eventDate = new Date('{{ $eventDate }}T20:00:00');
function updateCountdown() {
    const diff = eventDate - new Date();
    if (diff <= 0) {
        document.getElementById('countdown').innerHTML = '<div style="font-family:\'Playfair Display\',serif;font-size:2rem;color:var(--or);text-align:center;">✦ C\'est ce soir ! ✦</div>';
        return;
    }
    const j = Math.floor(diff/86400000);
    const h = Math.floor((diff%86400000)/3600000);
    const m = Math.floor((diff%3600000)/60000);
    const s = Math.floor((diff%60000)/1000);
    document.getElementById('cd-j').textContent = String(j).padStart(2,'0');
    document.getElementById('cd-h').textContent = String(h).padStart(2,'0');
    document.getElementById('cd-m').textContent = String(m).padStart(2,'0');
    document.getElementById('cd-s').textContent = String(s).padStart(2,'0');
}
updateCountdown();
setInterval(updateCountdown, 1000);

updateWhatsApp('—', 0);
</script>
@endpush
