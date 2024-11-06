<?php

namespace src\classes\action;

use src\classes\render\AudioListRenderer;
use src\classes\repository\DeefyRepository;
use src\classes\auth\Authz;

class DisplayPlaylistAction extends Action {

    public function execute(): string {
        session_start(); // Assurez-vous que la session est toujours démarrée

        // Début du contenu HTML avec inclusion du fichier CSS
        $html = "<!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Deefy - Playlist</title>
            <link rel='stylesheet' href='src/styles/displayplaylistaction.css'>
        </head>
        <body>";

        // Récupérer l'ID de la playlist depuis les paramètres GET
        $playlistId = isset($_GET['id']) ? intval($_GET['id']) : null;



        if ($playlistId !== null) {
            if (Authz::checkPlaylistOwner($playlistId)) {
                $playlist = DeefyRepository::getInstance()->findPlaylistById($playlistId);

                if ($playlist) {
                    // Stocker la playlist courante en session
                    $_SESSION['current_playlist'] = serialize($playlist);

                    // Récupérer les pistes de la playlist et mettre à jour l'objet playlist
                    $tracks = DeefyRepository::getInstance()->getTracksForPlaylist($playlist);
                    $playlist->setTracks($tracks);

                    // Rendu de la liste audio avec les pistes
                    $renderer = new AudioListRenderer($playlist);
                    $html .= $renderer->render(2);
                } else {
                    $html .= "<p>Playlist introuvable pour l'ID spécifié.</p>";
                }
            } else {
                $html .= "<p>Vous n'êtes pas autorisé à accéder à cette playlist.</p>";
            }
        } else {
            $html .= "<p>Aucun ID de playlist spécifié.</p>";
        }

        // Lien pour ajouter une piste et retour aux playlists
        $html .= "<div class='actions'>
                    <a href='?action=add-track' class='action-link'>Ajouter une piste</a>
                    <a href='?action=mes-playlists' class='action-link'>Retour à mes playlists</a>
                  </div>";

        $html .= "</body></html>";
        return $html;
    }
}
