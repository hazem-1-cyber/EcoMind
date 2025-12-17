# 🌱 EcoMind - Plateforme de Dons Écologiques

EcoMind est une plateforme web moderne permettant de gérer des dons écologiques (monétaires et matériels) avec un système de paiement intégré et une interface d'administration complète.

## ✨ Fonctionnalités

### 🎯 Front-Office
- **Formulaire de don** avec validation en temps réel
- **Paiements sécurisés** via Stripe (cartes bancaires)
- **Dons monétaires et matériels** (panneaux solaires, électronique, etc.)
- **Upload d'images** pour les dons matériels
- **Consultation des dons** par email
- **Génération automatique de reçus PDF**

### 📊 Back-Office
- **Dashboard temps réel** avec statistiques et graphiques
- **Histogramme d'évolution** des dons (jours/mois/années)
- **Gestion des dons** (validation, rejet, corbeille)
- **Système de corbeille** avec restauration
- **Paramètres configurables** (montants, objectifs, etc.)
- **Notifications email** automatiques

### 🔧 Technique
- **Architecture MVC** stricte
- **Paiements Stripe** intégrés
- **Emails automatiques** (PHPMailer)
- **Génération PDF** (DomPDF)
- **Base de données MySQL**
- **Interface responsive**

## 🚀 Installation

### Prérequis
- PHP 7.4+
- MySQL 5.7+
- Composer
- Serveur web (Apache/Nginx)

### 1. Cloner le projet
\`\`\`bash
git clone https://github.com/votre-username/ecomind.git
cd ecomind
\`\`\`

### 2. Installer les dépendances
\`\`\`bash
composer install
\`\`\`

### 3. Configuration de la base de données
\`\`\`bash
# Créer la base de données
mysql -u root -p < database.sql
\`\`\`

### 4. Configuration
\`\`\`bash
# Copier le fichier de configuration
cp .env.example .env

# Éditer .env avec vos paramètres
nano .env
\`\`\`

### 5. Configuration Stripe
1. Créer un compte sur [Stripe](https://stripe.com)
2. Récupérer vos clés API (test ou live)
3. Les ajouter dans \`config.php\`

### 6. Configuration Email
1. Configurer Gmail avec un mot de passe d'application
2. Modifier \`controller/config/email_config.php\`

## 📁 Structure du Projet

\`\`\`
ecomind/
├── controller/                    # Contrôleurs
│   ├── config/                   # Configuration
│   │   ├── SettingsManager.php  # Gestion des paramètres
│   │   ├── settings.json         # Paramètres JSON
│   │   └── email_config.php      # Configuration email
│   ├── helpers/                  # Classes utilitaires
│   │   ├── EmailHelper.php       # Envoi d'emails
│   │   └── ReceiptHelper.php     # Génération PDF
│   ├── vendor/                   # Dépendances Composer
│   ├── DonController.php        # Logique des dons
│   └── categorieController.php  # Logique des catégories
├── model/                        # Modèles
│   ├── DonModel.php             # Modèle des dons
│   └── categorieModel.php       # Modèle des catégories
├── view/                        # Vues et interface
│   ├── BackOffice/              # Interface d'administration
│   │   ├── dashboard.php        # Tableau de bord
│   │   ├── dons.php            # Gestion des dons
│   │   ├── corbeille.php       # Corbeille
│   │   └── parametres.php      # Paramètres
│   └── FrontOffice/              # Interface publique
│       ├── addDon.php          # Formulaire de don
│       ├── paiement.php        # Page de paiement
│       ├── consulterdonpersonnel.php # Consultation
│       └── images/             # Ressources
│           └── uploads/        # Fichiers uploadés
├── config.php                  # Configuration principale
└── database.sql               # Structure BDD
\`\`\`

## 🎨 Fonctionnalités Détaillées

### Dashboard Temps Réel
- **Statistiques en direct** : Total des dons, montants collectés
- **Graphiques interactifs** : Évolution par jours/mois/années
- **Objectifs de collecte** : Suivi des objectifs mensuels
- **Notifications** : Alertes pour les dons en attente

### Système de Paiement
- **Stripe intégré** : Paiements sécurisés par carte
- **Multi-devises** : Support TND avec conversion USD
- **Reçus automatiques** : Génération PDF et envoi email
- **Validation automatique** : Option configurable

### Gestion des Dons
- **Types multiples** : Argent, matériel, électronique, etc.
- **Upload d'images** : Photos des dons matériels
- **Workflow complet** : Pending → Validated/Rejected
- **Corbeille** : Système de suppression/restauration

## 🔒 Sécurité

- **Validation côté serveur** : Toutes les données sont validées
- **Protection CSRF** : Sessions sécurisées
- **Sanitisation** : Échappement des données utilisateur
- **Clés API sécurisées** : Configuration externe
- **Uploads sécurisés** : Validation des types de fichiers

## 🧪 Tests

### Cartes de test Stripe
- **Succès** : \`4242 4242 4242 4242\`
- **Échec** : \`4000 0000 0000 0002\`
- **CVV** : n'importe quel 3 chiffres
- **Date** : n'importe quelle date future

## 📧 Configuration Email

### Gmail
1. Activer la validation en 2 étapes
2. Générer un mot de passe d'application
3. Utiliser ce mot de passe dans la configuration

## 🤝 Contribution

1. Fork le projet
2. Créer une branche feature (\`git checkout -b feature/AmazingFeature\`)
3. Commit vos changements (\`git commit -m 'Add AmazingFeature'\`)
4. Push vers la branche (\`git push origin feature/AmazingFeature\`)
5. Ouvrir une Pull Request

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier \`LICENSE\` pour plus de détails.

## 👥 Auteurs

- **Votre Nom** - *Développement initial* - [VotreGitHub](https://github.com/votre-username)

## 🙏 Remerciements

- [Stripe](https://stripe.com) pour l'API de paiement
- [PHPMailer](https://github.com/PHPMailer/PHPMailer) pour l'envoi d'emails
- [DomPDF](https://github.com/dompdf/dompdf) pour la génération PDF
- [Chart.js](https://www.chartjs.org/) pour les graphiques

---

**🌱 EcoMind - Pour un avenir plus vert ! 🌍**
