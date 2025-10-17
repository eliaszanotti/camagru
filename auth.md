# Camagru Authentication System

## Architecture Overview

This document describes the authentication system for Camagru, implementing a simple MVC approach with PHP sessions.

## Requirements (from subject.md)

- User registration with email, username, and password (complexity requirements)
- Email verification for account activation
- Login/logout functionality
- Password reset via email
- Profile editing (username, email, password)
- All forms must be secure (no XSS, SQL injection, etc.)
- Passwords must be hashed (no plain text)

## Database Schema

### Users Table Updates

```sql
ALTER TABLE users ADD COLUMN email_verification_token VARCHAR(255) NULL;
ALTER TABLE users ADD COLUMN password_reset_token VARCHAR(255) NULL;
ALTER TABLE users ADD COLUMN password_reset_expires TIMESTAMP NULL;
```

### Fields
- `id` - Primary key
- `username` - Unique username
- `email` - Unique email address
- `password_hash` - Hashed password (NEVER plain text)
- `is_verified` - Boolean for email verification status
- `email_verification_token` - Token for email verification
- `password_reset_token` - Token for password reset
- `password_reset_expires` - Expiration time for reset token
- `created_at` - Account creation timestamp
- `updated_at` - Last update timestamp

## Security Measures

### Password Security
- Use `password_hash()` with `PASSWORD_DEFAULT`
- Minimum 8 characters
- Must contain: uppercase, lowercase, number, special character

### Session Security
- Regenerate session ID on login
- Set secure session parameters
- Session timeout after inactivity

### Form Security
- CSRF tokens on all forms
- Input sanitization with `htmlspecialchars()`
- SQL injection prevention with prepared statements
- Rate limiting on login attempts

### Email Security
- Tokens expire after 24 hours (verification) or 1 hour (reset)
- Unique tokens generated with `random_bytes()`
- Tokens invalidated after use

## File Structure

```
php/
├── auth.md                    # This documentation
├── database/schema.sql        # Database schema
├── services/EmailService.php  # Email handling
├── middleware/AuthMiddleware.php # Session protection
├── register.php               # Registration page
├── login.php                  # Login page
├── logout.php                 # Logout handler
├── verify.php                 # Email verification
├── forgot-password.php        # Password reset request
├── reset-password.php         # Password reset form
├── profile.php                # User profile/editing
└── includes/header.php        # Navigation updates
```

## User Flows

### Registration Flow
1. User submits registration form
2. Validate input (username, email, password complexity)
3. Create user account with `is_verified = false`
4. Generate verification token
5. Send verification email
6. Display "check your email" message
7. User clicks verification link
8. Verify token, set `is_verified = true`
9. Redirect to login

### Login Flow
1. User submits login form
2. Check if user exists and is verified
3. Verify password hash
4. Create secure session
5. Regenerate session ID
6. Redirect to protected area

### Password Reset Flow
1. User requests password reset
2. Verify email exists
3. Generate reset token with 1-hour expiration
4. Send reset email
5. User clicks reset link
6. Verify token and expiration
7. Allow password reset
8. Clear reset token

### Profile Update Flow
1. User must be logged in
2. Display current user information
3. Allow editing username, email, password
4. Validate all inputs
5. Update database
6. Handle email change (re-verify if needed)

## Email Templates

### Verification Email
Subject: "Verify your Camagru account"
```
Hello {username},

Please click the link below to verify your Camagru account:

{verification_url}

This link will expire in 24 hours.

If you didn't create this account, please ignore this email.
```

### Password Reset Email
Subject: "Reset your Camagru password"
```
Hello {username},

You requested to reset your password. Click the link below:

{reset_url}

This link will expire in 1 hour.

If you didn't request this reset, please ignore this email.
```

## Environment Variables

Required in `.env` file:
```
# SMTP Configuration
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_FROM=noreply@camagru.com

# Security
CSRF_SECRET=your-secret-key
SESSION_TIMEOUT=3600
```

## Error Handling

### User-Friendly Messages
- Registration: "Account created! Please check your email to verify."
- Login: "Invalid username/password or account not verified."
- Reset: "If the email exists, a reset link has been sent."
- Verification: "Account verified! You can now login."

### Error Logging
- Log all authentication failures
- Log email sending failures
- Log suspicious activity (multiple failed attempts)
- Use PHP's `error_log()` function

## Testing Checklist

- [ ] User can register with valid data
- [ ] Registration fails with invalid data
- [ ] Verification email is sent and works
- [ ] User cannot login without verification
- [ ] Login works with correct credentials
- [ ] Login fails with wrong credentials
- [ ] Password reset flow works end-to-end
- [ ] Profile updates work correctly
- [ ] Session management is secure
- [ ] CSRF protection works
- [ ] All forms are sanitized
- [ ] Navigation updates based on auth state

## Security Best Practices

1. **Never** store plain passwords
2. **Always** use prepared statements
3. **Always** sanitize user input
4. **Always** use HTTPS in production
5. **Always** set secure headers
6. **Never** trust client-side data
7. **Always** implement rate limiting
8. **Always** log security events