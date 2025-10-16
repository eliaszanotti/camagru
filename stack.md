# Camagru - Tech Stack

## Backend (Obligatoire PHP natif)
- **PHP 8+** (obligatoire selon les contraintes du sujet)
- **MySQL** ou **PostgreSQL** ✅ (PostgreSQL est autorisé)
- **PDO** (pour les requêtes SQL sécurisées)
- **PHP GD** (pour le traitement d'images côté serveur)
- **PHP Mail** (pour l'envoi d'emails de confirmation)
- **Sessions PHP** (pour l'authentification)

## Frontend (Moderne avec contraintes)
- **HTML5** (structure sémantique)
- **Tailwind CSS** ✅ (CSS framework moderne autorisé)
- **DaisyUI** ✅ (composants Tailwind - pas de JS additionnel)
- **JavaScript vanilla** ✅ (ES6+ est parfaitement autorisé)
- **Fetch API** (pour les appels AJAX modernes)
- **WebRTC getUserMedia** (accès webcam)
- **Canvas API** (manipulation d'images)
- **NO TypeScript** ❌ (pas de transpilation, JavaScript vanilla uniquement)

## Base de données
- **PostgreSQL** ✅ (excellent choix, plus sécurisé que MySQL)
- **Tables requises** :
  - `users` (id, username, email, password_hash, email_verified, created_at)
  - `images` (id, user_id, image_path, created_at)
  - `likes` (id, user_id, image_id, created_at)
  - `comments` (id, user_id, image_id, content, created_at)
  - `superposable_images` (id, name, path, created_at)

## Containerisation (Obligatoire)
- **Docker** + **Docker Compose**
- **Multi-containers** :
  - Container PHP (Apache/Nginx + PHP)
  - Container PostgreSQL
  - Container Node.js (pour build Tailwind uniquement)

## Structure du projet
```
camagru/
├── config/
│   ├── database.php          # Configuration BDD
│   ├── .env                  # Variables d'environnement
│   └── constants.php         # Constantes de l'application
├── models/
│   ├── User.php              # Classe User (CRUD)
│   ├── Image.php             # Classe Image (CRUD)
│   ├── Comment.php           # Classe Comment (CRUD)
│   └── Like.php              # Classe Like (CRUD)
├── controllers/
│   ├── AuthController.php    # Register/Login/Logout
│   ├── GalleryController.php # Display images/pagination
│   ├── EditController.php    # Image processing
│   └── UserController.php    # Profile management
├── views/
│   ├── layouts/
│   │   ├── header.php
│   │   ├── footer.php
│   │   └── main.php
│   ├── auth/
│   │   ├── login.php
│   │   ├── register.php
│   │   └── reset-password.php
│   ├── gallery/
│   │   ├── index.php
│   │   └── image.php
│   ├── edit/
│   │   └── editor.php
│   └── profile/
│       └── settings.php
├── assets/
│   ├── css/
│   │   ├── input.css         # Fichier Tailwind principal
│   │   └── style.css         # Fichier compilé
│   ├── js/
│   │   ├── auth.js           # Gestion authentification
│   │   ├── gallery.js        # Galerie infinie/likes
│   │   ├── camera.js         # Webcam + capture
│   │   └── main.js           # Utilitaires généraux
│   └── images/
│       ├── uploads/          # Images uploadées
│       ├── processed/        # Images traitées
│       └── overlays/         # Images superposables
├── vendor/                   # Dépendances PHP (si composer utilisé)
├── docker-compose.yml        # Configuration Docker
├── Dockerfile               # Image PHP
├── package.json             # Dépendances frontend
├── tailwind.config.js       # Config Tailwind
├── .gitignore
└── index.php                # Router principal
```

## Configuration Tailwind + DaisyUI
```javascript
// tailwind.config.js
module.exports = {
  content: [
    "./views/**/*.php",
    "./assets/js/**/*.js"
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('daisyui'),
    require('@tailwindcss/typography')
  ],
  daisyui: {
    themes: [
      "light",
      "dark",
      "cupcake",
      "bumblebee",
      "emerald",
      "corporate",
      "synthwave"
    ],
  },
}
```

## Sécurité (Obligatoire)
- **Password hashing** : `password_hash()` + `password_verify()`
- **XSS Prevention** : `htmlspecialchars()` sur tous les outputs
- **SQL Injection** : Requêtes préparées PDO
- **CSRF Protection** : Tokens de session
- **File Upload Security** : Validation MIME types, tailles max
- **Email Validation** : Filtrage et validation regex
- **Rate Limiting** : Protection brute force

## Outils de développement
- **Docker Desktop** (containerisation locale)
- **PostgreSQL Client** (pgAdmin ou DBeaver)
- **VS Code** (avec extensions PHP, Tailwind, Docker)
- **Postman** (testing API endpoints)

## Notes importantes
- **JavaScript vanilla ES6+** est parfaitement autorisé
- **Pas de transpilation TypeScript/JSX**
- **Tailwind + DaisyUI** pour un design moderne sans framework JS
- **PostgreSQL** pour une base de données robuste
- **PHP natif uniquement** (pas Laravel, Symfony, etc.)