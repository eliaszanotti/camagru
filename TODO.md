Ajouter une option "renvoyer le mail de vérification" sur la page checkEmail

Ajouter un refresh token parce que le jwt expire au bout d'1 heure

rename profil -> profile

Pour DEMAIN :
DRY le script module de password confirmation dans changePassword.ejs et resetPassword.ejs
DRY les fieldsets dans changePassword.ejs, changeUsername.ejs et resetPassword.ejs...

Dans publish au lieu de faire un truc comme on a en reactif dynamic quand on a upload le fichier ou pris depuis la camera, on redirige vers une page preview qui affiche le post et apres seulement on publie

When an image receives a new comment, the author of the image should be notified
by email. This preference must be set as true by default but can be deactivated in
user’s preferences.

attention commentaire vide provoque une erreur

fais une pagination tu te cassera moins la tete avec la lenteur des fetch

voir daisyui pour mettre des chat bubbles dans les commentaires

Avec sharp avant de save limage on la rogne en carrée de 1000x1000 (ou autre plus grand si on peut upscale)

on ajoute un champs origalImage pour retablir l'image originale ou alors pour pas ajouter trop d'emoji
