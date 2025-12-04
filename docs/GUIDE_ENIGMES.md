# Guide de Création d'Énigmes - Projet Enigma

Ce guide explique comment créer et configurer des énigmes pour le jeu Projet Enigma.

## Types d'énigmes disponibles

### 1. Type "photo" - IA ou pas ?

Ce type d'énigme présente des paires d'images où l'élève doit identifier quelle image n'a PAS été générée par une IA.

#### Structure JSON attendue :

```json
{
  "pairs": [
    {
      "id": 0,
      "image1": "img1-real.jpg",
      "image2": "img1-ai.jpg",
      "correct": 0
    },
    {
      "id": 1,
      "image1": "img2-ai.jpg",
      "image2": "img2-real.jpg",
      "correct": 1
    }
  ]
}
```

#### Champs :
- `id` : Identifiant unique de la paire (0, 1, 2, etc.)
- `image1` : Nom du fichier de la première image
- `image2` : Nom du fichier de la deuxième image
- `correct` : Index de l'image correcte (0 pour image1, 1 pour image2)

#### Comment ajouter les images :
1. Placez vos images dans `public/images/enigmas/`
2. Nommez-les de manière cohérente (ex: `pair1-real.jpg`, `pair1-ai.jpg`)
3. Référencez le nom du fichier dans le JSON

#### Règles du jeu :
- L'élève peut faire 2 erreurs maximum
- À la 3ème erreur, il doit recommencer l'énigme
- Une fois validée, l'énigme révèle le code secret

---

### 2. Type "timeline" - Remettre de l'ordre

Ce type d'énigme demande aux élèves de remettre des éléments dans le bon ordre chronologique ou logique.

#### Structure JSON attendue :

```json
{
  "items": [
    {
      "id": 1,
      "text": "Premier événement"
    },
    {
      "id": 2,
      "text": "Deuxième événement"
    },
    {
      "id": 3,
      "text": "Troisième événement"
    }
  ]
}
```

#### Champs :
- `id` : Identifiant unique de l'élément (l'ordre correct est déterminé par ces ID)
- `text` : Texte à afficher pour cet élément

#### Fonctionnement :
- Les éléments sont mélangés aléatoirement à l'affichage
- L'élève utilise le drag & drop (glisser-déposer) pour les réorganiser
- La validation vérifie que l'ordre des ID correspond à l'ordre croissant

#### Exemple concret :

```json
{
  "items": [
    {
      "id": 1,
      "text": "Test de Turing proposé par Alan Turing (1950)"
    },
    {
      "id": 2,
      "text": "Création du terme 'Intelligence Artificielle' à Dartmouth (1956)"
    },
    {
      "id": 3,
      "text": "Deep Blue bat Kasparov aux échecs (1997)"
    }
  ]
}
```

---

### 3. Type "mcq" - Questions à Choix Multiple

Ce type d'énigme présente une série de questions avec plusieurs réponses possibles.

#### Structure JSON attendue :

```json
{
  "questions": [
    {
      "question": "Quelle est la capitale de la France ?",
      "answers": ["Londres", "Paris", "Berlin", "Madrid"],
      "correct": 1
    },
    {
      "question": "Combien font 2 + 2 ?",
      "answers": ["3", "4", "5", "6"],
      "correct": 1
    }
  ]
}
```

#### Champs :
- `question` : Texte de la question
- `answers` : Tableau des réponses possibles
- `correct` : Index de la bonne réponse (0 pour la première, 1 pour la deuxième, etc.)

#### Règles du jeu :
- Toutes les questions doivent être répondues
- Toutes les réponses doivent être correctes pour valider l'énigme
- En cas d'erreur, l'élève peut réessayer immédiatement

---

## Workflow de création d'une énigme

### Étape 1 : Préparer le contenu

1. **Choisissez le type** d'énigme adapté à votre objectif pédagogique
2. **Rédigez le titre** et les **instructions** claires
3. **Définissez un code secret** unique (ex: "AI42", "CHRONO2024")
4. **Préparez les données** selon le format JSON du type choisi

### Étape 2 : Ajouter les médias (si nécessaire)

Pour les énigmes de type "photo" :
1. Préparez vos images (format JPG ou PNG recommandé)
2. Nommez-les de manière cohérente
3. Placez-les dans `public/images/enigmas/`

### Étape 3 : Créer l'énigme dans l'admin

1. Connectez-vous à l'interface admin (`/admin`)
2. Accédez à la gestion des thèmes (`/admin/games`)
3. Cliquez sur "Énigmes" pour le thème souhaité
4. Cliquez sur "Nouvelle énigme"
5. Remplissez le formulaire :
   - **Type** : Sélectionnez le type d'énigme
   - **Ordre** : Numéro de l'énigme dans la séquence (1, 2, 3, etc.)
   - **Titre** : Titre accrocheur
   - **Instructions** : Consignes claires pour les élèves
   - **Code secret** : Code qui sera révélé après réussite
   - **Données JSON** : Collez votre structure JSON
6. Enregistrez

### Étape 4 : Tester l'énigme

1. Créez une équipe test sur la page d'accueil
2. Lancez une session de jeu depuis l'admin
3. Jouez l'énigme pour vérifier qu'elle fonctionne correctement
4. Vérifiez que :
   - Les images s'affichent correctement (pour type "photo")
   - Les éléments peuvent être réorganisés (pour type "timeline")
   - Les réponses sont correctement validées (pour type "mcq")
   - Le code secret s'affiche après réussite

---

## Exemples complets

### Exemple 1 : Énigme "photo" sur la reconnaissance d'images

**Titre** : "Vraie ou fausse photo ?"
**Instructions** : "Identifiez les vraies photographies parmi les images générées par IA"
**Code secret** : "REAL2024"

**JSON** :
```json
{
  "pairs": [
    {
      "id": 0,
      "image1": "photo-nature-real.jpg",
      "image2": "photo-nature-ai.jpg",
      "correct": 0
    },
    {
      "id": 1,
      "image1": "photo-portrait-ai.jpg",
      "image2": "photo-portrait-real.jpg",
      "correct": 1
    },
    {
      "id": 2,
      "image1": "photo-ville-real.jpg",
      "image2": "photo-ville-ai.jpg",
      "correct": 0
    }
  ]
}
```

### Exemple 2 : Énigme "timeline" sur l'histoire de l'informatique

**Titre** : "L'évolution de l'informatique"
**Instructions** : "Remettez ces inventions dans l'ordre chronologique"
**Code secret** : "CHRONO1945"

**JSON** :
```json
{
  "items": [
    {"id": 1, "text": "ENIAC - Premier ordinateur électronique (1945)"},
    {"id": 2, "text": "Transistor inventé (1947)"},
    {"id": 3, "text": "Premier microprocesseur Intel 4004 (1971)"},
    {"id": 4, "text": "Apple II commercialisé (1977)"},
    {"id": 5, "text": "World Wide Web créé par Tim Berners-Lee (1989)"}
  ]
}
```

### Exemple 3 : Énigme "mcq" sur la cybersécurité

**Titre** : "Quiz Cybersécurité"
**Instructions** : "Testez vos connaissances en sécurité informatique"
**Code secret** : "SECURE42"

**JSON** :
```json
{
  "questions": [
    {
      "question": "Qu'est-ce qu'un mot de passe fort ?",
      "answers": [
        "123456",
        "Un mélange de lettres, chiffres et symboles",
        "Votre date de naissance",
        "Le nom de votre animal"
      ],
      "correct": 1
    },
    {
      "question": "Que signifie 'phishing' ?",
      "answers": [
        "Pêche en ligne",
        "Technique de piratage par email frauduleux",
        "Un type de virus",
        "Un pare-feu"
      ],
      "correct": 1
    },
    {
      "question": "Comment protéger ses données personnelles ?",
      "answers": [
        "Les partager sur les réseaux sociaux",
        "Utiliser le même mot de passe partout",
        "Activer l'authentification à deux facteurs",
        "Les envoyer par email"
      ],
      "correct": 2
    }
  ]
}
```

---

## Conseils pédagogiques

### Pour créer une bonne énigme :

1. **Définissez un objectif clair** : Quelle compétence ou connaissance voulez-vous évaluer ?

2. **Adaptez la difficulté** : 
   - Énigmes 1-2 : Plus faciles, pour mettre en confiance
   - Énigmes 3-4 : Difficulté moyenne
   - Énigmes 5+ : Plus complexes

3. **Variez les types** : Alternez les types d'énigmes pour maintenir l'intérêt

4. **Instructions claires** : Les élèves doivent comprendre immédiatement ce qui est attendu

5. **Testez avant** : Jouez l'énigme vous-même pour vérifier la cohérence

### Erreurs à éviter :

- ❌ Questions ambiguës avec plusieurs réponses possibles
- ❌ JSON mal formaté (utilisez un validateur JSON en ligne)
- ❌ Chemins d'images incorrects
- ❌ Ordre trop évident pour les timelines
- ❌ Énigmes trop longues (max 5-10 minutes par énigme)

---

## Dépannage

### L'énigme ne s'affiche pas correctement

- Vérifiez que le JSON est valide (utilisez jsonlint.com)
- Vérifiez que tous les champs requis sont présents
- Regardez la console du navigateur pour les erreurs

### Les images ne s'affichent pas

- Vérifiez que les fichiers existent dans `public/images/enigmas/`
- Vérifiez l'orthographe des noms de fichiers
- Vérifiez les droits d'accès aux fichiers

### La validation ne fonctionne pas

- Vérifiez que les index `correct` sont corrects (commencent à 0)
- Pour les timelines, vérifiez que les `id` sont bien en ordre croissant
- Testez avec des valeurs simples d'abord

---

## Support

Pour toute question ou problème, consultez la documentation technique ou contactez l'administrateur du système.
