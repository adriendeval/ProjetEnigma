# Projet Enigma - Site d'énigmes pédagogiques

Site web complet permettant à des groupes d'élèves de 3ème de participer à un jeu d'énigmes sur le thème de l'Intelligence Artificielle.

## 🎯 Fonctionnalités

### Pour les élèves (sans connexion)
- **Création d'équipe** : Choisir un nom et un avatar
- **Salle d'attente** : Attendre le lancement par le professeur
- **Jeu d'énigmes** : 
  - Énigmes "photo" (identifier les images non-générées par IA)
  - Énigmes "timeline" (remettre des événements dans l'ordre)
  - Énigmes "mcq" (questions à choix multiples)
- **Chronomètre en direct** (25 minutes par défaut)
- **Code final** pour terminer et être classé

### Pour les professeurs (avec connexion)
- **Gestion des thèmes** : Créer et modifier des jeux
- **Gestion des énigmes** : Ajouter, modifier, supprimer des énigmes
- **Session de jeu** : 
  - Lancer une partie
  - Suivre les équipes en temps réel
  - Voir le classement final
  - Terminer la partie

## 🛠️ Technologies utilisées

- **Backend** : Symfony 7.4 (PHP 8.3)
- **Frontend** : Twig + Tailwind CSS (via CDN)
- **Database** : MySQL 8.0
- **JavaScript** : Vanilla JS + SortableJS pour le drag & drop
- **Docker** : MySQL via Docker Compose

## 📋 Prérequis

- PHP 8.3 ou supérieur
- Composer
- Docker et Docker Compose
- MySQL 8.0

## 🚀 Installation

### 1. Cloner le projet

```bash
git clone https://github.com/adriendeval/ProjetEnigma.git
cd ProjetEnigma
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Démarrer MySQL

```bash
docker compose up -d
```

### 4. Créer la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5. Charger les données de démonstration

```bash
php bin/console doctrine:fixtures:load
```

Cela créera :
- Un utilisateur admin : `admin@enigma.fr` / `admin123`
- 3 types d'énigmes (photo, timeline, mcq)
- 1 thème : "L'Intelligence Artificielle"
- 3 énigmes de démonstration
- 8 avatars

### 6. Lancer le serveur de développement

```bash
php -S localhost:8000 -t public
```

ou avec Symfony CLI :

```bash
symfony serve
```

Accédez à l'application sur `http://localhost:8000`

## 📖 Utilisation

### Côté élèves

1. Visitez `/` pour créer une équipe
2. Choisissez un nom d'équipe et un avatar
3. Attendez sur la page `/waiting` que le professeur lance la partie
4. Une fois lancée, lisez les règles sur `/play`
5. Résolvez les énigmes une par une `/play/enigma/{order}`
6. Entrez le code final sur `/play/finish` pour terminer

### Côté professeurs

1. Connectez-vous sur `/login` avec `admin@enigma.fr` / `admin123`
2. Accédez au dashboard `/admin`
3. Gérez vos thèmes et énigmes
4. Lancez une session de jeu sur `/admin/session`
5. Suivez la progression des équipes en temps réel
6. Terminez la partie quand vous le souhaitez

## 📁 Structure du projet

```
ProjetEnigma/
├── config/               # Configuration Symfony
├── docs/                 # Documentation
│   └── GUIDE_ENIGMES.md # Guide de création d'énigmes
├── migrations/           # Migrations de base de données
├── public/               # Fichiers publics
│   ├── images/
│   │   ├── avatars/     # Avatars des équipes
│   │   ├── enigmas/     # Images pour les énigmes
│   │   └── games/       # Images de bienvenue
│   └── index.php
├── src/
│   ├── Controller/      # Contrôleurs
│   ├── Entity/          # Entités Doctrine
│   ├── Form/            # Formulaires
│   ├── Repository/      # Repositories
│   └── DataFixtures/    # Fixtures
├── templates/           # Templates Twig
│   ├── admin/          # Templates admin
│   ├── home/           # Page d'accueil et attente
│   ├── play/           # Pages de jeu
│   ├── security/       # Login/Register
│   └── base.html.twig  # Template de base
└── compose.yaml        # Configuration Docker
```

## 🎮 Types d'énigmes

### 1. Photo (IA ou pas ?)

Les élèves doivent identifier quelle image n'a **pas** été générée par une IA parmi des paires d'images.

**Format JSON** :
```json
{
  "pairs": [
    {
      "id": 0,
      "image1": "pair1-real.jpg",
      "image2": "pair1-ai.jpg",
      "correct": 0
    }
  ]
}
```

### 2. Timeline (Ordre chronologique)

Les élèves doivent remettre des éléments dans le bon ordre en utilisant le drag & drop.

**Format JSON** :
```json
{
  "items": [
    {"id": 1, "text": "Premier événement"},
    {"id": 2, "text": "Deuxième événement"}
  ]
}
```

### 3. MCQ (Questions à choix multiples)

Questions avec plusieurs réponses possibles, une seule est correcte.

**Format JSON** :
```json
{
  "questions": [
    {
      "question": "Question ?",
      "answers": ["Réponse A", "Réponse B", "Réponse C"],
      "correct": 1
    }
  ]
}
```

Consultez `docs/GUIDE_ENIGMES.md` pour plus de détails sur la création d'énigmes.

## 🗄️ Configuration de la base de données

Par défaut, le projet utilise MySQL avec les paramètres suivants :

- **Host** : localhost
- **Port** : 3306
- **User** : root
- **Password** : root
- **Database** : projetenigma

Modifiez le fichier `.env` pour changer ces paramètres :

```env
DATABASE_URL="mysql://root:root@127.0.0.1:3306/projetenigma?serverVersion=8.0.32&charset=utf8mb4"
```

## 🔐 Sécurité

- Les élèves ne se connectent jamais (mode équipe avec session PHP)
- Les professeurs/admins ont des comptes avec rôles (ROLE_PROF, ROLE_ADMIN)
- Les routes `/admin/*` sont protégées
- Les mots de passe sont hashés avec bcrypt

## 🎨 Design

- **Tailwind CSS** via CDN pour le styling
- **Design responsive** adapté mobile/tablette/desktop
- **Couleurs** : Bleu/gris foncé pour un aspect professionnel
- **SortableJS** pour le drag & drop dans les énigmes timeline

## 📝 Commandes utiles

```bash
# Créer une nouvelle migration
php bin/console make:migration

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Créer un nouvel utilisateur admin
php bin/console make:user

# Charger les fixtures
php bin/console doctrine:fixtures:load

# Vider le cache
php bin/console cache:clear

# Lister les routes
php bin/console debug:router
```

## 🧪 Tests

Pour tester l'application :

1. Créez plusieurs équipes sur la page d'accueil
2. Connectez-vous en tant qu'admin
3. Lancez une session de jeu avec un code final
4. Dans une autre fenêtre/navigateur, jouez en tant qu'équipe
5. Vérifiez le suivi en temps réel dans l'admin

## 🤝 Contribution

Pour contribuer au projet :

1. Fork le projet
2. Créez une branche (`git checkout -b feature/nouvelle-fonctionnalite`)
3. Committez vos changements (`git commit -am 'Ajout d'une nouvelle fonctionnalité'`)
4. Pushez vers la branche (`git push origin feature/nouvelle-fonctionnalite`)
5. Créez une Pull Request

## 📄 Licence

Ce projet est sous licence propriétaire.

## 👨‍💻 Auteurs

- Projet développé pour des élèves de 3ème
- Thème : Intelligence Artificielle

## 🆘 Support

Pour toute question ou problème :

1. Consultez le fichier `docs/GUIDE_ENIGMES.md`
2. Vérifiez les logs Symfony dans `var/log/`
3. Vérifiez que MySQL est bien démarré avec `docker compose ps`

---

**Note** : Les images placeholder dans `public/images/` sont des exemples. Remplacez-les par de vraies images pour un usage en production.
