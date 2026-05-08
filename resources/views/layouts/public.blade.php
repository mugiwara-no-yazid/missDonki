<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vote Miss Populaire') — Gala Tabaski Act 3</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=Cormorant+Garamond:wght@300;400;600&family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --rouge:       #c0001a;
            --rouge-fonce: #8b0012;
            --rouge-vif:   #e5001f;
            --or:          #c9a84c;
            --or-clair:    #f0d080;
            --or-fonce:    #a0782a;
            --blanc:       #fdfaf4;
            --noir:        #0a0a0a;
            --gris:        #1a1a1a;
        }
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--noir);
            color: var(--blanc);
            overflow-x: hidden;
        }

        /* ── PARTICLES ── */
        #particles {
            position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none; z-index: 0; overflow: hidden;
        }
        .particle {
            position: absolute; width: 2px; height: 2px;
            background: var(--or); border-radius: 50%;
            animation: floatUp linear infinite; opacity: 0;
        }
        @keyframes floatUp {
            0%   { transform: translateY(100vh) rotate(0deg);   opacity: 0; }
            10%  { opacity: 0.6; }
            90%  { opacity: 0.3; }
            100% { transform: translateY(-10vh) rotate(720deg); opacity: 0; }
        }

        /* ── NAV ── */
        nav {
            position: fixed; top: 0; width: 100%; z-index: 1000;
            display: flex; justify-content: space-between; align-items: center;
            padding: 18px 40px;
            background: linear-gradient(180deg, rgba(0,0,0,.95) 0%, transparent 100%);
            border-bottom: 1px solid rgba(201,168,76,.15);
            backdrop-filter: blur(8px);
        }
        .nav-logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem; color: var(--or);
            letter-spacing: 2px; text-transform: uppercase;
            text-decoration: none;
        }
        .nav-links { display: flex; gap: 32px; list-style: none; align-items: center; }
        .nav-links a {
            color: rgba(253,250,244,.75); text-decoration: none;
            font-size: .7rem; letter-spacing: 3px; text-transform: uppercase;
            transition: color .3s; font-weight: 600;
        }
        .nav-links a:hover,
        .nav-links a.active { color: var(--or); }
        .nav-links .btn-nav-vote {
            background: linear-gradient(135deg, var(--rouge), var(--rouge-fonce));
            color: var(--blanc); padding: 8px 20px; border-radius: 4px;
            border: 1px solid rgba(201,168,76,.3);
        }
        .nav-links .btn-nav-vote:hover { box-shadow: 0 0 20px rgba(192,0,26,.5); color: var(--blanc); }

        /* ── SECTION COMMONS ── */
        .section-label {
            font-size: .6rem; letter-spacing: 5px; text-transform: uppercase;
            color: var(--or); text-align: center; margin-bottom: 12px;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 700;
            text-align: center; line-height: 1.1; margin-bottom: 16px;
        }
        .section-divider {
            display: flex; align-items: center; justify-content: center;
            gap: 16px; margin-bottom: 60px;
        }
        .divider-line { width: 60px; height: 1px; background: linear-gradient(90deg, transparent, var(--or)); }
        .divider-line.right { background: linear-gradient(90deg, var(--or), transparent); }
        .divider-gem { color: var(--or); font-size: .8rem; }

        /* ── FOOTER ── */
        footer {
            position: relative; z-index: 1;
            border-top: 1px solid rgba(201,168,76,.1);
            padding: 20px 24px; text-align: center;
        }
        .footer-logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem; color: var(--or);
            margin-bottom: 10px; font-style: italic;
        }
        .footer-org {
            font-size: .65rem; letter-spacing: 3px;
            color: rgba(253,250,244,.4); text-transform: uppercase; margin-bottom: 24px;
        }
        .footer-links { display: flex; justify-content: center; gap: 15px; flex-wrap: wrap; }
        .footer-links a {
            font-size: .65rem; letter-spacing: 2px;
            color: rgba(253,250,244,.3); text-decoration: none;
            text-transform: uppercase; transition: color .2s;
        }
        .footer-links a:hover { color: var(--or); }
        .footer-copy { margin-top: 15px; font-size: .6rem; color: rgba(253,250,244,.2); }
        .footer-transparency {
            max-width: 700px; margin: 0 auto 32px;
            background: linear-gradient(135deg, rgba(201,168,76,.08), rgba(192,0,26,.05));
            border: 1px solid rgba(201,168,76,.25);
            border-radius: 8px; padding: 20px 28px;
            display: flex; align-items: flex-start; gap: 14px;
        }
        .footer-transparency .icon { font-size: 1.4rem; flex-shrink: 0; }
        .footer-transparency p {
            font-size: .78rem; color: rgba(253,250,244,.7);
            line-height: 1.7; font-style: italic;
        }
        .footer-transparency strong { color: var(--or); }


        /* ── CONFETTI ── */
        .confetti { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 2999; }
        .confetti-piece {
            position: absolute; border-radius: 1px;
            animation: confettiFall 3s ease-in forwards; opacity: 0;
        }
        @keyframes confettiFall {
            0%   { transform: translateY(-20px) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh)  rotate(720deg); opacity: 0; }
        }

        /* ── TOAST NOTIFICATIONS ── */
        .toast-container {
            position: fixed; top: 90px; right: 24px; z-index: 5000;
            display: flex; flex-direction: column; gap: 10px;
            pointer-events: none;
        }
        .toast {
            pointer-events: auto;
            display: flex; align-items: center; gap: 12px;
            min-width: 300px; max-width: 420px;
            padding: 16px 20px;
            border-radius: 10px;
            font-family: 'Montserrat', sans-serif;
            font-size: .82rem; font-weight: 500;
            color: var(--blanc);
            backdrop-filter: blur(16px);
            box-shadow: 0 8px 32px rgba(0,0,0,.5), 0 0 0 1px rgba(255,255,255,.05);
            animation: toastIn .4s cubic-bezier(.21,1.02,.73,1) forwards;
            cursor: pointer;
            transition: opacity .3s, transform .3s;
        }
        .toast:hover { transform: translateX(-4px); }
        .toast.toast-out {
            animation: toastOut .35s cubic-bezier(.55,.085,.68,.53) forwards;
        }
        .toast-success {
            background: linear-gradient(135deg, rgba(0,180,80,.2), rgba(0,100,40,.35));
            border: 1px solid rgba(0,220,100,.3);
        }
        .toast-error {
            background: linear-gradient(135deg, rgba(192,0,26,.25), rgba(140,0,18,.4));
            border: 1px solid rgba(229,0,31,.35);
        }
        .toast-info {
            background: linear-gradient(135deg, rgba(201,168,76,.15), rgba(160,120,42,.3));
            border: 1px solid rgba(201,168,76,.35);
        }
        .toast-icon { font-size: 1.3rem; flex-shrink: 0; }
        .toast-body { flex: 1; line-height: 1.5; }
        .toast-close {
            background: none; border: none; color: rgba(253,250,244,.4);
            font-size: 1.1rem; cursor: pointer; padding: 0 0 0 8px;
            transition: color .2s; font-family: 'Montserrat', sans-serif;
        }
        .toast-close:hover { color: var(--blanc); }
        .toast-progress {
            position: absolute; bottom: 0; left: 0; height: 3px;
            border-radius: 0 0 10px 10px;
            animation: toastProgress 5s linear forwards;
        }
        .toast-success .toast-progress { background: rgba(0,220,100,.5); }
        .toast-error .toast-progress   { background: rgba(229,0,31,.5); }
        .toast-info .toast-progress     { background: rgba(201,168,76,.5); }
        @keyframes toastIn {
            from { opacity: 0; transform: translateX(80px) scale(.95); }
            to   { opacity: 1; transform: translateX(0) scale(1); }
        }
        @keyframes toastOut {
            from { opacity: 1; transform: translateX(0); }
            to   { opacity: 0; transform: translateX(80px); }
        }
        @keyframes toastProgress {
            from { width: 100%; } to { width: 0%; }
        }

        @media(max-width: 768px) {
            nav { padding: 14px 20px; }
            .nav-links { gap: 16px; }
            .nav-links li:not(:last-child):not(:nth-last-child(2)) { display: none; }
            .toast-container { right: 12px; left: 12px; }
            .toast { min-width: auto; max-width: 100%; }
        }
        @media(max-width: 480px) {
            .nav-links { gap: 10px; }
        }
    </style>
    @stack('styles')
</head>
<body>

<div id="particles"></div>
<div class="confetti" id="confetti"></div>

<nav>
    <a href="{{ route('home') }}" class="nav-logo">Gala Tabaski ✦ Act 3</a>
    <ul class="nav-links">
        <li><a href="{{ route('home') }}#about" class="{{ request()->routeIs('home') ? 'active' : '' }}">Concours</a></li>
        <li><a href="{{ route('candidates') }}" class="{{ request()->routeIs('candidates') ? 'active' : '' }}">Candidates</a></li>
        @if(\App\Models\Setting::isResultsVisible())
            <li><a href="{{ route('results') }}" class="{{ request()->routeIs('results') ? 'active' : '' }}">Résultats</a></li>
        @endif
    </ul>
</nav>

<div class="toast-container" id="toast-container"></div>

<main>
    @yield('content')
</main>

<footer>
    <div class="footer-transparency">
        <span class="icon"><img src="{{ asset('storage/icons/atm-card.png') }}" alt="Calendrier"  style="max-width: 30px;"></span>
        <p>
            <strong>Transparence :</strong>
            {{ \App\Models\Setting::get('transparency_message', "Chaque vote coûte 100 FCFA. Les fonds collectés servent à l'organisation du Gala Tabaski et aux récompenses des candidates.") }}
        </p>
    </div>
    <div class="footer-logo">Gala Tabaski Act 3</div>
    <div class="footer-org">{{ \App\Models\Setting::get('organizer', 'Association des Guinéens au Bénin') }}</div>
    <div class="footer-links">
        <a href="{{ route('home') }}#about">À propos</a>
        <a href="{{ route('candidates') }}">Candidates</a>
        @if(\App\Models\Setting::isResultsVisible())
        <a href="{{ route('results') }}">Résultats</a>
        @endif
    </div>
    <p class="footer-copy">© {{ date('Y') }} Association des Guinéens au Bénin. Tous droits réservés.</p>
</footer>



<script>
// Particules
(function() {
    const c = document.getElementById('particles');
    for (let i = 0; i < 40; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        p.style.cssText = `left:${Math.random()*100}%;width:${Math.random()*3+1}px;height:${Math.random()*3+1}px;animation-duration:${8+Math.random()*12}s;animation-delay:${Math.random()*10}s;`;
        c.appendChild(p);
    }
})();

// Confettis
function spawnConfetti() {
    const container = document.getElementById('confetti');
    const colors = ['#C0001A','#C9A84C','#F0D080','#ffffff','#E5001F','#A0782A'];
    container.innerHTML = '';
    for (let i = 0; i < 80; i++) {
        const p = document.createElement('div');
        p.className = 'confetti-piece';
        p.style.cssText = `left:${Math.random()*100}%;background:${colors[Math.floor(Math.random()*colors.length)]};width:${4+Math.random()*8}px;height:${4+Math.random()*8}px;border-radius:${Math.random()>.5?'50%':'2px'};animation-delay:${Math.random()*.5}s;animation-duration:${2+Math.random()*2}s;`;
        container.appendChild(p);
    }
    setTimeout(() => container.innerHTML = '', 4000);
}

// ── TOAST SYSTEM ──
function showToast(message, type = 'info', duration = 5000) {
    const container = document.getElementById('toast-container');
    const icons = { success: '✅', error: '❌', info: 'ℹ️' };
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <span class="toast-icon">${icons[type] || icons.info}</span>
        <span class="toast-body">${message}</span>
        <button class="toast-close" onclick="dismissToast(this.parentElement)">✕</button>
        <div class="toast-progress"></div>
    `;
    toast.addEventListener('click', () => dismissToast(toast));
    container.appendChild(toast);
    setTimeout(() => dismissToast(toast), duration);
}

function dismissToast(toast) {
    if (!toast || toast.classList.contains('toast-out')) return;
    toast.classList.add('toast-out');
    setTimeout(() => toast.remove(), 350);
}

// Auto-show Laravel flash messages
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        showToast(@json(session('success')), 'success');
    @endif
    @if(session('error'))
        showToast(@json(session('error')), 'error');
    @endif
    @if(session('info'))
        showToast(@json(session('info')), 'info');
    @endif
});
</script>

@stack('scripts')
</body>
</html>
