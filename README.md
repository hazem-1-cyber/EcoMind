# EcoMind - Plateforme Écologique Modulaire

## Description du Projet

**EcoMind** est une plateforme web modulaire dédiée à la promotion de l'écologie et du développement durable. Ce projet  offre une solution complète pour gérer plusieurs aspects d'une organisation écologique :

- **Gestion des dons** : Collecte de dons monétaires et matériels avec paiement sécurisé
- **Boutique en ligne** : Vente de produits écologiques avec gestion des stocks par dépôt
- **Événements** : Organisation et gestion d'événements écologiques avec inscriptions
- **Conseils personnalisés** : Recommandations écologiques basées sur le profil utilisateur
- **Portail d'administration** : Interface complète pour gérer tous les modules

Le projet résout le problème de la fragmentation des outils écologiques en offrant une plateforme unifiée, facile à déployer et à maintenir.

---

## Table des Matières

- [Description du Projet](#description-du-projet)
- [Fonctionnalités Principales](#fonctionnalités-principales)
- [Architecture du Projet](#architecture-du-projet)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Utilisation](#utilisation)
- [Structure des Modules](#structure-des-modules)
- [Base de Données](#base-de-données)
- [Technologies Utilisées](#technologies-utilisées)
- [Contribution](#contribution)
- [Sécurité](#sécurité)
- [Licence](#licence)
- [Contact](#contact)

---

## Fonctionnalités Principales

### 🎁 Module Dons 
- Formulaire de don monétaire et matériel
- Intégration paiement Stripe sécurisé
- Génération automatique de reçus PDF
- Back office de gestion des dons

### 🛒 Module Boutique 
- Catalogue de produits écologiques
- Panier d'achat et gestion des commandes
- Gestion des stocks par dépôt géographique
- Paiement en ligne sécurisé (Stripe)

### 📅 Module Événements 
- Listing et détails des événements
- Système d'inscription en ligne
- Propositions d'événements par les utilisateurs
- Statistiques et administration

### 💡 Module Conseils 
- Questionnaire de profil écologique
- Génération de conseils personnalisés
- Export des conseils en PDF
- Historique des recommandations

### 👥 Portail Principal 
- Authentification et gestion des utilisateurs
- Profils utilisateurs et associations
- Interface d'administration complète
- Dashboard avec Tailwind CSS

---

## Architecture du Projet

Le projet suit une **architecture MVC (Modèle-Vue-Contrôleur)** modulaire 


## Prérequis

Avant d'installer le projet, assurez-vous d'avoir :

- **PHP** >= 7.4 (recommandé : PHP 8.0+)
- **MySQL** >= 5.7 ou **MariaDB** >= 10.2
- **Apache** avec mod_rewrite activé
- **XAMPP** ou **WAMP** (recommandé pour Windows)
- **Composer** (optionnel, pour les dépendances)
- **Compte Stripe** (pour les paiements en ligne)

---

## Installation

### 1. Clonez le repository

```bash
git clone https://github.com/votre-username/ecomind.git
cd ecomind
```

### 2. Configurez votre serveur local

1. Déplacez le dossier du projet dans le répertoire de votre serveur web :
   - **XAMPP** : `C:\xampp\htdocs\ecomind`
   - **WAMP** : `C:\wamp64\www\ecomind`

2. Démarrez Apache et MySQL depuis le panneau de contrôle XAMPP/WAMP

### 3. Créez les bases de données

1. Accédez à **phpMyAdmin** : `http://localhost/phpmyadmin`

2. Exécutez le script SQL complet :
   ```sql
   -- Importez le fichier
   COMPLETE_DATABASE_SETUP.sql
   ```

 

### 4. Installez les dépendances PHP (optionnel)

Si vous utilisez Composer pour certains modules :

```bash
cd ecomind/controller
composer install
```

---

## Configuration

### Configuration des bases de données

Modifiez les fichiers de configuration suivants avec vos identifiants MySQL :

#### 1. Portail principal
```php
// projet_web/config/config.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'integration');
define('DB_USER', 'root');
define('DB_PASS', '');
```

#### 2. Module dons
```php
// ecomind/config.php
$host = 'localhost';
$dbname = 'ecomind';
$username = 'root';
$password = '';
```

#### 3. Module boutique
```php
// depot-products/config/database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'depot_products_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

#### 4. Module événements
```php
// ecomind-events/config.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecomind_events');
define('DB_USER', 'root');
define('DB_PASS', '');
```

#### 5. Module conseils
```php
// ecomind_conseil/config.php
$host = 'localhost';
$dbname = 'ecomind';
$username = 'root';
$password = '';
```

### Configuration Stripe (Paiements)

#### Module dons
```php
// ecomind/config.php
define('STRIPE_SECRET_KEY', 'sk_test_votre_cle_secrete');
define('STRIPE_PUBLIC_KEY', 'pk_test_votre_cle_publique');
```

#### Module boutique
```php
// depot-products/config/stripe_config.php
define('STRIPE_SECRET_KEY', 'sk_test_votre_cle_secrete');
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_votre_cle_publique');
```

**Important** : Obtenez vos clés Stripe sur [https://dashboard.stripe.com/test/apikeys](https://dashboard.stripe.com/test/apikeys)

---

## Utilisation

### Démarrage du projet

1. Assurez-vous qu'Apache et MySQL sont démarrés
2. Accédez au projet via votre navigateur :
   ```
   http://localhost/ecomind/projet_web/
   ```



### Comptes de test

Après l'installation, vous pouvez utiliser ces comptes de test :

**Administrateur** :
- Email : `admin@ecomind.com`
- Mot de passe : `admin123`

**Utilisateur** :
- Email : `user@ecomind.com`
- Mot de passe : `user123`

---

## Structure des Modules

### Module MVC Standard

Chaque module suit le pattern MVC :

- **Contrôleurs** : Gèrent les requêtes HTTP et orchestrent la logique
- **Modèles** : Interagissent avec la base de données (PDO)
- **Vues** : Affichent les données (HTML/CSS/JS)

### Dossiers d'assets

- `public/` : Fichiers CSS, JavaScript, images
- `uploads/` : Fichiers uploadés par les utilisateurs
- `vendor/` : Bibliothèques tierces (PHPMailer, DomPDF, Stripe)

---

## Base de Données

### Bases de données créées

| Base de données | Description | Tables principales |
|----------------|-------------|-------------------|
| `ecomind_final` | Portail principal | `users`, `associations` |
| `depot_products_db` | Boutique | `produits`, `depots`, `stocks`, `commandes` |
| `ecomind_events` | Événements | `evenement`, `inscription`, `proposition` |
| `ecomind` | Conseils | `reponse_formulaire`, `conseil` |

### Scripts utilitaires

Le projet inclut plusieurs scripts pour faciliter la maintenance :

- `COMPLETE_DATABASE_SETUP.sql` : Installation complète
- `depot-products/create_database.php` : Création base boutique
- `depot-products/add_test_products.php` : Données de test
- `fix_associations_table.sql` : Corrections de schéma

---

## Technologies Utilisées

### Backend
- **PHP** 7.4+ : Langage serveur principal
- **MySQL** : Base de données relationnelle
- **PDO** : Accès sécurisé aux bases de données

### Frontend
- **HTML5/CSS3** : Structure et style
- **JavaScript** : Interactivité côté client
- **Tailwind CSS** : Framework CSS utilitaire
- **Bootstrap** : Composants UI (certains modules)

### Bibliothèques tierces
- **Stripe API** : Paiements en ligne sécurisés
- **PHPMailer** : Envoi d'emails
- **DomPDF** : Génération de PDF
- **jQuery** : Manipulation DOM (certains modules)

### Outils de développement
- **XAMPP/WAMP** : Environnement de développement local
- **phpMyAdmin** : Gestion de base de données
- **Git** : Contrôle de version

---

## Contribution

Nous accueillons les contributions de la communauté ! Voici comment participer :

### 1. Forkez le projet

Cliquez sur le bouton "Fork" en haut de la page GitHub.

### 2. Créez une branche pour votre fonctionnalité

```bash
git checkout -b feature/ma-nouvelle-fonctionnalite
```

### 3. Committez vos changements

```bash
git add .
git commit -m "Ajout d'une nouvelle fonctionnalité : description"
```

### 4. Poussez vers votre fork

```bash
git push origin feature/ma-nouvelle-fonctionnalite
```

### 5. Ouvrez une Pull Request

Allez sur GitHub et créez une Pull Request depuis votre branche vers `main`.

### Règles de contribution

- Suivez le style de code existant (PSR-12 pour PHP)
- Commentez votre code en français
- Testez vos modifications avant de soumettre
- Décrivez clairement vos changements dans la PR
- Assurez-vous que votre code ne casse pas les fonctionnalités existantes

### Signaler des bugs

Ouvrez une issue sur GitHub avec :
- Description détaillée du problème
- Étapes pour reproduire
- Captures d'écran si pertinent
- Environnement (OS, version PHP, navigateur)

---

## Sécurité

### Bonnes pratiques implémentées

- **PDO avec requêtes préparées** : Protection contre les injections SQL
- **Sessions PHP** : Gestion sécurisée de l'authentification
- **Validation des entrées** : Côté client et serveur
- **HTTPS recommandé** : Pour la production
- **Stripe en mode test** : Par défaut pour le développement

### Recommandations pour la production

1. **Ne committez jamais les clés secrètes** :
   - Utilisez des variables d'environnement
   - Créez un fichier `.env` (non versionné)

2. **Supprimez les scripts de test** :
   - `add_test_products.php`
   - `debug.php`
   - Tous les fichiers `test_*.php`

3. **Protégez les dossiers sensibles** :
   - Ajoutez des fichiers `.htaccess`
   - Limitez l'accès aux dossiers `config/`

4. **Activez HTTPS** :
   - Utilisez un certificat SSL
   - Forcez la redirection HTTPS

5. **Mettez à jour régulièrement** :
   - PHP et ses extensions
   - Bibliothèques tierces
   - Dépendances Composer

---

## Licence

Ce projet est sous licence **MIT**.

Vous êtes libre de :
- Utiliser ce code à des fins commerciales ou personnelles
- Modifier et distribuer le code
- Utiliser le code dans des projets privés

Conditions :
- Inclure une copie de la licence MIT
- Mentionner les auteurs originaux

Pour plus de détails, consultez le fichier [LICENSE](LICENSE) à la racine du projet.

---

## Contact

### Équipe de développement

**Projet EcoMind** - Année universitaire 2025-2026

- **Repository GitHub** : [https://github.com/votre-username/ecomind](https://github.com/votre-username/ecomind)
- **Email** : contact@ecomind.com
- **Documentation** : Consultez les fichiers `.md` dans chaque module

### Support

Pour toute question ou problème :
1. Consultez la documentation dans les dossiers des modules
2. Ouvrez une issue sur GitHub
3. Contactez l'équipe par email

---

## Remerciements

Merci à tous les contributeurs qui ont participé à ce projet !

- Template Tailwind CSS pour le design
- Communauté PHP pour les bonnes pratiques
- Stripe pour l'API de paiement
- Tous les testeurs et utilisateurs

---

**Développé avec ❤️ pour un monde plus écologique**
