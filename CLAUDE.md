# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Camagru is a web application for photo editing using webcam captures and predefined superposable images. Users can create, share, like, and comment on images in a public gallery.

## Core Development Rules

### File Structure and Imports

-   **ALWAYS** import header.php and/or footer.php in every PHP page
-   **NEVER** import Tailwind CSS or DaisyUI CDN links
-   The header handles all HTML structure and CSS imports via style.css
-   Create PHP variables at the top of files when needed

### Code Organization

-   **NEVER** write monolithic code - always separate into multiple small, clean files
-   Follow **MVC architecture**: Models in `/models/`, Controllers in `/controllers/`, Views as PHP pages
-   Keep logic separated from design as much as possible

### Project Requirements (from subject.md)

-   **ALWAYS** follow the requirements in @subject.md
-   When the subject provides choices, **ALWAYS** ask the user what they prefer
-   Security is mandatory: password hashing, XSS prevention, SQL injection protection
-   No external frameworks except CSS frameworks (without JS)
-   Use only PHP standard library equivalents
-   Server-side image processing is required

### Current Authentication System

-   Uses fieldset components from DaisyUI (via local style.css)
-   Email verification system with tokens
-   Password complexity requirements
-   CSRF protection through middleware
-   Session management

### Database

-   PostgreSQL with PDO
-   Schema includes users table with auth fields
-   All passwords must be hashed, never stored in plain text

### Security Checklist

-   Password hashing required
-   XSS prevention on all user inputs
-   SQL injection protection (use prepared statements)
-   CSRF token validation
-   File upload validation
-   Environment variables in .env (never commit credentials)

### Browser Compatibility

-   Must work on Firefox (>= 41) and Chrome (>= 46)
-   No console errors or warnings allowed
-   Mobile-responsive design required

### Image Processing Rules

-   All image superposition must be done **server-side**
-   Users can upload images instead of webcam capture
-   Users can only delete their own images
-   All captured images are public by default

### Email System

-   Email verification for registration
-   Password reset functionality
-   Comment notifications (enabled by default, can be disabled in preferences)

When in doubt about requirements or implementation choices, refer to @subject.md and ask the user for preferences when multiple options are available.

## Alert System

-   **TOUJOURS** utiliser le système `FormService` + `includes/alerts.php` pour gérer les erreurs et messages de succès
-   **JAMAIS** utiliser l'ancien système `$errors`/`$success`
-   **TOUJOURS** créer des Handlers dans `/handlers/` pour la logique métier
-   **TOUJOURS** utiliser `FormService::create()` pour initialiser la gestion des erreurs

## Architecture with Handlers

-   **TOUJOURS** séparer la logique métier dans des Handlers (ex: `ProfileHandler`, `LoginHandler`)
-   Les pages PHP doivent contenir uniquement l'initialisation du handler et le HTML
-   **TOUJOURS** suivre cette structure :
    ```php
    <?php
    require_once 'handlers/[Name]Handler.php';
    $handler = new [Name]Handler();
    $formService = $handler->getFormService();
    ?>
    <!-- HTML here -->
    <?php require_once 'includes/alerts.php'; ?>
    ```

## Fieldset Components

-   **TOUJOURS** utiliser les composants dans `components/Fieldset.php` pour tous les formulaires
-   **JAMAIS** écrire de HTML de formulaire brut, utiliser les méthodes statiques
-   Les composants Fieldset gèrent automatiquement l'affichage des erreurs via FormService

## JavaScript Best Practices

-   **TOUJOURS** séparer le JavaScript dans des fichiers `.js` dédiés dans `/assets/js/`
-   **JAMAIS** mélanger PHP et JavaScript dans le même fichier
-   **TOUJOURS** créer des classes JavaScript pour organiser le code (pas de fonctions globales)
-   **TOUJOURS** utiliser `addEventListener` au lieu de `onclick` inline
-   **JAMAIS** utiliser `document.createElement()` - préférer les templates HTML en string avec `innerHTML`
-   **TOUJOURS** utiliser des `data-attributes` pour les interactions (ex: `data-action="delete"`)
-   **TOUJOURS** initialiser les composants avec `DOMContentLoaded`
-   **JAMAIS** utiliser de variables globales sauf si nécessaire pour l'interop entre composants
-   **TOUJOURS** séparer les responsabilités : rendering, events, storage, business logic
-   Les composants PHP doivent uniquement inclure les fichiers JS : `<script src="assets/js/component.js"></script>`

# Rules

-   tout les input dans des form doivent utilise fieldset
-   les text en muted doivent etre en base-content/50 (pas 60 70 ou autre)
-   attemtion dans le projet jai /express (old) et /php (new refactor) fais tout dans php, quand tu lance le docker compose lance bien le bon !
-   les card doivent etre en bgbase-200 et pas de shadow
-   pas de commentaires
-   Utilise tjr les rounded box ou rounded de daisy ui pas de rounded-lg ou md par exemple
-   Met jamais de commentaire dans le code
