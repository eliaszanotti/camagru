<?php
require_once 'services/EmailService.php';

$to = 'zanotti.elias@gmail.com';
$subject = 'Test Camagru Email';
$message = '<h1>Test Email</h1><p>Si tu reçois cet email, ssmtp fonctionne !</p>';

$emailService = new EmailService();

echo "Envoi vers: $to\n";
echo "Depuis: " . $emailService->getFromEmail() . "\n\n";

$result = $emailService->sendTestEmail($to, $subject, $message);

if ($result) {
    echo "✅ Email envoyé !\n";
} else {
    echo "❌ Erreur\n";
}
