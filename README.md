# Application de Gestion d'Événements

Une application web construite avec Laravel pour gérer et organiser des événements. Cette plateforme permet aux utilisateurs de créer, découvrir et participer à des événements, qu'ils soient en présentiel ou en ligne.

## 🚀 Fonctionnalités

- **Authentification complète**
  - Inscription et connexion des utilisateurs
  - Vérification d'email
  - Gestion des tokens d'accès avec Sanctum

- **Gestion des événements**
  - Création et modification d'événements
  - Support pour événements présentiels et en ligne
  - Gestion de la capacité et de la billetterie
  - Système de tarification flexible (gratuit/payant)

- **Catégorisation avancée**
  - Tags pour le référencement
  - Système d'ambiances (festif, détente, culturel, etc.)
  - Types d'événements (musique, conférence, etc.)

- **Gestion des médias**
  - Support pour images et vidéos
  - Gestion de l'ordre d'affichage
  - Image principale de l'événement

- **Fonctionnalités sociales**
  - Système de favoris (événements enregistrés)
  - Gestion des participants
  - Interactions entre utilisateurs

## 🛠️ Technologies utilisées

- **Framework**: Laravel
- **Base de données**: MySQL
- **Authentification**: Laravel Sanctum
- **File Storage**: Laravel Storage
- **Queue System**: Laravel Jobs & Queue

## 📋 Prérequis

- PHP >= 8.1
- Composer
- MySQL 
- Serveur web (Apache)

## ⚙️ Installation

1. Cloner le dépôt
```bash
git clone https://github.com/Mahamane-Korobara/app_event.git
cd app_event_laravel
```

2. Installer les dépendances
```bash
composer install
```

3. Configurer l'environnement
```bash
cp .env.example .env
php artisan key:generate
```

4. Configurer la base de données dans le fichier .env
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nom_de_votre_base
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
```

5. Migrer la base de données
```bash
php artisan migrate
```

6. Lancer les seeders (optionnel)
```bash
php artisan db:seed
```

7. Lancer l'application
```bash
php artisan serve
```

## 📝 Structure de la base de données

- **users**: Gestion des utilisateurs
- **evenements**: Table principale des événements
- **ambiances**: Catégories d'ambiance
- **tags**: Système de tags
- **medias**: Gestion des médias
- **participants**: Gestion des inscriptions
- **evenements_enregistres**: Système de favoris

## 🔒 API Endpoints

L'API utilise l'authentification Sanctum. Voici les endpoints disponibles :

### Authentification

```http
# Inscription d'un nouvel utilisateur
POST /api/register
{
    "name": "string",
    "email": "string",
    "password": "string"
}

# Connexion
POST /api/login
{
    "email": "string",
    "password": "string"
}

# Déconnexion (nécessite authentification)
POST /api/logout

# Obtenir les informations de l'utilisateur connecté
GET /api/user

# Vérification d'email
GET /api/email/verify/{id}/{hash}

# Renvoyer l'email de vérification
POST /api/email/verification-notification
```

Tous les endpoints, sauf `/register` et `/login`, nécessitent un token Bearer dans l'en-tête Authorization :
```http
Authorization: Bearer <votre_token>
```

### Limites de taux

- Login : 5 tentatives par minute
- Envoi d'email de vérification : 6 tentatives par minute

