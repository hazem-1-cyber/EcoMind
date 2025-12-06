# EcoMind - Plateforme de Dons Écologiques

## 🌱 Description
Plateforme web permettant de gérer des dons (monétaires et matériels) pour des associations écologiques tunisiennes.

## ⚙️ Fonctionnalités Principales

### Système de Paramètres en Temps Réel
- **Validation automatique des dons monétaires** : 
  - Quand activé : tous les dons monétaires en attente sont validés automatiquement
  - Les nouveaux dons monétaires sont validés directement après paiement
  - Quand désactivé : les dons restent en attente et nécessitent une validation manuelle

- **Montant minimum configurable** : Définir le montant minimum accepté (en TND)
- **Notifications** : Activer/désactiver les notifications par email

### Types de Dons
- **Dons monétaires** : Paiement en ligne via Stripe (TND uniquement)
- **Panneaux solaires** : Don de matériel écologique
- **Matériel** : Don d'équipements divers
- **Autre** : Autres types de dons avec description

## 🔧 Configuration

### Paramètres (BackOffice)
Accédez à la page **Paramètres** pour configurer :
1. Montant minimum de don (TND)
2. Validation automatique des dons monétaires
3. Préférences de notifications

### Paiement Stripe
- Mode TEST pour développement/démonstration
- Devise fixe : TND (Dinar Tunisien)
- Paiement sécurisé via Stripe

## 📁 Structure
```
├── config/
│   ├── SettingsManager.php    # Gestion des paramètres
│   └── settings.json           # Fichier de configuration
├── controller/
│   ├── DonController.php       # Logique des dons
│   └── categorieController.php
├── model/
│   ├── DonModel.php           # Modèle de données
│   └── ParametreModel.php
├── view/
│   ├── BackOffice/            # Interface admin
│   └── FrontOffice/           # Interface publique
└── database.sql               # Structure de la base
```

## 🚀 Installation

### 1. Base de données
Importer le fichier `database.sql` dans votre base MySQL :
```bash
mysql -u root -p ecomind < database.sql
```

### 2. Configuration de l'environnement
Créer un fichier `.env` à la racine du projet en copiant `.env.example` :
```bash
cp .env.example .env
```

Puis éditer le fichier `.env` avec vos propres clés :
```env
# Configuration Stripe (Clés de test)
STRIPE_PUBLIC_KEY=pk_test_VOTRE_CLE_PUBLIQUE_STRIPE
STRIPE_SECRET_KEY=sk_test_VOTRE_CLE_SECRETE_STRIPE

# Configuration Base de données
DB_HOST=localhost
DB_NAME=ecomind
DB_USER=root
DB_PASS=
```

**⚠️ Important :** 
- Ne jamais commiter le fichier `.env` dans Git
- Utiliser uniquement les clés de TEST Stripe pour le développement
- Obtenir vos clés Stripe sur : https://dashboard.stripe.com/test/apikeys

### 3. Dépendances PHP
Installer les dépendances avec Composer :
```bash
composer install
```

### 4. Permissions
Vérifier les permissions des dossiers :
```bash
chmod 755 config/
chmod 666 config/settings.json
chmod 755 uploads/
```

### 5. Accès
Accéder à l'interface via votre serveur web :
- FrontOffice : `http://localhost/ecomind/view/FrontOffice/`
- BackOffice : `http://localhost/ecomind/view/BackOffice/`

## 💳 Mode Test Stripe
Utilisez ces cartes de test :
- Succès : `4242 4242 4242 4242`
- Date : `12/25`
- CVC : `123`

## 📝 Notes
- Projet étudiant - Mode TEST uniquement
- Devise fixe : TND (Tunisie)
- Validation automatique configurable en temps réel
