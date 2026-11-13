# Miss Donki 👑

Plateforme de vote en ligne payant pour l'élection **Miss Donki**, organisée dans le cadre du **Gala Tabaski Act 3** par l'Association des Guinéens au Bénin.

Les électeurs choisissent une candidate, achètent un pack de votes et paient par Mobile Money ou carte via **FedaPay**. Les organisateurs pilotent tout depuis un back-office : candidates, packs, paiements, résultats et paramètres de l'événement.

---

## Sommaire

- [Fonctionnalités](#fonctionnalités)
- [Stack technique](#stack-technique)
- [Installation](#installation)
- [Configuration](#configuration)
- [Utilisation](#utilisation)
- [Parcours de vote](#parcours-de-vote)
- [Structure du projet](#structure-du-projet)
- [Modèle de données](#modèle-de-données)
- [Routes](#routes)
- [Tests](#tests)
- [Déploiement](#déploiement)
- [Sécurité](#sécurité)

---

## Fonctionnalités

### Côté public

- **Accueil** : présentation de l'événement (date, lieu), compteur de candidates et de votes, aperçu des packs.
- **Candidates** : liste des candidates actives, triées par numéro, avec photo et biographie.
- **Vote payant** : choix d'une candidate et d'un pack, puis redirection vers le checkout FedaPay.
- **Résultats** : classement public, **masquable** par l'organisateur jusqu'au jour du gala.
- **Message de transparence** : texte configurable expliquant l'usage des fonds collectés.

### Back-office (`/admin`)

- **Tableau de bord** : vue d'ensemble des votes et des paiements.
- **Candidates** : création, édition, suppression, activation/désactivation, upload de photo (JPEG, PNG, WebP, 2 Mo max).
- **Packs de vote** : modification du prix et du nombre de votes, activation/désactivation.
- **Paiements** : liste filtrable et **export** des transactions.
- **Votes** : historique des votes comptabilisés.
- **Résultats** : classement complet et bascule de visibilité publique.
- **Paramètres** : ouverture/fermeture des votes, affichage des résultats, informations de l'événement, message de transparence.

---

## Stack technique

| Couche | Technologie |
|---|---|
| Backend | PHP ^8.3, Laravel ^13 |
| Base de données | MySQL (sessions, cache et queues en base) |
| Paiement | [FedaPay](https://fedapay.com) (`fedapay/fedapay-php`), devise XOF |
| Frontend | Blade, Vite 8, Tailwind CSS 4, Axios |
| Qualité | PHPUnit 12, Laravel Pint |

---

## Installation

### Prérequis

- PHP 8.3 ou plus, avec les extensions habituelles de Laravel (mbstring, pdo_mysql, openssl, etc.)
- Composer
- Node.js et npm
- MySQL
- Un compte [FedaPay](https://fedapay.com) (le mode sandbox suffit pour tester)

### Étapes

```bash
# 1. Récupérer le projet
git clone <url-du-depot> missDonki
cd missDonki

# 2. Dépendances PHP
composer install

# 3. Environnement
cp .env.example .env
php artisan key:generate

# 4. Créer la base de données MySQL "missDonki", puis renseigner .env (voir ci-dessous)

# 5. Tables et données initiales
php artisan migrate --seed

# 6. Lien public pour les photos des candidates
php artisan storage:link

# 7. Assets frontend
npm install
npm run build
```

> Le script `composer setup` automatise une grande partie de ces étapes (installation, `.env`, clé, migrations, build). Il ne lance pas le seeder ni `storage:link` : à faire à la main.

### Lancer en développement

```bash
composer dev
```

Cette commande démarre en parallèle le serveur (`php artisan serve`), l'écoute de la queue, les logs (`pail`) et Vite.

L'application est alors disponible sur <http://localhost:8000>.

---

## Configuration

Variables principales du fichier `.env` :

```dotenv
APP_NAME="Miss Donki"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=missDonki
DB_USERNAME=root
DB_PASSWORD=

# FedaPay
FEDAPAY_PUBLIC_KEY=pk_sandbox_...
FEDAPAY_SECRET_KEY=sk_sandbox_...
FEDAPAY_ENVIRONMENT=sandbox   # sandbox | live
```

`APP_URL` doit correspondre à l'URL réellement utilisée : elle sert à générer l'URL de retour FedaPay (`/fedapay/callback`).

### Données initiales (seeder)

`php artisan db:seed` crée :

| Élément | Valeur |
|---|---|
| Pack Bronze | 100 FCFA → 1 vote |
| Pack Argent | 500 FCFA → 6 votes |
| Pack Or | 1 000 FCFA → 15 votes |
| Votes | ouverts |
| Résultats publics | masqués |
| Événement | Gala Tabaski Act 3, 30 mai 2026, Salle des fêtes Jéricho |
| Compte admin | `admin@galatabaski.bj` / `ChangeMe2026!!` |

⚠️ **Changez ce mot de passe administrateur avant toute mise en ligne.**

Les candidates ne sont pas seedées : ajoutez-les depuis le back-office.

---

## Utilisation

1. Connectez-vous sur `/admin/login`.
2. Créez les candidates (nom, numéro unique, photo, bio).
3. Vérifiez ou ajustez les packs de vote.
4. Dans **Paramètres**, contrôlez :
   - **Votes ouverts** : coupe ou rouvre la prise de votes sans toucher au code.
   - **Résultats visibles** : garde le classement secret jusqu'au gala, puis le révèle.
5. Suivez les paiements et exportez-les depuis **Paiements**.

---

## Parcours de vote

```
Électeur                 Application                    FedaPay
   │  choisit candidate     │                               │
   │  + pack ─────────────► │ POST /voter                   │
   │                        │ crée Payment (pending)        │
   │                        │ crée la Transaction ────────► │
   │ ◄── URL de checkout ── │ ◄──────── token / URL ─────── │
   │ ─────────────── paiement sur FedaPay ───────────────► │
   │ ◄──── redirection GET /fedapay/callback?id=… ───────── │
   │                        │ retrieve(id) ───────────────► │
   │                        │ statut "approved" ?           │
   │                        │ → Payment = success           │
   │                        │ → crée Vote                   │
   │                        │ → candidate.total_votes += n  │
   │ ◄── message succès ─── │                               │
```

Points à connaître :

- Le paiement local est créé en `pending`, puis passe en `success` ou `failed`.
- La validation, la création du `Vote` et l'incrément du compteur se font dans **une seule transaction SQL**.
- Un paiement déjà traité n'est jamais comptabilisé deux fois.
- `candidates.total_votes` est un compteur dénormalisé pour un classement rapide.
- Si les votes sont fermés, `POST /voter` répond `403`.

---

## Structure du projet

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── PublicController.php      # accueil, résultats
│   │   ├── VotingController.php      # candidates, paiement FedaPay, callback
│   │   └── Admin/                    # Auth, Candidate, Dashboard, Pack,
│   │                                 # Payment, Result, Setting, Vote
│   └── Middleware/AdminAuth.php      # protège /admin
└── Models/                           # Admin, Candidate, Payment, Setting,
                                      # Vote, VotePack, User
database/
├── migrations/                       # schéma complet
└── seeders/DatabaseSeeder.php        # packs, paramètres, compte admin
resources/views/
├── layouts/public.blade.php
├── publics/                          # home, candidates, results
└── admin/                            # auth, dashboard, candidates, packs,
                                      # payments, votes, results, settings
routes/web.php
```

---

## Modèle de données

| Table | Rôle |
|---|---|
| `candidates` | nom, numéro unique, photo, bio, statut actif, `total_votes` |
| `vote_packs` | nom, prix en FCFA, nombre de votes, statut actif |
| `payments` | paiement lié à une candidate et un pack : montant, votes achetés, statut (`pending`, `success`, `failed`), référence FedaPay, IP, user-agent |
| `votes` | votes comptabilisés après un paiement réussi |
| `admins` | comptes du back-office (authentification séparée) |
| `settings` | paramètres clé/valeur, mis en cache 5 minutes |

---

## Routes

### Public

| Méthode | URL | Description |
|---|---|---|
| GET | `/` | Accueil |
| GET | `/candidates` | Candidates et packs |
| GET | `/resultats` | Classement public |
| POST | `/voter` | Initie un paiement (JSON) |
| POST | `/vote/confirm` | Confirmation AJAX d'un paiement |
| GET | `/fedapay/callback` | Retour après paiement FedaPay |

### Admin (préfixe `/admin`, protégé sauf login)

| Méthode | URL | Description |
|---|---|---|
| GET/POST | `/admin/login` | Connexion |
| POST | `/admin/logout` | Déconnexion |
| GET | `/admin` | Tableau de bord |
| resource | `/admin/candidates` | CRUD candidates (sans `show`) |
| PATCH | `/admin/candidates/{id}/toggle` | Activer/désactiver |
| GET | `/admin/votes` | Historique des votes |
| GET | `/admin/payments` | Paiements |
| GET | `/admin/payments/export` | Export des paiements |
| GET/PUT | `/admin/packs` | Liste et édition des packs |
| PATCH | `/admin/packs/{id}/toggle` | Activer/désactiver un pack |
| GET/PUT | `/admin/settings` | Paramètres |
| GET | `/admin/results` | Résultats |
| POST | `/admin/results/toggle` | Afficher/masquer les résultats |

---

## Tests

```bash
composer test
```

Le dossier `tests/` ne contient pour l'instant que les tests d'exemple de Laravel.

---

## Déploiement

1. Passer en production dans `.env` :
   ```dotenv
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://votre-domaine.com
   FEDAPAY_ENVIRONMENT=live
   FEDAPAY_PUBLIC_KEY=pk_live_...
   FEDAPAY_SECRET_KEY=sk_live_...
   ```
2. Installer sans les dépendances de dev : `composer install --no-dev --optimize-autoloader`
3. Compiler les assets : `npm ci && npm run build`
4. Migrer : `php artisan migrate --force`
5. Optimiser : `php artisan config:cache && php artisan route:cache && php artisan view:cache`
6. Pointer la racine web du serveur sur le dossier `public/`.
7. Lancer un worker de queue si vous utilisez des jobs (`QUEUE_CONNECTION=database`).

---

## Sécurité

À vérifier avant la mise en production :

- [ ] Changer le mot de passe du compte admin créé par le seeder.
- [ ] Générer une **nouvelle** `APP_KEY` (`php artisan key:generate`) : celle de `.env.example` est publique dans le dépôt.
- [ ] Ne jamais committer le fichier `.env` ni les clés FedaPay `live`.
- [ ] Mettre `APP_DEBUG=false`.
- [ ] Servir le site en HTTPS.
- [ ] Ajouter un fichier `public/images/placeholder.png` : il est utilisé comme photo par défaut des candidates sans image.

---

## Licence

Projet privé réalisé pour l'Association des Guinéens au Bénin. Basé sur le framework [Laravel](https://laravel.com), distribué sous licence MIT.
