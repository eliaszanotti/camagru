<?php
require_once 'services/EmailService.php';

$emailService = new EmailService();

$to = 'zanotti.elias@gmail.com';
$from = 'zanotti.elias@gmail.com';
$subject = 'Test Camagru - Email Gmail vers Gmail';

// Simple email texte brut
$message = "
<html>
<body>
    <h2>Test d'email Gmail vers Gmail</h2>
    <p>Bonjour,</p>
    <p>Ceci est un email de test envoyé depuis Camagru.</p>
    <p>De: $from<br>Vers: $to</p>
    <p>Si tu reçois cet email, la configuration SMTP fonctionne !</p>
    <br>
    <p>Cordialement,<br>Elias</p>
</body>
</html>";

echo "Envoi d'un email de test vers: $to\n";
echo "Depuis: $from\n\n";

// Envoi avec la fonction mail directement pour tester
$headers = [
    "From: Test <$from>",
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'X-Mailer: PHP/' . phpversion()
];

$result = mail($to, $subject, $message, implode("\r\n", $headers));

if ($result) {
    echo "✅ Email envoyé avec succès !\n";
    echo "Vérifie ta boîte mail (y compris les spams)\n";
} else {
    echo "❌ Erreur lors de l'envoi\n";
    echo "Erreur: " . error_get_last()['message'] . "\n";
}

// Affiche la configuration
echo "\nConfiguration SMTP:\n";
echo "SMTP: " . ini_get('SMTP') . "\n";
echo "Port: " . ini_get('smtp_port') . "\n";
echo "Sendmail: " . ini_get('sendmail_path') . "\n";
?>