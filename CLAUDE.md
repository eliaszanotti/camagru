# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Camagru is a web application for photo editing using webcam captures and predefined superposable images. Users can create, share, like, and comment on images in a public gallery.

## Core Development Rules

### File Structure and Imports
- **ALWAYS** import header.php and/or footer.php in every PHP page
- **NEVER** import Tailwind CSS or DaisyUI CDN links
- The header handles all HTML structure and CSS imports via style.css
- Create PHP variables at the top of files when needed

### Code Organization
- **NEVER** write monolithic code - always separate into multiple small, clean files
- Follow **MVC architecture**: Models in `/models/`, Controllers in `/controllers/`, Views as PHP pages
- Keep logic separated from design as much as possible

### Project Requirements (from subject.md)
- **ALWAYS** follow the requirements in @subject.md
- When the subject provides choices, **ALWAYS** ask the user what they prefer
- Security is mandatory: password hashing, XSS prevention, SQL injection protection
- No external frameworks except CSS frameworks (without JS)
- Use only PHP standard library equivalents
- Server-side image processing is required

### Current Authentication System
- Uses fieldset components from DaisyUI (via local style.css)
- Email verification system with tokens
- Password complexity requirements
- CSRF protection through middleware
- Session management

### Database
- PostgreSQL with PDO
- Schema includes users table with auth fields
- All passwords must be hashed, never stored in plain text

### Security Checklist
- Password hashing required
- XSS prevention on all user inputs
- SQL injection protection (use prepared statements)
- CSRF token validation
- File upload validation
- Environment variables in .env (never commit credentials)

### Browser Compatibility
- Must work on Firefox (>= 41) and Chrome (>= 46)
- No console errors or warnings allowed
- Mobile-responsive design required

### Image Processing Rules
- All image superposition must be done **server-side**
- Users can upload images instead of webcam capture
- Users can only delete their own images
- All captured images are public by default

### Email System
- Email verification for registration
- Password reset functionality
- Comment notifications (enabled by default, can be disabled in preferences)

When in doubt about requirements or implementation choices, refer to @subject.md and ask the user for preferences when multiple options are available.

# Rules

- tout les input dans des form doivent utilise fieldset
- les text en muted doivent etre en base-content/50 (pas 60 70 ou autre)