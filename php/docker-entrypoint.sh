#!/bin/bash

set -e

if [ -n "$SMTP_HOST" ] && [ -n "$SMTP_USER" ] && [ -n "$SMTP_PASS" ]; then
    echo "Configuring ssmtp..."

    cat > /etc/ssmtp/ssmtp.conf <<EOF
root=${SMTP_FROM_EMAIL:-no-reply@camagru.com}
mailhub=${SMTP_HOST}:${SMTP_PORT:-587}
AuthUser=${SMTP_USER}
AuthPass=${SMTP_PASS}
UseSTARTTLS=YES
FromLineOverride=YES
EOF

    cat > /etc/ssmtp/revaliases <<EOF
www-data:${SMTP_FROM_EMAIL:-no-reply@camagru.com}:${SMTP_HOST}:${SMTP_PORT:-587}
root:${SMTP_FROM_EMAIL:-no-reply@camagru.com}:${SMTP_HOST}:${SMTP_PORT:-587}
EOF

    echo "ssmtp configured with host: $SMTP_HOST"
fi

exec "$@"
