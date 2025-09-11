# Application de Gestion d'Événements #### 1. Modèles
- `Evenement.php` : Modèle Eloquent pour la gestion des événements
- `User.php` : Modèle Eloquent pour la gestion des utilisateurs

#### 2. Contrôleurs
- `EventController.php` : Gestion des opérations CRUD pour les événements

#### 3. Requêtes de Formulaire
- `StoreEventRequest.php` : Validation pour la création d'événements
- `UpdateEventRequest.php` : Validation pour la mise à jour d'événements

#### 4. Événements et Écouteurs
- `EventCreated.php` : Événement déclenché lors de la création d'un événement
- `SendEventNotification.php` : Écouteur pour envoyer des notifications

#### 5. Files d'Attente
- `ProcessEventBanner.php` : Job pour le traitement des bannières d'événements

#### 6. Notifications
- `EventReminder.php` : Notification de rappel d'événement

### Architecture en Couches

Cette application est un système de gestion d'événements développé avec Laravel, suivant une architecture propre et modulaire.

## Structure du Projet

### Architecture des Dossiers

```
app/
├── Controllers/          # Contrôleurs de l'application
├── Depots/              # Repositories pour l'accès aux données
├── Events/              # Événements Laravel
├── Http/                # Requêtes HTTP et Middleware
├── Interfaces/          # Contrats pour l'injection de dépendances
├── Jobs/                # Tâches en arrière-plan
├── Listeners/           # Écouteurs d'événements
├── Models/              # Modèles Eloquent
├── Notifications/       # Notifications Laravel
├── Services/           # Logique métier
├── Traits/             # Traits PHP réutilisables
└── Utilitaires/        # Fonctions helpers globales
```

### Composants Principaux

1. **Couche Présentation**
   - Controllers : Gestion des requêtes HTTP
   - Requests : Validation des données
   - Resources : Transformation des données

2. **Couche Métier**
   - Services : Logique métier complexe
   - Events : Communication entre composants
   - Jobs : Traitements asynchrones

3. **Couche Données**
   - Models : Représentation des données
   - Repositories : Accès aux données
   - Interfaces : Contrats pour l'injection de dépendances

## Migrations de Base de Données

La base de données inclut les tables suivantes :
- `users` : Stockage des utilisateurs
- `evenements` : Stockage des événements
- `jobs` : File d'attente pour les tâches asynchrones
- `cache` : Mise en cache des données

## Configuration

1. **Base de données**
   - Configurée dans `config/database.php`
   - Utilise SQLite par défaut

2. **File d'attente**
   - Configuration dans `config/queue.php`
   - Jobs asynchrones pour le traitement des bannières

3. **Système de fichiers**
   - Configuration dans `config/filesystems.php`
   - Stockage des fichiers dans `storage/app/public/`

## Tests

Le projet inclut une structure de tests :
- `tests/Feature/` : Tests d'intégration
- `tests/Unit/` : Tests unitaires

## Développement

Pour démarrer le développement :

```bash
# Installation des dépendances
composer install

# Configuration de l'environnement
cp .env.example .env
php artisan key:generate

# Migration de la base de données
php artisan migrate

# Démarrage du serveur
php artisan serve
```
