# Guide Complet : Authentification Laravel avec Email Verification et Sanctum

## Table des matières
1. [Installation et configuration de Sanctum](#installation-et-configuration-de-sanctum)
2. [Configuration initiale](#configuration-initiale)
3. [Inscription des utilisateurs](#inscription-des-utilisateurs)
4. [Configuration de la vérification d'email](#configuration-de-la-vérification-demail)
5. [Configuration de MailHog](#configuration-de-mailhog)
6. [Tests et utilisation](#tests-et-utilisation)

## Installation et configuration de Sanctum

### 1. Installation de Sanctum
```bash
composer require laravel/sanctum
```

### 2. Publication de la configuration
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 3. Migration des tables
```bash
php artisan migrate
```

### 4. Configuration du modèle User
Dans `app/Models/User.php` :
```php
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

### 5. Configuration du Middleware Sanctum
Dans `bootstrap/app.php` :
```php
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

## Configuration initiale

### Migration de la table users
```php
// La migration users contient par défaut :
$table->id();
$table->string('name');
$table->string('email')->unique();
$table->timestamp('email_verified_at')->nullable();
$table->string('password');
$table->rememberToken();
$table->timestamps();
```

> 💡 **Note** : 
> - `email_verified_at` est utilisé pour la vérification d'email
> - `rememberToken` gère les sessions "remember me"

Pour exécuter la migration :
```bash
php artisan migrate
```

## Inscription des utilisateurs

### 1. Création du AuthController
```bash
php artisan make:controller AuthController
```

### 2. Implémentation du contrôleur
```php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;



class AuthController extends Controller
{
    public function register(RegisterRequest $request){

        // Récupérer les données validées depuis RegisterRequest
        $validateData = $request->validated();

        // Créer un nouvel utilisateur
        $utilisateur = User::create([
            'name' => $validateData['name'],
            'email' => $validateData['email'],
            'password' => Hash::make($validateData['password'])
        ]);

        // Envoyer un email de confirmation d'email
        $utilisateur->sendEmailVerificationNotification();

        // Retourner une réponse JSON avec les details de l'utilisateur créé
        return response()->json([
            'message' => "Utilisateur {$utilisateur->name} créé avec succès vérifier votre email pour la confirmation."
        ]);
    }

    public function login(LoginRequest $request) 
    {
        $infoIdentification = $request->only('email', 'password');

        if (!Auth::attempt($infoIdentification)) {
             Log::warning('Tentative de connexion échouée', [
                'email' => $infoIdentification['email'],
                'ip' => $request->ip(),
            ]);
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        $utilisateur = Auth::user();
        $token = $utilisateur->createToken('auth_token')->plainTextToken;

        // Journaliser le succès
        Log::info('Connexion réussie', [
            'user_id' => $utilisateur->id,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Connexion réussie',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);

    }
}

```

### 3. Configuration des routes avec Sanctum
Dans `routes/api.php` :
```php
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1'); // Limite à 5 tentatives par minute

// Fake route pour la confirmation d'email (patch rapide)
Route::get('/login', function () {
    return response()->json(['message' => 'Email vérifié avec succès. Vous pouvez maintenant vous connecter depuis le frontend React.']);
})->name('login');

// Protection des routes
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Vérification email (sans Sanctum)
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // Marque l'email comme vérifié
    return response()->json(['message' => 'Email vérifié avec succès !']);
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

// Renvoi du lien de vérification
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Lien de vérification envoyé !']);
})->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');
```

## Configuration de MailHog

### 1. Installation de MailHog

Pour tester l'envoi d'emails en local, nous utiliserons MailHog. Voici les étapes d'installation :

#### Option 1 : Installation via Go
```bash
# Installation de Go (nécessaire uniquement si vous voulez compiler MailHog)
sudo apt install golang-go
```

#### Option 2 : Téléchargement direct (recommandé)
```bash
# Télécharger le binaire précompilé
wget https://github.com/mailhog/MailHog/releases/download/v1.0.1/MailHog_linux_amd64 -O MailHog

# Donner les droits d'exécution
chmod +x MailHog

# Lancer MailHog
./MailHog
```

### 2. Configuration dans Laravel

Ajoutez ces variables dans votre fichier `.env` :
```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="no-reply@monapp.com"
MAIL_FROM_NAME="App Event"

# Configuration Sanctum pour les domaines autorisés
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1:8000
```

### 3. Utilisation de MailHog

🚀 Points importants :
- Le serveur SMTP de MailHog écoute sur le port 1025
- Interface Web accessible sur http://localhost:8025
- Tous les emails envoyés par votre application seront interceptés par MailHog
- Visualisez les emails dans l'interface web sans qu'ils soient réellement envoyés

## Test des fonctionnalités

### 1. Test de l'inscription

Utilisez Postman ou cURL pour tester l'inscription :

```http
POST /api/register
Content-Type: application/json

{
    "name": "Mahamane",
    "email": "mahamane@example.com",
    "password": "monsecret123"
}
```

Réponse attendue :
```json
{
    "message": "Utilisateur Mahamane créé avec succès. Vérifiez votre email."
}
```

### 2. Vérification de l'email

1. Ouvrez l'interface MailHog (http://localhost:8025)
2. Vous devriez voir l'email de vérification
3. Cliquez sur le lien de vérification dans l'email
4. Votre email sera marqué comme vérifié

### 3. Test de connexion

```http
POST /api/login
Content-Type: application/json

{
    "email": "mahamane@example.com",
    "password": "monsecret123"
}
```

Réponse attendue :
```json
{
    "message": "Connexion réussie",
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOi...",
    "token_type": "Bearer"
}
```

### 4. Test des routes protégées

Pour accéder aux routes protégées :

```http
GET /api/user
Authorization: Bearer votre_token_ici
```

## Sécurité et journalisation

### 1. Protection contre la force brute
- Limitation à 5 tentatives de connexion par minute
- Journalisation des tentatives échouées

### 2. Logs de sécurité
- Tentatives échouées → `storage/logs/laravel.log` (niveau warning)
- Connexions réussies → `storage/logs/laravel.log` (niveau info)

### 3. Bonnes pratiques
- Utilisez HTTPS en production
- Configurez correctement les domaines autorisés dans `SANCTUM_STATEFUL_DOMAINS`
- Surveillez régulièrement les logs pour détecter les activités suspectes

✅ Fonctionnalités implémentées

- [x] Inscription sécurisée avec validation
- [x] Vérification d'email avec MailHog
- [x] Authentification par token avec Sanctum
- [x] Protection contre la force brute
- [x] Journalisation des activités
- [x] Routes API sécurisées