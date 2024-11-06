<?php

namespace src\classes\action;

use src\classes\auth\User;

class DefaultAction extends Action
{
    public function execute(): string
    {
        // Démarrer la session
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifier si l'utilisateur est connecté
        $user = $_SESSION['user'] ?? null;
        $userDisplay = '';

        if ($user) {
            $user = unserialize($user);
            if ($user instanceof User) {
                $userDisplay = "<div class='user-info'>Bonjour, " . htmlspecialchars($user->getEmail()) . "</div>";
            }
        }

        // Générer le contenu HTML
        $html = "<!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Deefy</title>
            <link rel='stylesheet' href='src/styles/defaultaction.css'>
        </head>
        <body>
            $userDisplay
            <h1><a href='?action=default'>Deefy</a></h1>
            <nav>
                <a href='?action=add-playlist'>Créer une playlist</a>
                <a href='?action=mes-playlists'>Mes playlists</a>
                <a href='?action=login'>Se connecter</a>
                <a href='?action=register'>S'inscrire</a>
                <a href='?action=logout'>Se déconnecter</a>
            </nav>
        </body>
        </html>";

        return $html;
    }
}
