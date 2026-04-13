<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin — Gala Tabaski Act 3</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root { --noir:#0a0a0a; --noir2:#111; --or:#c9a84c; --or2:#e8c76a; --blanc:#f5f4ef; --gris2:#3a3a3a; --gris3:#888; }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'DM Sans',sans-serif;background:var(--noir);color:var(--blanc);min-height:100vh;display:flex;align-items:center;justify-content:center;}
        body::before{
            content:'';position:fixed;inset:0;
            background:radial-gradient(ellipse 60% 50% at 50% 100%, rgba(201,168,76,.06) 0%, transparent 70%);
            pointer-events:none;
        }
        .box{
            width:100%;max-width:400px;padding:0 20px;
        }
        .brand{text-align:center;margin-bottom:40px;}
        .brand .title{font-family:'Cormorant Garamond',serif;font-size:28px;font-weight:600;color:var(--or);}
        .brand .sub{font-size:12px;color:var(--gris3);margin-top:6px;letter-spacing:1px;text-transform:uppercase;}
        .card{background:var(--noir2);border:1px solid #1e1e1e;border-radius:16px;padding:36px 32px;}
        .card h2{font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:500;color:var(--blanc);margin-bottom:24px;}
        .form-group{margin-bottom:18px;}
        .form-label{display:block;font-size:12px;color:var(--gris3);margin-bottom:6px;letter-spacing:.3px;}
        .form-control{
            width:100%;padding:11px 14px;
            background:#161616;border:1px solid #2a2a2a;
            border-radius:8px;color:var(--blanc);
            font-family:'DM Sans',sans-serif;font-size:14px;
            outline:none;transition:border-color .18s;
        }
        .form-control:focus{border-color:var(--or);}
        .form-control::placeholder{color:#555;}
        .error-msg{background:rgba(192,57,43,.1);border:1px solid rgba(192,57,43,.3);color:#e05a4b;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;}
        .btn{
            width:100%;padding:12px;background:var(--or);color:var(--noir);
            border:none;border-radius:8px;font-family:'DM Sans',sans-serif;
            font-size:14px;font-weight:500;cursor:pointer;
            transition:background .18s;letter-spacing:.3px;
        }
        .btn:hover{background:var(--or2);}
        .remember{display:flex;align-items:center;gap:8px;margin-bottom:20px;cursor:pointer;}
        .remember input{accent-color:var(--or);}
        .remember span{font-size:13px;color:var(--gris3);}
        .footer-note{text-align:center;margin-top:20px;font-size:12px;color:#444;}
    </style>
</head>
<body>
<div class="box">
    <div class="brand">
        <div class="title">Gala Tabaski Act 3</div>
        <div class="sub">Espace Administration</div>
    </div>

    <div class="card">
        <h2>Connexion</h2>

        @if($errors->any())
            <div class="error-msg">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Adresse e-mail</label>
                <input type="email" name="email" class="form-control"
                       placeholder="admin@exemple.com"
                       value="{{ old('email') }}" required autofocus>
            </div>
            <div class="form-group">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control"
                       placeholder="••••••••" required>
            </div>
            <label class="remember">
                <input type="checkbox" name="remember" value="1">
                <span>Rester connecté</span>
            </label>
            <button type="submit" class="btn">Accéder au dashboard</button>
        </form>
    </div>

    <div class="footer-note">Association des Guinéens au Bénin</div>
</div>
</body>
</html>