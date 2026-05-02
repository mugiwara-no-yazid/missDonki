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

        /* ── WHATSAPP FLOAT ── */
        .whatsapp-float { position: fixed; bottom: 30px; right: 24px; z-index: 999; }
        .whatsapp-btn {
            width: 54px; height: 54px; background: #25d366;
            border-radius: 50%; display: flex; align-items: center;
            justify-content: center; box-shadow: 0 4px 20px rgba(37,211,102,.4);
            cursor: pointer; transition: all .3s; text-decoration: none; font-size: 1.5rem;
        }
        .whatsapp-btn:hover { transform: scale(1.1); box-shadow: 0 6px 30px rgba(37,211,102,.6); }

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

        @media(max-width: 768px) {
            nav { padding: 14px 20px; }
            .nav-links { gap: 16px; }
            .nav-links li:not(:last-child):not(:nth-last-child(2)) { display: none; }
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
         @if(!\App\Models\Setting::isResultsVisible())
            <li><a href="{{ route('candidates') }}" class="{{ request()->routeIs('candidates') ? 'active' : '' }}">Candidates</a></li>
         @endif
        @if(\App\Models\Setting::isResultsVisible())
            <li><a href="{{ route('results') }}" class="{{ request()->routeIs('results') ? 'active' : '' }}">Résultats</a></li>
        @endif
    </ul>
</nav>

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

// WhatsApp
function updateWhatsApp(topName, topVotes) {
    const text = `🏆 Gala Tabaski Act 3 – Vote Miss Populaire\n\n👑 En tête : ${topName} avec ${topVotes} votes !\n\nVotez ici : ${window.location.origin}/candidates\n\n#GalaTabaski #MissPopulaire`;
    document.getElementById('whatsapp-share').href = `https://wa.me/?text=${encodeURIComponent(text)}`;
}
</script>

@stack('scripts')
</body>
</html>
