<?php

namespace src\classes\render;

use src\classes\audio\lists\AudioList;
use src\classes\audio\tracks\PodcastTrack;
use src\classes\audio\tracks\AlbumTrack;

class AudioListRenderer implements Renderer {

    protected AudioList $playlist;

    public function __construct(AudioList $playlist) {
        $this->playlist = $playlist;
    }

    public function render(int $mode): string {
        // Début du contenu HTML avec inclusion du fichier CSS
        $html = "<!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Deefy - {$this->playlist->__get('nom')}</title>
            <link rel='stylesheet' href='src/styles/audiolistrenderer.css'> 
        </head>
        <body>";

        // Titre de la playlist
        $html .= "<h1>{$this->playlist->__get('nom')}</h1>";

        // Vérifier si la liste des pistes est vide
        $tracks = $this->playlist->__get('tracks');
        if (empty($tracks)) {
            $html .= "<p>Aucune piste disponible.</p>";
        } else {
            $html .= "<ul class='track-list'>";
            foreach ($tracks as $track) {
                $audioFile = htmlspecialchars($track->__get('filename'));
                $titre = htmlspecialchars($track->__get('titre'));

                $html .= "<li class='track-item'>";
                if ($track instanceof PodcastTrack) {
                    $auteur = htmlspecialchars($track->__get('auteur'));
                    $date = $track->__get('date') ? htmlspecialchars($track->__get('date')) : "Date : Inconnue";

                    $html .= "<span class='track-title'>{$titre}</span> - <span class='track-author'>{$auteur}</span> - <span class='track-date'>{$date}</span>";
                } elseif ($track instanceof AlbumTrack) {
                    $artiste = htmlspecialchars($track->__get('artiste'));
                    $album = $track->__get('album') ? htmlspecialchars($track->__get('album')) : "Album : Inconnu";
                    $annee = $track->__get('annee') ? htmlspecialchars($track->__get('annee')) : "Annee : Inconnue";
                    $numero = $track->__get('numero_piste') ? htmlspecialchars($track->__get('numero_piste')) : "Numéro de piste : Inconnu";

                    $html .= "<span class='track-title'>{$titre}</span> - <span class='track-artist'>{$artiste}</span> - <span class='track-album'>{$album}</span> - <span class='track-year'>{$annee}</span> - <span class='track-number'>{$numero}</span>";
                } else {
                    $duree = htmlspecialchars($track->__get('duree'));
                    $html .= "<span class='track-title'>{$titre}</span> - <span class='track-duration'>Durée : {$duree} secondes</span>";
                }

                $html .= "<br><audio controls class='audio-player'>
                            <source src='../Deefy/audio/{$audioFile}' type='audio/mpeg'>
                            Votre navigateur ne supporte pas l'élément audio.
                          </audio>";
                $html .= "</li>";
            }
            $html .= "</ul>";
        }

        $html .= "</body></html>";
        return $html;
    }
}
