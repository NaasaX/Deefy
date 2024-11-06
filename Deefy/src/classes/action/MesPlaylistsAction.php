<?php

namespace src\classes\action;

use src\classes\repository\DeefyRepository;
use src\classes\auth\AuthProvider;
use src\classes\exception\AuthException;

class MesPlaylistsAction extends Action {
    public function execute(): string {
        session_start();

        // Générer le HTML de base pour la page
        $html = "<!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Mes Playlists</title>
            <link rel='stylesheet' href='src/styles/mesplaylistsaction.css'> 
        </head>
        <body>";

        // Vérifier si l'utilisateur est connecté
        try {
            $user = AuthProvider::getSignedInUser();
        } catch (AuthException $e) {
            $html .= "<p class='error-message'>Vous devez être connecté pour voir vos playlists.</p>
                      <p><a href='?action=login'>Cliquez ici pour vous connecter</a></p>";
            $html .= "</body></html>";
            return $html;
        }

        // Récupérer l'ID de l'utilisateur connecté
        $userId = $user->getId();

        // Vérifier si l'utilisateur est un administrateur
        $isAdmin = $user->getRole() == 100;

        // Si l'utilisateur est un administrateur, récupérer toutes les playlists
        if ($isAdmin) {
            $playlists = DeefyRepository::getPlaylists();
        } else {
            // Sinon, récupérer seulement les playlists de l'utilisateur connecté
            $playlists = DeefyRepository::getUserPlaylists($userId);
        }

        // Si aucune playlist n'est trouvée, afficher un message d'information
        if (empty($playlists)) {
            $html .= "<p class='error-message'>Aucune playlist trouvée.</p>";
        } else {
            // Afficher la liste des playlists
            $html .= "<h2>Playlists</h2><ul>";
            foreach ($playlists as $playlist) {
                $html .= "<li><a href='?action=display-playlist&id=" . $playlist->getId() . "'>" . htmlspecialchars($playlist->getNom()) . "</a></li>";
            }
            $html .= "</ul>";
        }

        $html .= "</body></html>";

        return $html;
    }
}

