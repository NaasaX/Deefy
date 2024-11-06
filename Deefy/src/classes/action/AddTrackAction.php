<?php

namespace src\classes\action;

use src\classes\audio\tracks\AlbumTrack;
use src\classes\audio\tracks\PodcastTrack;
use src\classes\repository\DeefyRepository;

class AddTrackAction extends Action {

    public function execute(): string {
        $html = "<!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Deefy - Ajouter une piste</title>
            <link rel='stylesheet' href='src/styles/addtrackaction.css'> 
        </head>
        <body>";

        // Affichage du formulaire d'ajout de piste en mode GET
        if ($this->http_method === 'GET') {
            $html .= "<h1>Ajouter une piste</h1>
            <form action='?action=add-track' method='post' enctype='multipart/form-data'>
                <label for='track-name'>Nom de la piste</label>
                <input type='text' id='track-name' name='track-name' required>

                <label for='track-file'>Fichier audio</label>
                <input type='file' id='track-file' name='track-file' required>

                <label for='track-duration'>Durée (en secondes)</label>
                <input type='number' id='track-duration' name='track-duration' required>

                <label for='track-author'>Auteur</label>
                <input type='text' id='track-author' name='track-author' required>

                <label for='track-genre'>Genre</label>
                <input type='text' id='track-genre' name='track-genre' required>

                <label for='track-type'>Type de piste</label>
                <select id='track-type' name='track-type' required>
                    <option value='podcast'>Podcast</option>
                    <option value='album'>Album</option>
                </select>

                <input type='submit' value='Ajouter'>
            </form>";
        }

        // Gestion du formulaire en mode POST : ajout de la piste
        // Gestion du formulaire en mode POST : ajout de la piste
        if ($this->http_method === 'POST') {
            session_start();

            // Récupération des données utilisateur
            $track_name = filter_var($_POST['track-name'], FILTER_SANITIZE_STRING);
            $track_duration = filter_var($_POST['track-duration'], FILTER_SANITIZE_NUMBER_INT);
            $track_author = filter_var($_POST['track-author'], FILTER_SANITIZE_STRING);
            $track_genre = filter_var($_POST['track-genre'], FILTER_SANITIZE_STRING);
            $track_type = $_POST['track-type']; // 'podcast' ou 'album'
            $playlist = isset($_SESSION['current_playlist']) ? unserialize($_SESSION['current_playlist']) : null;

            // Gestion du fichier audio
            $targetDir = __DIR__ . '/../../../audio/';
            $track_file = basename($_FILES['track-file']['name']);
            $targetFile = $targetDir . $track_file;

            if (move_uploaded_file($_FILES['track-file']['tmp_name'], $targetFile)) {
                // Création de la piste en fonction du type
                $trackId = null;
                if ($track_type === 'podcast') {
                    $podcast = new PodcastTrack($track_name, $track_file, $track_duration);
                    $podcast->__set('auteur', $track_author);
                    $podcast->__set('genre', $track_genre);
                    $podcast->__set('date', date('Y-m-d'));

                    $trackId = DeefyRepository::getInstance()->saveTrack($podcast);
                } elseif ($track_type === 'album') {
                    $albumTrack = new AlbumTrack($track_name, $track_file, $track_duration);
                    $albumTrack->__set('artiste', $track_author);
                    $albumTrack->__set('genre', $track_genre);

                    $trackId = DeefyRepository::getInstance()->saveTrack($albumTrack);
                }

                // Vérification et ajout de la piste à la playlist
                if ($playlist && $playlist->getId() !== null) {
                        DeefyRepository::getInstance()->addTrackToPlaylist($trackId, $playlist->getId());
                        $_SESSION["current_playlist"] = serialize($playlist);
                        $html .= "<p>Piste <strong>$track_name</strong> ajoutée avec succès à votre playlist <strong>{$playlist->getNom()}</strong>.</p>";
                        $html .= "<a href='?action=display-playlist&id={$playlist->getId()}'>Retour à la playlist</a>";

                } else {
                    $html .= "<p>ID de la playlist invalide.</p>";
                }
            } else {
                $html .= "<p>Erreur lors de l'upload du fichier audio.</p>";
            }
        }


        $html .= "</body></html>";
        return $html;
    }
}
