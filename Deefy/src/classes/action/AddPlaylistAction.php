<?php

namespace src\classes\action;

use src\classes\audio\lists\Playlists;
use src\classes\render\AudioListRenderer;
use src\classes\repository\DeefyRepository;

class AddPlaylistAction extends Action
{
    public function execute(): string
    {
        $html = "<!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Deefy</title>
            <link rel='stylesheet' href='src/styles/creerplaylistaction.css'> 
        </head>
        <body>";

        // Gestion de la requête GET : afficher le formulaire de création de playlist
        if ($this->http_method === 'GET') {
            $html .= "<h1>Créer une playlist</h1>
            <form action='?action=add-playlist' method='post'>
                <label for='playlist-name'>Nom de la playlist</label>
                <input type='text' id='playlist-name' name='playlist-name' required>
                <input type='submit' value='Créer'>
            </form>";
        }

        // Gestion de la requête POST : création de la playlist
        elseif ($this->http_method === 'POST') {
            session_start();
            $playlist_name = filter_var($_POST['playlist-name'], FILTER_SANITIZE_STRING);

            if (empty($playlist_name)) {
                $html .= "<p>Le nom de la playlist est vide</p>";
            } else {
                // Instancier une nouvelle playlist et la sauvegarder en session
                $playlist = new Playlists($playlist_name);
                $_SESSION['playlist'] = $playlist;

                // Sauvegarder la playlist en base de données
                DeefyRepository::getInstance()->saveEmptyPlaylist($playlist);

                // Afficher un message de succès
                $html .= "<p>Playlist '$playlist_name' créée avec succès</p>";

                // Rendre la playlist avec un AudioListRenderer
                $renderer = new AudioListRenderer($playlist);
                $html .= $renderer->render(1);

                // Lien pour ajouter une piste
                $html .= "<a href='?action=add-track' class='action-link'>Ajouter une piste</a>";
            }
        }

        $html .= "</body></html>";
        return $html;
    }
}
